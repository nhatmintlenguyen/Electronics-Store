import requests
from bs4 import BeautifulSoup
import json
import time
import random
from pymongo import MongoClient, UpdateOne
from datetime import datetime
from playwright.sync_api import sync_playwright  
from concurrent.futures import ThreadPoolExecutor, as_completed
import os 
import dotenv

dotenv.load_dotenv()

# MongoDB Configuration
MONGO_URI = os.getenv("MONGODB_CONNECTION_STRING")
DATABASE_NAME = os.getenv("MONGODB_DATABASE")
COLLECTION_NAME = os.getenv("MONGODB_PRODUCTS_COLLECTION")

WORKERS = 5  # Number of concurrent workers (one worker handles one URL)
BATCH_SIZE = 10

# Network/Render Timeout Configuration
HTTP_CONNECT_TIMEOUT = 10
HTTP_READ_TIMEOUT = 30
PAGE_GOTO_TIMEOUT_MS = 90000
PAGE_NETWORK_IDLE_TIMEOUT_MS = 15000
PAGE_PRODUCT_WAIT_TIMEOUT_MS = 20000
SHOW_MORE_WAIT_TIMEOUT_MS = 6000

# Product Schema Definition
PRODUCT_SCHEMA = {
    'name': '',                    # Product name
    'url': '',                     # Product detail page URL
    'image': '',                   # Product image URL
    'price': 0,                    # Price as integer (VND)
    'price_display': '',           # Formatted price string (e.g., "10.000.000đ")
    'rating': 0.0,                 # Product rating (float)
    'technical_specs': {},         # Dictionary of technical specifications
    'product_container': '',       # HTML content of product detail container
    'source_url': '',              # Category page URL where product was found
    'category': '',                # Product category (Smartphones, Laptops, etc.)
    'scraped_at': ''               # Timestamp when product was scraped (ISO format)
}

def create_empty_product():
    """Create a new product dictionary with default schema values."""
    return {
        'name': '',
        'url': '',
        'image': '',
        'price': 0,
        'price_display': '',
        'rating': 0.0,
        'technical_specs': {},
        'product_container': '',
        'source_url': '',
        'category': '',
        'scraped_at': ''
    }

# URL List from scraper.py
URL_LIST = [
    "https://cellphones.com.vn/mobile/apple.html", 
    "https://cellphones.com.vn/mobile/samsung.html", 
    "https://cellphones.com.vn/mobile/xiaomi.html",
    "https://cellphones.com.vn/mobile/oppo.html",
    "https://cellphones.com.vn/mobile/huawei.html", 

    "https://cellphones.com.vn/tablet/ipad.html", 
    "https://cellphones.com.vn/tablet/samsung.html",
    "https://cellphones.com.vn/tablet/xiaomi.html", 
    "https://cellphones.com.vn/tablet/huawei.html",
    "https://cellphones.com.vn/tablet/lenovo.html",
    "https://cellphones.com.vn/tablet/may-doc-sach.html", 
    
    "https://cellphones.com.vn/laptop/dell.html",
    "https://cellphones.com.vn/laptop/mac.html",
    "https://cellphones.com.vn/laptop/hp.html",
    "https://cellphones.com.vn/laptop/msi.html",
    "https://cellphones.com.vn/laptop/asus.html", 
    "https://cellphones.com.vn/laptop/lenovo.html",

    "https://cellphones.com.vn/thiet-bi-am-thanh/tai-nghe/apple.html", 
    "https://cellphones.com.vn/thiet-bi-am-thanh/tai-nghe/sony.html",

    "https://cellphones.com.vn/man-hinh/lg.html", 
    "https://cellphones.com.vn/man-hinh/xiaomi.html", 
    
    "https://cellphones.com.vn/phu-kien/the-nho-usb-otg/the-nho.html",
    "https://cellphones.com.vn/phu-kien/sac-dien-thoai/cap-dien-thoai.html",
    "https://cellphones.com.vn/phu-kien/sac-dien-thoai.html",  
]

def get_category_from_url(url):
    """Extract category name from URL."""
    if '/mobile/' in url:
        return 'Smartphones'
    elif '/laptop/' in url:
        return 'Laptops'
    elif '/tablet/' in url:
        return 'Tablets'
    elif '/thiet-bi-am-thanh/' in url or '/tai-nghe/' in url:
        return 'Audio'
    elif '/man-hinh/' in url:
        return 'Monitors'
    elif '/phu-kien/' in url:
        return 'Accessories'
    else:
        return 'Other'

def get_mongo_connection():
    """Create MongoDB connection and return collection."""
    try:
        client = MongoClient(MONGO_URI)
        db = client[DATABASE_NAME]
        collection = db[COLLECTION_NAME]
        print(f"✓ Connected to MongoDB: {DATABASE_NAME}.{COLLECTION_NAME}")
        return collection
    except Exception as e:
        print(f"✗ MongoDB connection error: {e}")
        return None

def fetch_page(url, max_retries=3, base_delay=1.0):
    """Fetch page content with retry/backoff to avoid failing when requests are too fast."""
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    }
    last_error = None

    for attempt in range(1, max_retries + 1):
        try:
            response = requests.get(
                url,
                headers=headers,
                timeout=(HTTP_CONNECT_TIMEOUT, HTTP_READ_TIMEOUT)
            )

            # Rate limit or temporary server issues -> retry
            if response.status_code in (429, 500, 502, 503, 504):
                retry_after = response.headers.get('Retry-After')
                if retry_after and retry_after.isdigit():
                    wait_time = float(retry_after)
                else:
                    # Exponential backoff + random jitter
                    wait_time = base_delay * (2 ** (attempt - 1)) + random.uniform(0.2, 0.8)

                if attempt < max_retries:
                    print(f"⚠ Request throttled/temporary error ({response.status_code}) for {url}. Retrying in {wait_time:.2f}s ({attempt}/{max_retries})...")
                    time.sleep(wait_time)
                    continue

            response.raise_for_status()
            return response.text

        except requests.exceptions.RequestException as e:
            last_error = e
            if attempt < max_retries:
                wait_time = base_delay * (2 ** (attempt - 1)) + random.uniform(0.2, 0.8)
                print(f"⚠ Error fetching {url}: {e}. Retrying in {wait_time:.2f}s ({attempt}/{max_retries})...")
                time.sleep(wait_time)
            else:
                print(f"✗ Error fetching {url} after {max_retries} attempts: {e}")

    if last_error:
        print(f"✗ Final fetch failure for {url}: {last_error}")
    return None


def fetch_page_with_load_more(url, target_products=20, max_clicks=10):
    """Fetch category page HTML with dynamic 'show more' clicking via headless browser."""
    try:
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page(viewport={"width": 1366, "height": 900})

            page.goto(url, wait_until='domcontentloaded', timeout=PAGE_GOTO_TIMEOUT_MS)
            try:
                page.wait_for_load_state('networkidle', timeout=PAGE_NETWORK_IDLE_TIMEOUT_MS)
            except Exception:
                pass

            # Wait for product grid to render on slower responses
            try:
                page.wait_for_selector(
                    'div.product-info-container.product-item',
                    timeout=PAGE_PRODUCT_WAIT_TIMEOUT_MS
                )
            except Exception:
                pass

            clicks = 0
            while clicks < max_clicks:
                current_count = len(page.query_selector_all('div.product-info-container.product-item'))
                if target_products > 0 and current_count >= target_products:
                    break

                show_more_btn = page.query_selector('a.button.btn-show-more.button__show-more-product')
                if not show_more_btn:
                    break

                show_more_btn.scroll_into_view_if_needed()
                show_more_btn.click(timeout=2000)
                clicks += 1

                # Wait until new products are rendered (or button disappears)
                try:
                    page.wait_for_function(
                        """(previousCount) => {
                            const count = document.querySelectorAll('div.product-info-container.product-item').length;
                            const hasShowMore = !!document.querySelector('a.button.btn-show-more.button__show-more-product');
                            return count > previousCount || !hasShowMore;
                        }""",
                        arg=current_count,
                        timeout=SHOW_MORE_WAIT_TIMEOUT_MS,
                    )
                except Exception:
                    # If waiting condition is not met quickly, continue without blocking
                    pass

            html = page.content()
            browser.close()

            print(f"  → Headless load-more clicks: {clicks}")
            return html
    except Exception as e:
        print(f"✗ Headless load-more failed for {url}: {e}")
        return None
    

def extract_technical_specs(product_url):
    """Extract technical specifications from product detail page."""
    print(f"  → Fetching specs from: {product_url}")
    
    content = fetch_page(product_url)
    if not content:
        return {}, ""
    
    soup = BeautifulSoup(content, 'html.parser')
    
    overview_specs = {}
    product_container = ""
    
    # Find technical specifications table
    tech_table = soup.find('table', class_='technical-content')
    
    if tech_table:
        rows = tech_table.find_all('tr', class_='technical-content-item')
        for row in rows:
            cells = row.find_all('td')
            if len(cells) >= 2:
                key = cells[0].get_text(strip=True)
                value = cells[1].get_text(strip=True)
                overview_specs[key] = value
    
    have_product_container = soup.find('div', class_='block-content-product')
    if have_product_container:
        product_container = str(have_product_container)  # Store the HTML content of the product container
    else: 
        product_container = ""

    return overview_specs, product_container


def scrape_product_from_item(item):
    """Extract product information from a product item div."""
    product_data = create_empty_product()
    
    try:
        # Find the link element
        link = item.find('a', class_='product__link')
        if not link:
            return None
        
        # Extract product URL
        product_url = link.get('href', '')
        if product_url and not product_url.startswith('http'):
            product_url = 'https://cellphones.com.vn' + product_url
        product_data['url'] = product_url
        
        # Extract product name
        name_elem = item.find('div', class_='product__name')
        if name_elem:
            h3 = name_elem.find('h3')
            product_data['name'] = h3.get_text(strip=True) if h3 else name_elem.get_text(strip=True)
        
        # Extract image
        img_elem = item.find('img', class_='product__img')
        if img_elem:
            product_data['image'] = img_elem.get('src', '')
        
        # Extract price
        price_elem = item.find('p', class_='product__price--show')
        if price_elem:
            price_text = price_elem.get_text(strip=True)
            # Clean price: remove 'đ', '.', and convert to number
            price_clean = price_text.replace('đ', '').replace('.', '').replace(',', '').strip()
            try:
                product_data['price'] = int(price_clean)
                product_data['price_display'] = price_text
            except:
                product_data['price'] = 0
                product_data['price_display'] = price_text
        
        # Extract rating if available
        rating_elem = item.find('div', class_='product__box-rating')
        if rating_elem:
            rating_text = rating_elem.get_text(strip=True)
            try:
                product_data['rating'] = float(rating_text)
            except:
                product_data['rating'] = 0.0
        
        # Fetch technical specifications from detail page
        if product_url:
            product_data['technical_specs'], product_data['product_container'] = extract_technical_specs(product_url)
        
        return product_data
        
    except Exception as e:
        print(f"  Error extracting product: {e}")
        return None

def scrape_products_from_url(url, max_products=10):
    """Scrape all products from a category page."""
    print(f"\n{'='*60}")
    print(f"Scraping: {url}")
    print('='*60)
    
    content = fetch_page(url)
    if not content:
        return []
    
    soup = BeautifulSoup(content, 'html.parser')

    # Detect dynamic "show more" button and use headless browser when needed
    has_show_more_button = soup.select_one('a.button.btn-show-more.button__show-more-product') is not None
    initial_count = len(soup.find_all('div', class_='product-info-container product-item'))

    # max_products <= 0 means scrape all available products
    # Also force headless fallback when static HTML returns zero products (slow/dynamic page render).
    should_use_headless = (
        initial_count == 0
        or (has_show_more_button and (max_products <= 0 or initial_count < max_products))
    )
    if should_use_headless:
        if initial_count == 0:
            print("Static HTML returned 0 products. Retrying with headless browser rendering...")
        else:
            print("Detected 'Xem thêm sản phẩm' button. Using headless browser to load more products...")
        target_products = max_products if max_products > 0 else 9999
        enhanced_content = fetch_page_with_load_more(url, target_products=target_products)
        if enhanced_content:
            soup = BeautifulSoup(enhanced_content, 'html.parser')

    products = []
    
    # Find all product containers with the specific class
    find_limit = None if max_products <= 0 else max_products
    product_containers = soup.find_all('div', class_='product-info-container product-item', limit=find_limit)
    
    print(f"Found {len(product_containers)} products")
    
    for idx, container in enumerate(product_containers, 1):
        print(f"\n[{idx}/{len(product_containers)}] Processing product...")
        
        product_data = scrape_product_from_item(container)
        
        if product_data:
            # Add metadata
            product_data['source_url'] = url
            product_data['category'] = get_category_from_url(url)
            product_data['scraped_at'] = datetime.now().isoformat()
            
            products.append(product_data)
    return products

def save_to_mongodb(products, collection):
    """Bulk upsert products to MongoDB by product URL."""
    if collection is None:
        print("✗ No MongoDB collection available")
        return 0
    
    if not products:
        print("✗ No products to save")
        return 0
    
    try:
        operations = []
        for product in products:
            product_url = product.get('url', '').strip()
            if not product_url:
                continue

            operations.append(
                UpdateOne(
                    {'url': product_url},
                    {'$set': product},
                    upsert=True
                )
            )

        if not operations:
            print("✗ No valid products with URL to upsert")
            return 0

        total_upserted = 0
        total_modified = 0

        # Write in chunks to avoid huge bulk payloads
        for i in range(0, len(operations), BATCH_SIZE):
            chunk = operations[i:i + BATCH_SIZE]
            result = collection.bulk_write(chunk, ordered=False)
            total_upserted += result.upserted_count
            total_modified += result.modified_count

        total_changed = total_upserted + total_modified
        print(f"\n✓ Bulk upsert complete: upserted={total_upserted}, modified={total_modified}, total_changed={total_changed}")
        return total_changed
    except Exception as e:
        print(f"✗ Error saving to MongoDB: {e}")
        return 0


def scrape_and_upsert_url(url, max_products, collection, worker_label):
    """Worker task: scrape one URL and bulk upsert its products."""
    print(f"\n\n{'█'*70}")
    print(f"  [{worker_label}] Processing URL")
    print('█'*70)

    products = scrape_products_from_url(url, max_products)
    changed_count = 0

    if products and collection is not None:
        changed_count = save_to_mongodb(products, collection)

    print(f"\n  → Worker [{worker_label}] scraped {len(products)} products")
    print(f"  → Worker [{worker_label}] upserted/modified {changed_count} products")

    return url, products, changed_count

def main():
    """Main scraping function with MongoDB storage."""
    print("\n" + "="*70)
    print("  CELLPHONES.COM.VN SCRAPER - MongoDB Edition")
    print("="*70)
    
    # Connect to MongoDB
    collection = get_mongo_connection()

    if collection is not None:
        # Ensure fast upsert lookup and uniqueness by URL
        collection.create_index('url', unique=True)
    
    MAX_PRODUCTS = 40

    all_products = []
    total_scraped = 0
    total_changed = 0

    # Concurrent scraping: each worker handles one URL
    max_workers = min(WORKERS, len(URL_LIST))
    with ThreadPoolExecutor(max_workers=max_workers) as executor:
        future_to_url = {}
        for idx, url in enumerate(URL_LIST, 1):
            worker_label = f"{idx}/{len(URL_LIST)}"
            future = executor.submit(scrape_and_upsert_url, url, MAX_PRODUCTS, collection, worker_label)
            future_to_url[future] = url

        for future in as_completed(future_to_url):
            url = future_to_url[future]
            try:
                _, products, changed_count = future.result()
                all_products.extend(products)
                total_scraped += len(products)
                total_changed += changed_count

                print(f"\n  → Completed URL: {url}")
                print(f"  → Total scraped so far: {total_scraped} products")
                print(f"  → Total upserted/modified so far: {total_changed} products")
            except Exception as e:
                print(f"✗ Worker failed for {url}: {e}")
    
    # Save to JSON as backup (remove MongoDB ObjectId fields)
    products_for_json = []
    for product in all_products:
        product_copy = product.copy()
        if '_id' in product_copy:
            del product_copy['_id']
        products_for_json.append(product_copy)
    
    output_data = {
        'total_products': len(products_for_json),
        'scraped_at': datetime.now().isoformat(),
        'source_urls': URL_LIST,
        'products': products_for_json
    }
    
    json_file = 'scraped_products_detailed.json'
    with open(json_file, 'w', encoding='utf-8') as f:
        json.dump(output_data, f, ensure_ascii=False, indent=2)
    
    # Final summary
    print("\n\n" + "="*70)
    print("  SCRAPING COMPLETE!")
    print("="*70)
    print(f"  Total products scraped: {len(all_products)}")
    print(f"  Total products upserted/modified: {total_changed}")
    print(f"  Saved to MongoDB: {DATABASE_NAME}.{COLLECTION_NAME}")
    print(f"  Backup JSON file: {json_file}")
    print("="*70)
    
    # Show sample product
    if all_products:
        print("\nSample Product:")
        sample = all_products[0]
        print(f"  Name: {sample.get('name', 'N/A')}")
        print(f"  Price: {sample.get('price_display', 'N/A')}")
        print(f"  URL: {sample.get('url', 'N/A')}")
        print(f"  Specs: {len(sample.get('technical_specs', {}))} fields")

if __name__ == "__main__":
    main()

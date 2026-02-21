import requests
from bs4 import BeautifulSoup
import json
import time
import random
from pymongo import MongoClient
from datetime import datetime

# MongoDB Configuration
MONGO_URI = "mongodb://localhost:27017/"
DATABASE_NAME = "electronics_store"
COLLECTION_NAME = "products"

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
    "https://cellphones.com.vn/tablet/msi.html",
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

def fetch_page(url):
    """Fetch the content of a web page with proper headers."""
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    }
    try:
        response = requests.get(url, headers=headers, timeout=10)
        response.raise_for_status()
        return response.text
    except Exception as e:
        print(f"✗ Error fetching {url}: {e}")
        return None


def extract_technical_specs(product_url):
    """Extract technical specifications from product detail page."""
    print(f"  → Fetching specs from: {product_url}")
    
    content = fetch_page(product_url)
    if not content:
        return {}
    
    soup = BeautifulSoup(content, 'html.parser')
    
    overview_specs = {}
    product_container = {}
    
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
            time.sleep(0.01)  
        
        return product_data
        
    except Exception as e:
        print(f"  ✗ Error extracting product: {e}")
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
    products = []
    
    # Find all product containers with the specific class
    product_containers = soup.find_all('div', class_='product-info-container product-item', limit=max_products)
    
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
            print(f"  ✓ {product_data.get('name', 'Unknown')[:60]}...")
            print(f"    Price: {product_data.get('price_display', 'N/A')}")
            print(f"    Rating: {product_data.get('rating', 'N/A')}")
            print(f"    Specs: {len(product_data.get('technical_specs', {}))} fields")
        
        time.sleep(1)  # Be respectful to the server
    
    return products

def save_to_mongodb(products, collection):
    """Save products to MongoDB."""
    if collection is None:
        print("✗ No MongoDB collection available")
        return 0
    
    if not products:
        print("✗ No products to save")
        return 0
    
    try:
        # Insert products into MongoDB
        result = collection.insert_many(products)
        inserted_count = len(result.inserted_ids)
        print(f"\n✓ Inserted {inserted_count} products into MongoDB")
        return inserted_count
    except Exception as e:
        print(f"✗ Error saving to MongoDB: {e}")
        return 0

def main():
    """Main scraping function with MongoDB storage."""
    print("\n" + "="*70)
    print("  CELLPHONES.COM.VN SCRAPER - MongoDB Edition")
    print("="*70)
    
    # Connect to MongoDB
    collection = get_mongo_connection()
    
    # Ask user for products per URL
    try:
        max_products = int(input("\nHow many products to scrape per URL? (default: 5): ") or "5")
    except:
        max_products = 5
    
    all_products = []
    total_scraped = 0
    
    # Scrape from each URL
    for idx, url in enumerate(URL_LIST, 1):
        print(f"\n\n{'█'*70}")
        print(f"  [{idx}/{len(URL_LIST)}] Processing URL")
        print('█'*70)
        
        products = scrape_products_from_url(url, max_products)
        all_products.extend(products)
        total_scraped += len(products)
        
        print(f"\n  → Scraped {len(products)} products from this URL")
        print(f"  → Total so far: {total_scraped} products")
        
        # Save to MongoDB after each URL (incremental save)
        if products and collection is not None:
            save_to_mongodb(products, collection)
        
        # Delay between URLs
        if idx < len(URL_LIST):
            time.sleep(2)
    
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
    print(f"  ✓ Total products scraped: {len(all_products)}")
    print(f"  ✓ Saved to MongoDB: {DATABASE_NAME}.{COLLECTION_NAME}")
    print(f"  ✓ Backup JSON file: {json_file}")
    print("="*70)
    
    # Show sample product
    if all_products:
        print("\n📦 Sample Product:")
        sample = all_products[0]
        print(f"  Name: {sample.get('name', 'N/A')}")
        print(f"  Price: {sample.get('price_display', 'N/A')}")
        print(f"  URL: {sample.get('url', 'N/A')}")
        print(f"  Specs: {len(sample.get('technical_specs', {}))} fields")

if __name__ == "__main__":
    main()

import mysql.connector
import json
import hashlib
import random
from datetime import datetime

# Database configuration - UPDATE THESE WITH YOUR CREDENTIALS
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',  # Change to your MySQL username
    'password': '',  # Change to your MySQL password
    'database': 'electronics_store'
}

def hash_password(password):
    """Hash password using SHA-256."""
    return hashlib.sha256(password.encode()).hexdigest()

def connect_db():
    """Create database connection."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        print("✓ Connected to database successfully")
        return conn
    except mysql.connector.Error as e:
        print(f"✗ Error connecting to database: {e}")
        return None

def insert_users(cursor):
    """Insert sample users."""
    print("\n📌 Inserting users...")
    
    users = [
        ('admin', 'admin@electronicsstore.com', hash_password('password123'), 'Admin User', 'admin'),
        ('customer1', 'customer@example.com', hash_password('password123'), 'John Doe', 'customer')
    ]
    
    query = """
        INSERT INTO users (username, email, password, full_name, role)
        VALUES (%s, %s, %s, %s, %s)
    """
    
    cursor.executemany(query, users)
    print(f"✓ Inserted {cursor.rowcount} users")

def insert_categories(cursor):
    """Insert product categories."""
    print("\n📌 Inserting categories...")
    
    categories = [
        ('Smartphones', 'Latest smartphones and mobile devices'),
        ('Laptops', 'High-performance laptops and notebooks'),
        ('Tablets', 'Tablets and iPad devices'),
        ('Audio', 'Headphones, earbuds, and audio accessories'),
        ('Accessories', 'Tech accessories and gadgets')
    ]
    
    query = """
        INSERT INTO categories (name, description)
        VALUES (%s, %s)
    """
    
    cursor.executemany(query, categories)
    print(f"✓ Inserted {cursor.rowcount} categories")
    
    # Return category IDs
    cursor.execute("SELECT id, name FROM categories")
    return {name: id for id, name in cursor.fetchall()}

def insert_products(cursor, category_map):
    """Insert products from scraped data or use defaults."""
    print("\n📌 Inserting products...")
    
    products = []
    
    # Try to load scraped data
    try:
        with open('scraped_data.json', 'r', encoding='utf-8') as f:
            data = json.load(f)
            scraped_products = data.get('products', [])
            
            for product in scraped_products[:12]:  # Limit to 12 products
                category_name = product.get('category', 'Smartphones')
                category_id = category_map.get(category_name, 1)
                
                products.append((
                    category_id,
                    product.get('name', 'Unknown Product'),
                    product.get('description', 'High-quality electronics product'),
                    product.get('price', 10000000),
                    product.get('rating', 4.5),
                    product.get('image_url', '')
                ))
            
            print(f"✓ Loaded {len(products)} products from scraped data")
    except FileNotFoundError:
        print("⚠ scraped_data.json not found, using default products")
    
    # If not enough products, add defaults
    if len(products) < 12:
        default_products = [
            (category_map['Smartphones'], 'iPhone 15 Pro Max', 'Latest iPhone with A17 Pro chip', 34990000, 4.8, ''),
            (category_map['Smartphones'], 'Samsung Galaxy S24 Ultra', 'Flagship Samsung with S Pen', 29990000, 4.7, ''),
            (category_map['Smartphones'], 'Xiaomi 14 Pro', 'High-end Xiaomi flagship', 19990000, 4.5, ''),
            (category_map['Laptops'], 'MacBook Pro 16" M3', 'Professional laptop with M3 chip', 64990000, 4.9, ''),
            (category_map['Laptops'], 'Dell XPS 15', 'Premium Windows laptop', 42990000, 4.7, ''),
            (category_map['Laptops'], 'ASUS ROG Zephyrus G14', 'Gaming laptop', 38990000, 4.6, ''),
            (category_map['Tablets'], 'iPad Pro 12.9"', 'Professional tablet with M2 chip', 29990000, 4.8, ''),
            (category_map['Tablets'], 'Samsung Galaxy Tab S9', 'Android tablet with S Pen', 24990000, 4.6, ''),
            (category_map['Audio'], 'AirPods Pro 2', 'Premium wireless earbuds', 6290000, 4.8, ''),
            (category_map['Audio'], 'Sony WH-1000XM5', 'Noise-cancelling headphones', 8990000, 4.9, ''),
            (category_map['Accessories'], 'Apple Watch Ultra 2', 'Premium smartwatch', 21990000, 4.8, ''),
            (category_map['Accessories'], 'Anker PowerBank 20000mAh', 'High-capacity power bank', 890000, 4.5, ''),
        ]
        
        products = default_products[:12]
        print(f"✓ Using {len(products)} default products")
    
    query = """
        INSERT INTO products (category_id, name, description, price, rating, image_url)
        VALUES (%s, %s, %s, %s, %s, %s)
    """
    
    cursor.executemany(query, products)
    print(f"✓ Inserted {cursor.rowcount} products")

def insert_locations(cursor):
    """Insert store locations."""
    print("\n📌 Inserting locations...")
    
    locations = [
        ('Main Street Branch', '123 Main Street, District 1, Ho Chi Minh City', 
         'https://maps.google.com/?q=10.762622,106.660172'),
        ('City Mall Store', '456 Nguyen Hue, District 1, Ho Chi Minh City',
         'https://maps.google.com/?q=10.774174,106.700806'),
        ('Airport Store', 'Tan Son Nhat Airport, Tan Binh District, Ho Chi Minh City',
         'https://maps.google.com/?q=10.817765,106.658776')
    ]
    
    query = """
        INSERT INTO locations (name, address, map_link)
        VALUES (%s, %s, %s)
    """
    
    cursor.executemany(query, locations)
    print(f"✓ Inserted {cursor.rowcount} locations")

def insert_product_availability(cursor):
    """Insert product availability data."""
    print("\n📌 Inserting product availability...")
    
    # Get all products and locations
    cursor.execute("SELECT id FROM products")
    product_ids = [row[0] for row in cursor.fetchall()]
    
    cursor.execute("SELECT id FROM locations")
    location_ids = [row[0] for row in cursor.fetchall()]
    
    availability_data = []
    
    for product_id in product_ids:
        for location_id in location_ids:
            # Randomly make some products out of stock at certain locations
            is_available = random.choice([True, True, True, False])  # 75% available
            quantity = random.randint(5, 50) if is_available else 0
            
            availability_data.append((
                product_id,
                location_id,
                quantity,
                is_available
            ))
    
    query = """
        INSERT INTO product_availability (product_id, location_id, quantity, is_available)
        VALUES (%s, %s, %s, %s)
    """
    
    cursor.executemany(query, availability_data)
    print(f"✓ Inserted {cursor.rowcount} product availability records")

def main():
    """Main function to insert all data."""
    print("=" * 60)
    print("Electronics Store - Database Population Script")
    print("=" * 60)
    
    conn = connect_db()
    if not conn:
        return
    
    try:
        cursor = conn.cursor()
        
        # Insert data in order (respecting foreign key constraints)
        insert_users(cursor)
        category_map = insert_categories(cursor)
        insert_products(cursor, category_map)
        insert_locations(cursor)
        insert_product_availability(cursor)
        
        # Commit all changes
        conn.commit()
        
        print("\n" + "=" * 60)
        print("✓ All data inserted successfully!")
        print("=" * 60)
        
        # Show summary
        print("\n📊 Database Summary:")
        cursor.execute("SELECT COUNT(*) FROM users")
        print(f"   Users: {cursor.fetchone()[0]}")
        
        cursor.execute("SELECT COUNT(*) FROM categories")
        print(f"   Categories: {cursor.fetchone()[0]}")
        
        cursor.execute("SELECT COUNT(*) FROM products")
        print(f"   Products: {cursor.fetchone()[0]}")
        
        cursor.execute("SELECT COUNT(*) FROM locations")
        print(f"   Locations: {cursor.fetchone()[0]}")
        
        cursor.execute("SELECT COUNT(*) FROM product_availability")
        print(f"   Product Availability Records: {cursor.fetchone()[0]}")
        
    except mysql.connector.Error as e:
        print(f"\n✗ Error: {e}")
        conn.rollback()
    finally:
        cursor.close()
        conn.close()
        print("\n✓ Database connection closed")

if __name__ == "__main__":
    main()

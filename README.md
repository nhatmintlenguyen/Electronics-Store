# Electronics Store - Web Programming Project

A full-featured electronics e-commerce website built with PHP and Bootstrap, featuring web scraping from cellphones.com.vn.

## Features

- 🛍️ Product catalog with categories
- 🔍 Search and filter functionality
- 👤 User authentication (Admin & Customer roles)
- 📍 Multiple store locations with Google Maps integration
- 📊 Product availability tracking across locations
- 🎨 Responsive design with Bootstrap 5
- 🔒 Secure password hashing

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript
- **Web Scraping**: Python (BeautifulSoup, Requests)

## Project Structure

```
electronics_store/
├── admin/                  # Admin dashboard files
├── assets/
│   ├── css/               # Custom stylesheets
│   ├── js/                # Custom JavaScript
│   └── images/            # Image assets
├── includes/
│   ├── config.php         # Database configuration
│   ├── functions.php      # Helper functions
│   ├── header.php         # Header template
│   └── footer.php         # Footer template
├── public/                # Public assets
├── database_setup.sql     # Database schema
├── insert_data.py         # Data insertion script
├── scraper_enhanced.py    # Web scraper
└── *.php                  # Main application files
```

## Installation & Setup

### Prerequisites

- PHP 7.4 or higher
- MySQL/MariaDB
- Python 3.7+ (for web scraping)
- Apache/Nginx web server
- Composer (optional)

### Step 1: Database Setup

1. Create a new MySQL database:
```sql
CREATE DATABASE electronics_store;
```

2. Import the database schema:
```bash
mysql -u root -p electronics_store < database_setup.sql
```

Or use phpMyAdmin to import `database_setup.sql`.

### Step 2: Configure Database Connection

Edit the database credentials in two files:

**File: `includes/config.php`**
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_mysql_username');
define('DB_PASS', 'your_mysql_password');
define('DB_NAME', 'electronics_store');
```

**File: `insert_data.py`**
```python
DB_CONFIG = {
    'host': 'localhost',
    'user': 'your_mysql_username',
    'password': 'your_mysql_password',
    'database': 'electronics_store'
}
```

### Step 3: Install Python Dependencies

```bash
# Create virtual environment (optional but recommended)
python3 -m venv .venv
source .venv/bin/activate  # On Linux/Mac
# .venv\Scripts\activate  # On Windows

# Install required packages
pip install requests beautifulsoup4 mysql-connector-python
```

### Step 4: Scrape Data from cellphones.com.vn

```bash
python scraper_enhanced.py
```

This will:
- Scrape product data from cellphones.com.vn
- Save data to `scraped_data.json`
- Scrape 12 products across 5 categories

### Step 5: Insert Data into Database

```bash
python insert_data.py
```

This will populate your database with:
- ✅ 5 Categories (Smartphones, Laptops, Tablets, Audio, Accessories)
- ✅ 12 Products (from scraped data or defaults)
- ✅ 3 Store Locations with Google Maps links
- ✅ 2 Users (admin and customer)
- ✅ Product availability records

### Step 6: Configure Web Server

**Option A: Using PHP Built-in Server (Development)**
```bash
cd /path/to/electronics_store
php -S localhost:8000
```

Then visit: http://localhost:8000

**Option B: Using Apache**

1. Copy project to Apache document root:
```bash
sudo cp -r electronics_store /var/www/html/
```

2. Configure virtual host (optional):
```apache
<VirtualHost *:80>
    ServerName electronics-store.local
    DocumentRoot /var/www/html/electronics_store
    
    <Directory /var/www/html/electronics_store>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Restart Apache:
```bash
sudo systemctl restart apache2
```

## Default Login Credentials

### Admin Account
- **Username**: `admin`
- **Password**: `password123`

### Customer Account
- **Username**: `customer1`
- **Password**: `password123`

## Database Schema

### Tables

1. **users** - User accounts (admin & customers)
2. **categories** - Product categories
3. **products** - Product information
4. **locations** - Store locations
5. **product_availability** - Product stock per location
6. **orders** - Customer orders
7. **order_items** - Order line items

### Relationships

- Products → Categories (Many-to-One)
- Products → Locations (Many-to-Many via product_availability)
- Orders → Users (Many-to-One)
- Order Items → Orders (Many-to-One)
- Order Items → Products (Many-to-One)

## Key Features Explanation

### Web Scraping
The `scraper_enhanced.py` script:
- Scrapes real product data from cellphones.com.vn
- Falls back to sample data if scraping fails
- Respects website with delays between requests
- Extracts: names, prices, ratings, descriptions, images

### User Roles
- **Admin**: Full access to admin dashboard, manage products, orders, users
- **Customer**: Browse products, place orders, manage profile

### Product Availability
- Tracks stock levels across multiple store locations
- Shows which products are available at which stores
- Allows customers to check stock before visiting

## Troubleshooting

### Issue: "Connection failed: Access denied"
**Solution**: Check database credentials in `includes/config.php` and `insert_data.py`

### Issue: "Table doesn't exist"
**Solution**: Run `database_setup.sql` to create tables first

### Issue: Python packages not found
**Solution**: Activate virtual environment and install packages:
```bash
source .venv/bin/activate
pip install -r requirements.txt  # or install individually
```

### Issue: Web scraper returns empty data
**Solution**: The script automatically falls back to sample data if scraping fails. This is normal and expected.

## Development Notes

### Adding New Features

1. **New PHP pages**: Create in root directory, include header/footer
2. **New styles**: Add to `assets/css/style.css`
3. **New JavaScript**: Add to `assets/js/script.js`
4. **Database changes**: Update `database_setup.sql` and migration scripts

### File Permissions (Linux)

```bash
# Make sure web server can read files
sudo chown -R www-data:www-data electronics_store/
sudo chmod -R 755 electronics_store/

# If you need to upload images
sudo chmod -R 777 electronics_store/assets/images/
```

## API Endpoints (Future Enhancement)

The project structure supports adding REST API endpoints:
- `api/products.php` - Product CRUD operations
- `api/cart.php` - Shopping cart management
- `api/orders.php` - Order processing

## Security Considerations

- ✅ Password hashing with SHA-256
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS protection (htmlspecialchars)
- ✅ Input sanitization
- ✅ Session management
- ⚠️ For production: Use bcrypt instead of SHA-256, add HTTPS, implement CSRF tokens

## License

This project is for educational purposes (University Semester 6 - Web Programming).

## Support

For issues or questions, please refer to the assignment documentation or contact your instructor.

---

**Created for**: University Semester 6 - Web Programming Course  
**Topic**: Electronics Store E-commerce Website

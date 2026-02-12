# Quick Start Guide

## 🚀 Fast Setup (5 Minutes)

### 1. Install Python Dependencies
```bash
pip install requests beautifulsoup4 mysql-connector-python
```

### 2. Setup Database
```bash
# Login to MySQL
mysql -u root -p

# Create database and import schema
CREATE DATABASE electronics_store;
USE electronics_store;
SOURCE database_setup.sql;
```

### 3. Configure Database Credentials

Edit these two files with your MySQL credentials:

**includes/config.php:**
```php
define('DB_USER', 'root');  // Your MySQL username
define('DB_PASS', '');      // Your MySQL password
```

**insert_data.py:**
```python
DB_CONFIG = {
    'user': 'root',      # Your MySQL username
    'password': '',      # Your MySQL password
}
```

### 4. Scrape & Insert Data
```bash
# Scrape data from cellphones.com.vn
python scraper_enhanced.py

# Insert into database
python insert_data.py
```

### 5. Start PHP Server
```bash
php -S localhost:8000
```

### 6. Access Website
Open browser: **http://localhost:8000**

Login with:
- Username: `admin`
- Password: `password123`

## ✅ What You Get

- ✅ 5 Categories
- ✅ 12 Products (scraped from cellphones.com.vn)
- ✅ 3 Store Locations
- ✅ 2 Users (admin + customer)
- ✅ Product availability tracking
- ✅ Bootstrap 5 responsive design

## 🔧 Troubleshooting

**Can't connect to database?**
- Check MySQL is running: `sudo systemctl status mysql`
- Verify credentials in config files

**Scraper not working?**
- Don't worry! It will use default sample data automatically

**Port 8000 already in use?**
- Try: `php -S localhost:8080` instead

## 📁 Important Files

| File | Purpose |
|------|---------|
| `index.php` | Homepage |
| `products.php` | Product listing |
| `login.php` | User login |
| `includes/config.php` | Database config |
| `database_setup.sql` | Database schema |
| `scraper_enhanced.py` | Web scraper |
| `insert_data.py` | Data insertion |

---

Need help? Check the full **README.md** for detailed instructions!

# 🚀 COMPLETE SETUP GUIDE - Electronics Store

## Prerequisites Checklist

Before starting, ensure you have:
- [ ] PHP 7.4 or higher installed
- [ ] MySQL/MariaDB installed and running
- [ ] Python 3.7+ installed
- [ ] Terminal/Command line access
- [ ] Text editor (VS Code, Sublime, etc.)

## Quick Start (Choose One Method)

### 🎯 Method 1: Automated Setup (Recommended)

```bash
# Run the complete setup script
python3 setup_complete.py
```

This will:
1. Check Python version
2. Install required packages
3. Guide you through database setup
4. Run the scraper
5. Insert all data
6. Show final instructions

### 🎯 Method 2: Manual Step-by-Step

Follow the steps below for complete control.

---

## Manual Setup Instructions

### Step 1: Install Python Dependencies

```bash
# Option A: Using pip directly
pip install requests beautifulsoup4 mysql-connector-python

# Option B: Using virtual environment (recommended)
python3 -m venv .venv
source .venv/bin/activate  # On Linux/Mac
pip install -r requirements.txt
```

### Step 2: Setup MySQL Database

```bash
# Login to MySQL
mysql -u root -p

# In MySQL prompt:
CREATE DATABASE electronics_store;
USE electronics_store;
SOURCE database_setup.sql;
exit;
```

**Verify tables created:**
```bash
mysql -u root -p electronics_store -e "SHOW TABLES;"
```

You should see:
- users
- categories
- products
- locations
- product_availability
- orders
- order_items

### Step 3: Configure Database Credentials

**File 1: `includes/config.php`**

Open and update lines 3-5:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // ← Your MySQL username
define('DB_PASS', '');            // ← Your MySQL password
define('DB_NAME', 'electronics_store');
```

**File 2: `insert_data.py`**

Open and update lines 7-12:
```python
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',           # ← Your MySQL username
    'password': '',           # ← Your MySQL password
    'database': 'electronics_store'
}
```

### Step 4: Run Web Scraper

```bash
python3 scraper_enhanced.py
```

**Expected output:**
```
============================================================
Electronics Store Data Scraper
============================================================
Scraping Smartphones from https://cellphones.com.vn/mobile.html...
  ✓ Scraped: iPhone 15 Pro Max - 34,990,000₫ - Rating: 4.8
  ✓ Scraped: Samsung Galaxy S24 Ultra - 29,990,000₫ - Rating: 4.7
...
✓ Successfully scraped 12 products!
✓ Data saved to scraped_data.json
============================================================
```

**Note:** If scraping fails, the script will automatically use default sample data.

### Step 5: Insert Data into Database

```bash
python3 insert_data.py
```

**Expected output:**
```
============================================================
Electronics Store - Database Population Script
============================================================
✓ Connected to database successfully

📌 Inserting users...
✓ Inserted 2 users

📌 Inserting categories...
✓ Inserted 5 categories

📌 Inserting products...
✓ Loaded 12 products from scraped data
✓ Inserted 12 products

📌 Inserting locations...
✓ Inserted 3 locations

📌 Inserting product availability...
✓ Inserted 36 product availability records

============================================================
✓ All data inserted successfully!
============================================================

📊 Database Summary:
   Users: 2
   Categories: 5
   Products: 12
   Locations: 3
   Product Availability Records: 36
```

### Step 6: Verify Database Content

```bash
# Check users
mysql -u root -p electronics_store -e "SELECT id, username, role FROM users;"

# Check categories
mysql -u root -p electronics_store -e "SELECT * FROM categories;"

# Check products count
mysql -u root -p electronics_store -e "SELECT COUNT(*) as total_products FROM products;"
```

### Step 7: Start PHP Development Server

```bash
# Navigate to project directory
cd /path/to/electronics_store

# Start server
php -S localhost:8000
```

**Expected output:**
```
PHP 8.1.0 Development Server (http://localhost:8000) started
```

### Step 8: Access the Website

Open your browser and go to:
```
http://localhost:8000
```

**Test the following pages:**
- Homepage: http://localhost:8000/index.php
- Products: http://localhost:8000/products.php
- Locations: http://localhost:8000/locations.php
- Login: http://localhost:8000/login.php

### Step 9: Test Login

#### Test Admin Account
1. Go to http://localhost:8000/login.php
2. Enter:
   - Username: `admin`
   - Password: `password123`
3. Click Login
4. You should be redirected to admin dashboard (or homepage)

#### Test Customer Account
1. Logout first
2. Go to http://localhost:8000/login.php
3. Enter:
   - Username: `customer1`
   - Password: `password123`
4. Click Login
5. You should see customer view

---

## Troubleshooting Guide

### Problem: "Connection failed: Access denied for user"

**Solution:**
1. Check MySQL is running: `sudo systemctl status mysql`
2. Verify your MySQL credentials
3. Update `includes/config.php` and `insert_data.py`
4. Try connecting manually: `mysql -u root -p`

### Problem: "Table 'electronics_store.users' doesn't exist"

**Solution:**
```bash
cd /path/to/electronics_store
mysql -u root -p electronics_store < database_setup.sql
```

### Problem: "No module named 'requests'"

**Solution:**
```bash
pip install requests beautifulsoup4 mysql-connector-python
```

### Problem: "php: command not found"

**Solution:**
```bash
# Ubuntu/Debian
sudo apt install php php-mysql

# Fedora/RHEL
sudo dnf install php php-mysqlnd

# macOS
brew install php
```

### Problem: "Port 8000 already in use"

**Solution:**
Use a different port:
```bash
php -S localhost:8080
# Then access: http://localhost:8080
```

### Problem: Scraper returns empty data

**Solution:**
This is normal! The script automatically falls back to default sample data. Your database will still be populated correctly.

### Problem: "PDOException: SQLSTATE[HY000] [1045]"

**Solution:**
1. Wrong database password in `includes/config.php`
2. Check MySQL user permissions:
```sql
GRANT ALL PRIVILEGES ON electronics_store.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

---

## Verification Checklist

After setup, verify everything works:

- [ ] Database created and tables exist
- [ ] Can connect to database (no errors in browser)
- [ ] Homepage loads successfully
- [ ] Can see 12 products on products page
- [ ] Can filter products by category
- [ ] Can search for products
- [ ] Store locations page shows 3 locations
- [ ] Can login with admin account
- [ ] Can login with customer account
- [ ] No PHP errors in browser
- [ ] Bootstrap styling is applied correctly

---

## Next Steps After Setup

### For Development:

1. **Customize Design**
   - Edit `assets/css/style.css`
   - Modify color scheme in CSS variables

2. **Add Features**
   - Shopping cart functionality
   - Product detail pages
   - Admin dashboard
   - Order management

3. **Add More Data**
   - Run scraper again for more products
   - Manually add products via admin panel (future)

### For Production:

1. **Change passwords** in database
2. **Use bcrypt** instead of SHA-256
3. **Add HTTPS** with SSL certificate
4. **Configure Apache/Nginx** properly
5. **Set up backups** for database
6. **Add .htaccess** for security
7. **Enable error logging**

---

## Common Commands Reference

```bash
# Start PHP server
php -S localhost:8000

# Stop PHP server
Ctrl+C (in terminal)

# Check MySQL status
sudo systemctl status mysql

# Restart MySQL
sudo systemctl restart mysql

# Access MySQL
mysql -u root -p

# Run scraper
python3 scraper_enhanced.py

# Insert data
python3 insert_data.py

# Check database
mysql -u root -p electronics_store -e "SHOW TABLES;"

# Backup database
mysqldump -u root -p electronics_store > backup.sql

# Restore database
mysql -u root -p electronics_store < backup.sql
```

---

## Support & Resources

- **Full Documentation**: See `README.md`
- **Architecture Details**: See `ARCHITECTURE.md`
- **Project Status**: See `PROJECT_CHECKLIST.md`
- **Quick Reference**: See `QUICK_START.md`

---

## Success! 🎉

If you can:
1. ✅ Access http://localhost:8000
2. ✅ See products on the homepage
3. ✅ Login with admin/password123

**Then your setup is complete and working!**

Enjoy building your electronics store website! 🚀

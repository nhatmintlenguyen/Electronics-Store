# Electronics Store

Electronics Store is a Web Programming semester project built with PHP, MySQL/MariaDB, HTML, CSS, and Vanilla JavaScript. The project has been refactored into a lightweight MVC structure with a single public entry point, clean routes, reusable views, AJAX endpoints, and database-backed product data.

## Features

- Homepage with hero banner, category tiles, hardcoded featured products, and promotional product cards.
- Product catalog with category filtering, keyword search, sorting, and pagination.
- Product detail page with image, price, rating, technical specifications, store availability, normalized HTML description, and related products.
- AJAX search dropdown with JSON responses from the PHP backend.
- AJAX category filtering and AJAX add-to-cart/add-to-wishlist actions.
- Session-based cart and wishlist for the current browsing session.
- Login, registration, logout, and password reset pages.
- Static pages for profile, about, contact, and store locations.
- Clean URLs through `.htaccess` rewrite rules.
- Basic SEO files: `public/robots.txt` and `public/sitemap.xml`.

## Tech Stack

- PHP 8.1+ with PDO MySQL.
- MySQL or MariaDB. XAMPP MariaDB works with the default configuration.
- HTML5 and Tailwind CDN.
- Vanilla JavaScript with `fetch()` for AJAX.
- Python 3.12+ for scraping and MongoDB-to-MySQL migration scripts.

## Project Structure

```text
electronics_store/
├── public/
│   ├── index.php          # Front controller, all web requests enter here
│   ├── .htaccess          # Clean URL rewrite to public/index.php
│   ├── css/
│   │   └── app.css        # Main stylesheet
│   ├── js/
│   │   ├── app.js         # Main JavaScript
│   │   └── script.js      # Legacy/compatibility JavaScript
│   ├── robots.txt
│   └── sitemap.xml
├── app/
│   ├── Core/              # Mini MVC framework: App, Router, View, Database
│   ├── Controllers/       # Request handlers
│   ├── Models/            # Database query layer
│   ├── Services/          # Reserved for service classes
│   ├── Support/           # Shared helpers and language helpers
│   └── bootstrap.php      # Path constants, autoloading, config loading
├── routes/
│   ├── web.php            # Page routes
│   ├── api.php            # JSON/AJAX routes
│   └── admin.php          # Reserved admin routes
├── resources/
│   └── views/
│       ├── layouts/       # Header, footer, main layout
│       └── pages/         # Page templates
├── config/
│   ├── app.php            # Session/app helpers and app settings
│   ├── database.php       # MySQL connection settings
│   └── mail.php           # Mail settings placeholder
├── database/
│   ├── schema/
│   │   └── database_setup.sql
│   └── data/
│       └── scraped_products_detailed.json
├── scripts/               # Scraping, migration, and debug utilities
├── storage/               # Runtime/session storage
├── schema.sql             # Root copy of the database schema
├── requirements.txt       # Python dependencies
└── pyproject.toml         # Python project metadata
```

## Request Flow

```text
Browser
-> public/index.php
-> app/bootstrap.php
-> routes/web.php or routes/api.php
-> App\Core\Router
-> Controller
-> Model / Database if needed
-> View or JSON response
```

Example routes:

```text
GET  /                  -> HomeController@index
GET  /products          -> ProductController@index
GET  /product/{id}      -> ProductController@show
GET  /search-products   -> ApiController@searchProducts
POST /add-to-cart       -> ApiController@addToCart
POST /add-to-wishlist   -> ApiController@addToWishlist
GET  /login             -> AuthController@login
POST /login             -> AuthController@login
```

Legacy `.php` URLs such as `/products.php` and `/product_detail.php?id=1` are still supported for compatibility.

## Local Setup

### 1. Start MySQL

Start MySQL/MariaDB from XAMPP or your local database service.

The default database configuration is:

```php
host:     127.0.0.1
port:     3306
database: electronics_store
username: root
password: ''
```

If your local database uses different values, update:

```text
config/database.php
```

### 2. Create The Database

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS electronics_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

For XAMPP with an empty root password, use:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS electronics_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3. Import The Schema

Recommended schema file:

```bash
mysql -u root -p electronics_store < database/schema/database_setup.sql
```

For XAMPP with an empty root password:

```bash
mysql -u root electronics_store < database/schema/database_setup.sql
```

The migration script also points to `database/schema/database_setup.sql`, so this is the canonical schema file.

### 4. Install Python Dependencies For Data Migration

Only required if you want to run the scraper or MongoDB-to-MySQL migration scripts.

```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
```

The migration script requires these `.env` variables:

```text
MONGODB_CONNECTION_STRING=
MONGODB_DATABASE=
MONGODB_PRODUCTS_COLLECTION=
```

### 5. Migrate Product Data

```bash
python scripts/migrate_mongo_to_mysql.py
```

The script initializes the MySQL schema if no tables exist, inserts seed users, categories, locations, products, specifications, descriptions, and product-location availability.

Seed accounts created by the migration:

```text
admin@electronics.local / admin123
customer1@electronics.local / pass1234
```

## Running The Website

### Option A: PHP Built-In Server

Use this option for quick local development:

```bash
php -S localhost:8000 -t public
```

Open:

```text
http://localhost:8000/
http://localhost:8000/products
http://localhost:8000/product/1
```

### Option B: XAMPP Apache

Place the project inside the XAMPP web root, for example:

```text
/opt/lampp/htdocs/electronics_store
```

or:

```text
C:\xampp\htdocs\electronics_store
```

Enable Apache rewrite module if needed, then open:

```text
http://localhost/electronics_store/
http://localhost/electronics_store/products
http://localhost/electronics_store/product/1
```

The root `.htaccess` forwards requests into `public/`, and `public/.htaccess` rewrites clean URLs to `public/index.php`.

If `.htaccess` rewriting is disabled, use the public front controller URL:

```text
http://localhost/electronics_store/public/index.php
```

## Useful Commands

Check PHP syntax:

```bash
php -l public/index.php
php -l app/bootstrap.php
php -l app/Controllers/HomeController.php
php -l app/Models/Product.php
php -l resources/views/pages/home.php
```

Search for remaining Vietnamese UI strings:

```bash
rg "[À-ỹ]" app resources public/js config -n
```

Debug normalized product description output:

```bash
php scripts/debug_product_description.php 2
```

## Notes About Cart And Wishlist

Cart and wishlist are stored in PHP sessions:

```text
$_SESSION['cart']
$_SESSION['wishlist']
```

This does not conflict with the `orders` and `order_items` tables. The session cart is a temporary shopping state before checkout, while `orders` and `order_items` are intended for finalized purchases. The current schema does not include a persistent wishlist table.

## Troubleshooting

### Database Connection Failed

Check:

- MySQL/MariaDB is running.
- The `electronics_store` database exists.
- `config/database.php` matches your local database credentials.
- Use `127.0.0.1` instead of `localhost` if PHP tries to connect through a missing MySQL socket.

### Clean URLs Do Not Work In XAMPP

Check:

- Apache rewrite module is enabled.
- `.htaccess` files are allowed by Apache configuration.
- The project root contains `.htaccess`.
- `public/.htaccess` exists.

Fallback URL:

```text
http://localhost/electronics_store/public/index.php
```

### CSS Or JavaScript Does Not Load

Check that these files exist:

```text
public/css/app.css
public/js/app.js
```

Also check the generated asset URLs if the project is served from a subfolder such as `/electronics_store`.

### Migration Fails With `No module named mysql`

Activate the virtual environment and install dependencies:

```bash
source .venv/bin/activate
pip install -r requirements.txt
```

The required package is `mysql-connector-python`.

### Product Detail Description Looks Wrong

Descriptions are scraped HTML stored in the `products.description` field. The project normalizes and embeds that HTML in the product detail page, then styles it through `public/css/app.css`.

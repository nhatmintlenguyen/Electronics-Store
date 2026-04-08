# Electronics Store

Dynamic PHP/MySQL semester project for the Web Programming course. The codebase has been refactored into an application-style structure with `app/` for shared logic, `public/` for web entry points, `database/` for schema/data assets, and `scripts/` for scraping and data enrichment.

## Current Structure

```text
electronics_store/
├── app/
│   ├── bootstrap.php
│   ├── Config/
│   │   └── app.php
│   ├── Support/
│   │   ├── helpers.php
│   │   └── language.php
│   └── Views/
│       └── layouts/
│           ├── footer.php
│           └── header.php
├── public/
│   ├── index.php
│   ├── products.php
│   ├── product_detail.php
│   ├── login.php
│   ├── logout.php
│   ├── locations.php
│   └── assets/
│       ├── css/style.css
│       └── js/script.js
├── database/
│   └── schema/
│       └── database_setup.sql
├── scripts/
│   ├── scraper_enhanced.py
│   ├── insert_data.py
│   ├── enrich_fields.py
│   ├── main.py
│   └── setup.sh
├── resources/
├── storage/
├── pyproject.toml
├── requirements.txt
└── SemesterProject.pdf
```

## Tech Stack

- PHP 8+
- MySQL / MariaDB
- HTML, CSS, JavaScript
- Tailwind CDN for UI styling
- Python for scraping and data enrichment

## Features Present In The Current Version

- Product catalog from MySQL
- Product sorting and category filtering
- Product detail page with breadcrumbs
- Shared app bootstrap and helper layer
- Multi-language support scaffold (`vi` / `en`)
- Store locations page
- Python scraping pipeline
- Product field enrichment script

## Prerequisites

- PHP 8.0+
- MySQL / MariaDB
- Python 3.10+ recommended

## Database Setup

Create the database:

```sql
CREATE DATABASE electronics_store;
```

Import the schema:

```bash
mysql -u root -p electronics_store < database/schema/database_setup.sql
```

## Application Configuration

Edit database credentials in [app/Config/app.php](/home/nhatminh/Documents/UNIVERSITY/semester 6/web programming/electronics_store/app/Config/app.php):

```php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'electronics_store';
```

If your Python scripts connect to MySQL directly, also update credentials in:

- [scripts/insert_data.py](/home/nhatminh/Documents/UNIVERSITY/semester 6/web programming/electronics_store/scripts/insert_data.py)
- [scripts/enrich_fields.py](/home/nhatminh/Documents/UNIVERSITY/semester 6/web programming/electronics_store/scripts/enrich_fields.py)

## Python Environment

```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
```

If `requirements.txt` is incomplete in your local copy, install the needed packages manually, for example:

```bash
pip install requests beautifulsoup4 mysql-connector-python
```

## Data Pipeline

Scrape source product data:

```bash
python scripts/scraper_enhanced.py
```

Insert data into MySQL:

```bash
python scripts/insert_data.py
```

Enrich product metadata and availability fields:

```bash
python scripts/enrich_fields.py
```

`scraper_enhanced.py` is intentionally kept as part of the project.

## Run The Website Locally

This project now uses `public/` as the web document root.

Preferred command:

```bash
php -S localhost:8000 -t public
```

Then open:

- `http://localhost:8000/`
- `http://localhost:8000/products.php`
- `http://localhost:8000/product_detail.php?id=1`
- `http://localhost:8000/login.php`
- `http://localhost:8000/locations.php`

## Why You Got `Not Found` For `/products.php`

If you started the server like this:

```bash
php -S localhost:8000
```

then PHP served the repository root, but `products.php` is no longer in the root directory. It was moved to `public/products.php` during the refactor.

That means:

- `http://localhost:8000/products.php` will fail when serving the repo root
- `http://localhost:8000/public/products.php` may work in that setup
- the correct setup is still to run `php -S localhost:8000 -t public`

## Apache / Nginx Note

If you use Apache or Nginx, point the document root to the `public/` folder, not the repository root.

Example Apache `DocumentRoot`:

```apache
DocumentRoot /path/to/electronics_store/public
```

## Important Current Limitation

The project structure has been refactored, but some pages and features are still being aligned to the new structure. If a page throws include/path errors, it means that route still needs to be updated to the new `app/` bootstrap flow.

## Troubleshooting

### `Not Found: /products.php`

Use:

```bash
php -S localhost:8000 -t public
```

### Database connection failed

Check:

- MySQL is running
- the `electronics_store` database exists
- credentials in `app/Config/app.php` are correct

### Python script cannot connect to MySQL

Make sure the Python-side DB config matches your local MySQL credentials.

### CSS or JS not loading

Confirm you are serving from `public/` as the document root. The asset files are under:

- `public/assets/css/style.css`
- `public/assets/js/script.js`

## Suggested Git Workflow

After verifying the local app works with the `public/` document root:

```bash
git status
git add -A
git commit -m "Update README for refactored public app structure"
git push origin <your-branch>
```

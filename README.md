# Electronics Store

Semester project built with PHP + MySQL for Web Programming.

## Mục tiêu refactor

Codebase này đã được sắp xếp lại để bám sát structure gợi ý:

```text
project-root/
├── public/        # entry points + public assets
├── app/           # bootstrap + shared application logic
├── config/        # app/database configuration
├── resources/     # views/templates
├── storage/       # archives, logs, runtime-oriented files
├── database/      # schema + data files
├── scripts/       # scraping/migration utilities
└── README.md
```

## Structure hiện tại

```text
electronics_store/
├── public/
│   ├── index.php
│   ├── products.php
│   ├── product_detail.php
│   ├── login.php
│   ├── profile.php
│   ├── cart.php
│   ├── wishlist.php
│   ├── locations.php
│   ├── about.php
│   ├── contact.php
│   ├── logout.php
│   ├── add_to_cart.php
│   ├── add_to_wishlist.php
│   ├── search_products.php
│   ├── filter_products.php
│   ├── sitemap.xml
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   ├── images/
│   └── assets/
├── app/
│   ├── bootstrap.php
│   └── Support/
│       ├── helpers.php
│       └── language.php
├── config/
│   └── app.php
├── resources/
│   └── views/
│       └── layouts/
│           ├── header.php
│           └── footer.php
├── storage/
│   └── archive/
│       └── legacy/
├── database/
│   ├── schema/
│   │   └── database_setup.sql
│   └── data/
├── scripts/
├── requirements.txt
└── pyproject.toml
```

## Tech stack

- PHP 8+
- MySQL / MariaDB
- HTML + Tailwind CDN + JavaScript thuần
- Python scripts cho scraping / migration

## Chức năng hiện có

- Trang chủ với giao diện storefront, hero, featured products, category filter
- Product catalog có lọc danh mục, tìm kiếm, sắp xếp, phân trang
- Product detail có mô tả HTML và tình trạng cửa hàng
- Tìm kiếm AJAX
- Add-to-cart / add-to-wishlist bằng AJAX
- Cart / wishlist lưu bằng session
- Login / register / forgot password
- Profile page
- About / contact / locations pages

## Chạy local

### 1. Tạo database

```sql
CREATE DATABASE electronics_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Import schema

```bash
mysql -u root -p electronics_store < database/schema/database_setup.sql
```

### 3. Cấu hình kết nối PHP

Sửa file:

```text
config/app.php
```

Các hằng chính:

```php
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'electronics_store';
```

### 4. Cài Python dependencies nếu cần migration

```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
```

### 5. Migrate dữ liệu

Khuyến nghị dùng:

```bash
python scripts/migrate_mongo_to_mysql.py
```

### 6. Chạy web server

```bash
php -S localhost:8000 -t public
```

Mở:

- `http://localhost:8000/`
- `http://localhost:8000/products.php`
- `http://localhost:8000/product_detail.php?id=1`

## Lưu ý structure

- `public/` chỉ chứa file được web server serve trực tiếp
- `config/` chứa cấu hình, không để trong `public/`
- `resources/views/` chứa layout / template dùng chung
- `app/` chứa bootstrap và shared logic
- `storage/archive/legacy/` giữ file cũ để tham chiếu, không dùng runtime

## Troubleshooting nhanh

### Không load được CSS/JS

Kiểm tra:

- document root phải là `public/`
- file tồn tại tại:
  - `public/css/style.css`
  - `public/js/script.js`

### Lỗi database connection

Kiểm tra:

- MySQL đang chạy
- DB `electronics_store` đã được tạo
- thông tin trong `config/app.php` đúng

### Trang trắng hoặc 500

Chạy thử:

```bash
php -l public/index.php
php -l public/product_detail.php
php -l config/app.php
```

Và kiểm tra path include trong:

- `app/bootstrap.php`
- `resources/views/layouts/`

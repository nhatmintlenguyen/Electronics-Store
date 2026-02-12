# Electronics Store - Architecture Overview

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT (Browser)                          │
│  - Bootstrap 5 UI                                            │
│  - Responsive Design                                         │
│  - JavaScript Interactivity                                  │
└────────────────────┬────────────────────────────────────────┘
                     │ HTTP Requests
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                  WEB SERVER (PHP)                            │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  Front Pages:                                         │  │
│  │  - index.php (Homepage)                               │  │
│  │  - products.php (Product Listing)                     │  │
│  │  - login.php (Authentication)                         │  │
│  │  - locations.php (Store Locations)                    │  │
│  └───────────────────────────────────────────────────────┘  │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  Backend (includes/):                                 │  │
│  │  - config.php (Database Config)                       │  │
│  │  - functions.php (Helper Functions)                   │  │
│  │  - header.php (Template Header)                       │  │
│  │  - footer.php (Template Footer)                       │  │
│  └───────────────────────────────────────────────────────┘  │
└────────────────────┬────────────────────────────────────────┘
                     │ PDO Queries
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                DATABASE (MySQL)                              │
│  ┌──────────────┬──────────────┬──────────────────────┐    │
│  │   users      │  categories  │    products          │    │
│  │  ─────────   │  ──────────  │    ────────          │    │
│  │  - id        │  - id        │    - id              │    │
│  │  - username  │  - name      │    - name            │    │
│  │  - password  │              │    - price           │    │
│  │  - role      │              │    - rating          │    │
│  └──────────────┴──────────────┴──────────────────────┘    │
│  ┌──────────────┬──────────────┬──────────────────────┐    │
│  │  locations   │  product_    │    orders            │    │
│  │              │  availability│                      │    │
│  │  - id        │  - product_id│    - id              │    │
│  │  - name      │  - location_id│   - user_id         │    │
│  │  - address   │  - quantity  │    - total_amount    │    │
│  │  - map_link  │  - is_available│  - status          │    │
│  └──────────────┴──────────────┴──────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                     ▲
                     │ Data Population
                     │
┌─────────────────────────────────────────────────────────────┐
│              DATA COLLECTION (Python)                        │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  scraper_enhanced.py                                  │  │
│  │  - Web scraping from cellphones.com.vn                │  │
│  │  - BeautifulSoup HTML parsing                         │  │
│  │  - Data extraction and cleaning                       │  │
│  │  - Output: scraped_data.json                          │  │
│  └───────────────────────────────────────────────────────┘  │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  insert_data.py                                       │  │
│  │  - Read scraped data                                  │  │
│  │  - Connect to MySQL                                   │  │
│  │  - Insert into database                               │  │
│  │  - Create relationships                               │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

## Data Flow

### 1. Initial Setup Flow
```
[1] Run database_setup.sql
     ↓
[2] Create tables and schema
     ↓
[3] Run scraper_enhanced.py
     ↓
[4] Scrape cellphones.com.vn → scraped_data.json
     ↓
[5] Run insert_data.py
     ↓
[6] Insert data into MySQL
     ↓
[7] Database ready with sample data
```

### 2. User Request Flow
```
[1] User visits website (index.php)
     ↓
[2] PHP includes header.php (navigation, session check)
     ↓
[3] PHP queries database (via PDO)
     ↓
[4] Data processed by functions.php
     ↓
[5] HTML rendered with Bootstrap styling
     ↓
[6] Page sent to browser
     ↓
[7] JavaScript enhances interactivity
```

### 3. Authentication Flow
```
[1] User visits login.php
     ↓
[2] Submits credentials
     ↓
[3] PHP validates against users table
     ↓
[4] Password hash compared
     ↓
[5] Session created on success
     ↓
[6] Redirect based on role (admin/customer)
```

## Database Relationships

```
users (1) ───────── (N) orders
                      │
                      │ (1)
                      │
                      ▼
                    (N) order_items
                      │
                      │ (N)
                      │
                      ▼
                    (1) products
                      │
                      │ (N)
                      │
            ┌─────────┴─────────┐
            │                   │
            ▼                   ▼
        (1) categories    (N) product_availability
                                │
                                │ (N)
                                │
                                ▼
                              (1) locations
```

## Key Features Implementation

### 1. Web Scraping
- **Technology**: Python + BeautifulSoup + Requests
- **Source**: cellphones.com.vn
- **Output**: JSON file with product data
- **Fallback**: Default sample data if scraping fails

### 2. Database Management
- **Technology**: MySQL + PDO
- **Security**: Prepared statements (SQL injection prevention)
- **Features**: Foreign keys, indexes, cascading deletes

### 3. Authentication
- **Method**: Session-based
- **Security**: SHA-256 password hashing
- **Roles**: Admin and Customer with different permissions

### 4. Frontend
- **Framework**: Bootstrap 5
- **Features**: Responsive, mobile-first design
- **Icons**: Bootstrap Icons
- **Custom**: CSS and JavaScript enhancements

## File Structure Purpose

```
electronics_store/
│
├── includes/              # Backend logic
│   ├── config.php        # Database connection & constants
│   ├── functions.php     # Reusable PHP functions
│   ├── header.php        # Common header template
│   └── footer.php        # Common footer template
│
├── assets/               # Static resources
│   ├── css/             # Custom stylesheets
│   ├── js/              # Custom JavaScript
│   └── images/          # Images and media
│
├── admin/               # Admin panel (future)
│
├── public/              # Public assets
│
├── *.php                # Main application pages
│
├── scraper_enhanced.py  # Web scraping script
├── insert_data.py       # Database population
├── database_setup.sql   # Database schema
│
└── Documentation files  # README, guides, etc.
```

## Technologies Used

| Layer | Technology | Purpose |
|-------|------------|---------|
| Frontend | Bootstrap 5 | UI framework |
| Frontend | HTML5/CSS3 | Structure & styling |
| Frontend | JavaScript | Interactivity |
| Backend | PHP 7.4+ | Server-side logic |
| Database | MySQL/MariaDB | Data storage |
| Scraping | Python 3 | Data collection |
| Scraping | BeautifulSoup | HTML parsing |
| Scraping | Requests | HTTP requests |

## Security Measures

1. **Password Hashing**: SHA-256 (consider bcrypt for production)
2. **SQL Injection**: PDO prepared statements
3. **XSS Prevention**: htmlspecialchars() on all outputs
4. **Input Validation**: sanitize() function
5. **Session Management**: Secure session handling
6. **Role-Based Access**: Admin/Customer separation

## Performance Optimizations

1. **Database Indexes**: On foreign keys and frequently queried columns
2. **PDO Persistent Connections**: Reuse database connections
3. **Lazy Loading**: Load data only when needed
4. **CSS/JS Minification**: Reduce file sizes (future)
5. **Image Optimization**: Compress images (future)

## Deployment Considerations

### Development
- PHP built-in server: `php -S localhost:8000`
- Local MySQL server
- Debug mode enabled

### Production (Future)
- Apache/Nginx web server
- SSL/TLS encryption (HTTPS)
- Environment variables for config
- Error logging
- Database backups
- Rate limiting for scraping

---

This architecture provides a solid foundation for an e-commerce electronics store while maintaining clean separation of concerns and scalability for future enhancements.

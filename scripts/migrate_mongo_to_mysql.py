import json
import os
import re
import sys
from pathlib import Path

import dotenv
import mysql.connector
from pymongo import MongoClient

# ── Config ────────────────────────────────────────────────────────────────────

dotenv.load_dotenv(Path(__file__).resolve().parent.parent / ".env")

BASE_DIR = Path(__file__).resolve().parent.parent
SCHEMA_PATH = BASE_DIR / "database" / "schema" / "database_setup.sql"

MONGO_URI        = os.getenv("MONGODB_CONNECTION_STRING")
MONGO_DB         = os.getenv("MONGODB_DATABASE")
MONGO_COLLECTION = os.getenv("MONGODB_PRODUCTS_COLLECTION")

MYSQL_CONFIG = {
    "host":     "127.0.0.1",
    "port":     3306,
    "user":     "root",
    "password": "",          # XAMPP default: empty
    "database": "electronics_store",
    "charset":  "utf8mb4",
}

# Sample admin + customer accounts that will always be created
SEED_USERS = [
    # (username, email, password_sha256, full_name, role)
    ("admin",     "admin@electronics.local",    _hash := __import__("hashlib").sha256(b"admin123").hexdigest(),    "Admin",     "admin"),
    ("customer1", "customer1@electronics.local", __import__("hashlib").sha256(b"pass1234").hexdigest(), "John Doe", "customer"),
]

REQUIRED_TABLES = {
    "users",
    "categories",
    "locations",
    "products",
    "product_locations",
}

# ── Helpers ───────────────────────────────────────────────────────────────────

def slugify(text: str) -> str:
    text = text.lower().strip()
    text = re.sub(r"[^\w\s-]", "", text)
    text = re.sub(r"[\s_]+", "-", text)
    text = re.sub(r"-+", "-", text)
    return text[:255]


def unique_slug(base: str, existing: set) -> str:
    slug = slugify(base)
    candidate, n = slug, 1
    while candidate in existing:
        candidate = f"{slug}-{n}"
        n += 1
    existing.add(candidate)
    return candidate


def load_sql_statements(sql_path: Path) -> list[str]:
    statements: list[str] = []
    buffer: list[str] = []

    for raw_line in sql_path.read_text(encoding="utf-8").splitlines():
        line = raw_line.strip()
        if not line or line.startswith("--"):
            continue

        buffer.append(raw_line)
        if line.endswith(";"):
            statement = "\n".join(buffer).strip()
            if statement.endswith(";"):
                statement = statement[:-1].strip()
            if statement:
                statements.append(statement)
            buffer = []

    if buffer:
        statement = "\n".join(buffer).strip()
        if statement:
            statements.append(statement)

    return statements


def get_existing_tables(cursor) -> set[str]:
    cursor.execute("SHOW TABLES")
    return {row[0] for row in cursor.fetchall()}


def ensure_mysql_schema(conn, cursor) -> None:
    existing_tables = get_existing_tables(cursor)

    if not existing_tables:
        if not SCHEMA_PATH.exists():
            print(f"ERROR: Schema file not found: {SCHEMA_PATH}")
            sys.exit(1)

        print(f"No MySQL tables found. Initializing schema from {SCHEMA_PATH} …")
        for statement in load_sql_statements(SCHEMA_PATH):
            cursor.execute(statement)
        conn.commit()
        return

    missing_tables = REQUIRED_TABLES - existing_tables
    if missing_tables:
        print("ERROR: MySQL schema does not match what this migration script expects.")
        print(f"Missing tables: {', '.join(sorted(missing_tables))}")
        print(f"Expected schema file: {SCHEMA_PATH}")
        print("Recreate the database using that schema, or point the script at the correct database.")
        sys.exit(1)


# ── Main migration ─────────────────────────────────────────────────────────────

def migrate():
    # ── MongoDB ──────────────────────────────────────────────────────────────
    if not all([MONGO_URI, MONGO_DB, MONGO_COLLECTION]):
        print("ERROR: Set MONGODB_CONNECTION_STRING, MONGODB_DATABASE, "
              "MONGODB_PRODUCTS_COLLECTION in your .env file.")
        sys.exit(1)

    mongo_client = MongoClient(MONGO_URI)
    mongo_col    = mongo_client[MONGO_DB][MONGO_COLLECTION]

    docs = list(mongo_col.find({}))
    print(f"Fetched {len(docs)} documents from MongoDB.")
    mongo_client.close()

    if not docs:
        print("Nothing to migrate.")
        return

    # ── MySQL ─────────────────────────────────────────────────────────────────
    conn   = mysql.connector.connect(**MYSQL_CONFIG)
    cursor = conn.cursor()
    ensure_mysql_schema(conn, cursor)

    # ── 1. Users ──────────────────────────────────────────────────────────────
    print("\nInserting seed users …")
    cursor.executemany(
        """INSERT IGNORE INTO users (username, email, password, full_name, role)
           VALUES (%s, %s, %s, %s, %s)""",
        SEED_USERS,
    )

    # ── 2. Categories ─────────────────────────────────────────────────────────
    print("Inserting categories …")
    category_names = sorted({doc.get("category", "Other") for doc in docs})
    for cat in category_names:
        cursor.execute(
            "INSERT IGNORE INTO categories (name, slug) VALUES (%s, %s)",
            (cat, slugify(cat)),
        )

    cursor.execute("SELECT id, name FROM categories")
    cat_map = {name: cid for cid, name in cursor.fetchall()}

    # ── 3. Locations ──────────────────────────────────────────────────────────
    print("Inserting locations …")
    seen_store_ids: dict[int, int] = {}   # source store_id -> MySQL location id

    all_locations: list[dict] = []
    for doc in docs:
        for loc in doc.get("locations", []):
            sid = loc.get("store_id")
            if sid not in seen_store_ids:
                all_locations.append(loc)
                seen_store_ids[sid] = None   # placeholder

    for loc in all_locations:
        cursor.execute(
            """INSERT IGNORE INTO locations (name, district, address, google_maps_url)
               VALUES (%s, %s, %s, %s)""",
            (
                loc.get("name"),
                loc.get("district"),
                loc.get("address"),
                loc.get("google_maps_url"),
            ),
        )
        # Retrieve the id just inserted (or already existing)
        cursor.execute(
            "SELECT id FROM locations WHERE name = %s LIMIT 1",
            (loc.get("name"),),
        )
        row = cursor.fetchone()
        if row:
            seen_store_ids[loc["store_id"]] = row[0]

    # ── 4. Products + product_locations ───────────────────────────────────────
    print("Inserting products …")
    used_slugs: set[str] = set()
    inserted_products = 0

    for doc in docs:
        cat_name   = doc.get("category", "Other")
        category_id = cat_map.get(cat_name, list(cat_map.values())[0])

        name      = doc.get("name", "Unknown Product")
        slug      = unique_slug(name, used_slugs)
        image_url = doc.get("image", "")
        price     = float(doc.get("price", 0))
        rating    = float(doc.get("rating", 0))
        description = doc.get("product_container", None)
        specs       = doc.get("technical_specs", None)
        specs_json  = json.dumps(specs, ensure_ascii=False) if specs else None

        cursor.execute(
            """INSERT INTO products
               (category_id, name, slug, image_url, price, rating, description, specifications)
               VALUES (%s, %s, %s, %s, %s, %s, %s, %s)""",
            (category_id, name, slug, image_url, price, rating, description, specs_json),
        )
        product_id = cursor.lastrowid
        inserted_products += 1

        # product_locations
        for loc in doc.get("locations", []):
            mysql_loc_id = seen_store_ids.get(loc.get("store_id"))
            if mysql_loc_id:
                cursor.execute(
                    "INSERT IGNORE INTO product_locations (product_id, location_id) VALUES (%s, %s)",
                    (product_id, mysql_loc_id),
                )

    conn.commit()

    # ── Summary ───────────────────────────────────────────────────────────────
    print("\n── Migration complete ──────────────────────────────")
    for table in ("users", "categories", "locations", "products", "product_locations"):
        cursor.execute(f"SELECT COUNT(*) FROM {table}")
        print(f"  {table}: {cursor.fetchone()[0]} rows")

    cursor.close()
    conn.close()


if __name__ == "__main__":
    migrate()

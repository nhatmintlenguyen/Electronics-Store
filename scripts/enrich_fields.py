from pathlib import Path
import os
import random
import sys

import dotenv
from pymongo import MongoClient, UpdateOne

SCRIPT_DIR = Path(__file__).resolve().parent
if str(SCRIPT_DIR) not in sys.path:
    sys.path.insert(0, str(SCRIPT_DIR))

from scraper_enhanced import extract_technical_specs


dotenv.load_dotenv()

MONGODB_URI = os.getenv("MONGODB_CONNECTION_STRING")
MONGODB_DATABASE = os.getenv("MONGODB_DATABASE")
MONGODB_PRODUCTS_COLLECTION = os.getenv("MONGODB_PRODUCTS_COLLECTION")

STORES = [
    {
        "store_id": 1,
        "name": "CellphoneS - 435 Nguyen Thi Thap",
        "district": "Q7",
        "address": "435 Nguyen Thi Thap, Tan Phong, Quan 7, TP.HCM",
        "google_maps_url": "https://www.google.com/maps?saddr=10.7914333,106.687859&daddr=CellphoneS - Trung tam Laptop, Smart Home chinh hang, gia tot, 435 Nguyen Thi Thap, Tan Phong, Quan 7",
    },
    {
        "store_id": 2,
        "name": "CellphoneS - 248 Nguyen Thi Thap",
        "district": "Q7",
        "address": "248 Nguyen Thi Thap, Quan 7, TP.HCM",
        "google_maps_url": "https://www.google.com/maps?saddr=10.7914333,106.687859&daddr=cellphone S, 248 Nguyen Thi Thap, P, Quan 7",
    },
    {
        "store_id": 3,
        "name": "CellphoneS - 571 Huynh Tan Phat",
        "district": "Q7",
        "address": "571 Huynh Tan Phat, Tan Thuan Dong, Quan 7, TP.HCM",
        "google_maps_url": "https://www.google.com/maps?saddr=10.7914333,106.687859&daddr=CellphoneS Huynh Tan Phat, 571 Huynh Tan Phat, Tan Thuan Dong, Quan 7",
    },
    {
        "store_id": 4,
        "name": "CellphoneS - 579 D. Ba Trac",
        "district": "Q8",
        "address": "579 D. Ba Trac, Phuong 1, Quan 8, TP.HCM",
        "google_maps_url": "https://www.google.com/maps?saddr=10.7914333,106.687859&daddr=cellphone S, 579 D. Ba Trac, Phuong 1, Quan 8",
    },
    {
        "store_id": 5,
        "name": "CellphoneS - 177 Khanh Hoi",
        "district": "Q4",
        "address": "177 Khanh Hoi, Phuong 3, Quan 4, TP.HCM",
        "google_maps_url": "https://www.google.com/maps?saddr=10.7914333,106.687859&daddr=cellphone S, 177 Khanh Hoi, Phuong 3, Quan 4",
    },
]


def get_products_collection():
    missing = [
        key
        for key, value in {
            "MONGODB_CONNECTION_STRING": MONGODB_URI,
            "MONGODB_DATABASE": MONGODB_DATABASE,
            "MONGODB_PRODUCTS_COLLECTION": MONGODB_PRODUCTS_COLLECTION,
        }.items()
        if not value
    ]
    if missing:
        raise RuntimeError(f"Missing MongoDB environment variables: {', '.join(missing)}")

    client = MongoClient(MONGODB_URI)
    db = client[MONGODB_DATABASE]
    return client, db[MONGODB_PRODUCTS_COLLECTION]


def enrich_locations():
    client, products = get_products_collection()

    try:
        bulk_ops = []

        for product in products.find({}, {"_id": 1}):
            assigned_locations = random.sample(STORES, random.randint(1, len(STORES)))
            bulk_ops.append(
                UpdateOne(
                    {"_id": product["_id"]},
                    {"$set": {"locations": assigned_locations}},
                )
            )

        if bulk_ops:
            result = products.bulk_write(bulk_ops)
            print("Updated locations for:", result.modified_count)
        else:
            print("No products found for location enrichment.")
    finally:
        client.close()


def retry_get_technical_specifications():
    client, products = get_products_collection()

    try:
        query = {
            "$or": [
                {"technical_specs": {"$exists": False}},
                {"technical_specs": None},
                {"technical_specs": {}},
            ]
        }

        projection = {"_id": 1, "url": 1, "name": 1}
        bulk_ops = []
        processed = 0

        for product in products.find(query, projection):
            product_url = product.get("url")
            if not product_url:
                continue

            processed += 1
            print(f"Fetching specs for: {product.get('name', product_url)}")
            overview_specs, _product_container = extract_technical_specs(product_url)

            if not overview_specs:
                print("  No specs found.")
                continue

            bulk_ops.append(
                UpdateOne(
                    {"_id": product["_id"]},
                    {"$set": {"technical_specs": overview_specs}},
                )
            )

        if bulk_ops:
            result = products.bulk_write(bulk_ops)
            print("Updated technical specifications for:", result.modified_count)
        else:
            print(f"No technical specification updates were generated. Products checked: {processed}")
    finally:
        client.close()


if __name__ == "__main__":
    retry_get_technical_specifications()

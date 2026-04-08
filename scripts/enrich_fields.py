from pymongo import MongoClient, UpdateOne
import random
import os 
import dotenv

dotenv.load_dotenv()
MONGODB_URI = os.getenv("MONGODB_CONNECTION_STRING")
MONGODB_DATABASE = os.getenv("MONGODB_DATABASE")
MONGODB_PRODUCTS_COLLECTION = os.getenv("MONGODB_PRODUCTS_COLLECTION")

print(f"{MONGODB_URI=}, {MONGODB_DATABASE=}, {MONGODB_PRODUCTS_COLLECTION=}")
STORES = [
    {
        "store_id": 1,
        "name": "CellphoneS - 435 Nguyễn Thị Thập",
        "district": "Q7",
        "address": "435 Nguyễn Thị Thập, Tân Phong, Quận 7, TP.HCM",
        "google_maps_url": "https://www.google.com/maps?saddr=10.7914333,106.687859&daddr=CellphoneS - Trung tâm Laptop, Smart Home chính hãng, giá tốt, 435 Nguyễn Thị Thập, Tân Phong, Quận 7"
    },
    {
        "store_id": 2,
        "name": "CellphoneS - 248 Nguyễn Thị Thập",
        "district": "Q7",
        "address": "248 Nguyễn Thị Thập, Quận 7, TP.HCM",
        "google_maps_url": "https://www.google.com/maps?saddr=10.7914333,106.687859&daddr=cellphone S, 248 Nguyễn Thị Thập, P, Quận 7"
    },
    {
        "store_id": 3,
        "name": "CellphoneS - 571 Huỳnh Tấn Phát",
        "district": "Q7",
        "address": "571 Huỳnh Tấn Phát, Tân Thuận Đông, Quận 7, TP.HCM",
        "google_maps_url": "https://www.google.com/maps?saddr=10.7914333,106.687859&daddr=CellphoneS Huỳnh Tấn Phát, 571 Huỳnh Tấn Phát, Tân Thuận Đông, Quận 7"
    },
    {
        "store_id": 4,
        "name": "CellphoneS - 579 D. Bá Trạc",
        "district": "Q8",
        "address": "579 D. Bá Trạc, Phường 1, Quận 8, TP.HCM",
        "google_maps_url": "https://www.google.com/maps?saddr=10.7914333,106.687859&daddr=cellphone S, 579 D. Bá Trạc, Phường 1, Quận 8"
    },
    {
        "store_id": 5,
        "name": "CellphoneS - 177 Khánh Hội",
        "district": "Q4",
        "address": "177 Khánh Hội, Phường 3, Quận 4, TP.HCM",
        "google_maps_url": "https://www.google.com/maps?saddr=10.7914333,106.687859&daddr=cellphone S, 177 Khánh Hội, Phường 3, Quận 4"
    }
]


def enrich_fields():
    client = MongoClient(MONGODB_URI)
    db = client[MONGODB_DATABASE]
    products = db[MONGODB_PRODUCTS_COLLECTION]

    bulk_ops = []

    for product in products.find({}, {"_id": 1}):
        k = random.randint(1, 5)   # mỗi sản phẩm có ngẫu nhiên 1 đến 5 cửa hàng
        assigned_locations = random.sample(STORES, k)

        bulk_ops.append(
            UpdateOne(
                {"_id": product["_id"]},
                {"$set": {"locations": assigned_locations}}
            )
        )

    if bulk_ops:
        result = products.bulk_write(bulk_ops)
        print("Updated:", result.modified_count)


if __name__ == "__main__":  
    enrich_fields()

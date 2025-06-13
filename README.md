## 📦 專案建置與資料初始化

1. 使用附檔 `docker-compose.yaml` 可建置 MySQL 環境  
2. 執行 `database_setup.sql` 建立資料庫與資料表  
3. 執行 `generate_test_data.php` 導入假資料  

---

## 🔗 API Endpoints

### 📄 取得訂單列表
```bash
GET /api/orders?page=1&pageSize=10

curl --location 'localhost:8000/api/orders?page=1&pageSize=10'

```

----------

### 🔍 查詢單一訂單

```bash
GET /api/orders/{id}

curl --location 'localhost:8000/api/orders/1'

```

----------

### 📝 建立新訂單

```bash
POST /api/orders

curl --location 'localhost:8000/api/orders' \
--header 'Content-Type: application/json' \
--data '{
    "user_id": 1,
    "items": [
        {
            "product_id": 1,
            "quantity": 100
        },
        {
            "product_id": 3,
            "quantity": 1
        }
    ]
}'

```

----------

### 🔄 更新訂單狀態

```bash
PUT /api/orders/{id}/?status=pending

curl --location --request PUT 'localhost:8000/api/orders/1/?status=pending'

```

----------

### 📊 訂單統計資料

```bash
GET /api/orders/stats

curl --location 'localhost:8000/api/orders/stats'

```

----------

### 🛒 商品列表

```bash
GET /api/products

curl --location 'localhost:8000/api/products'

```
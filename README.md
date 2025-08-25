# Ecommerce API

Laravel + PostgreSQL tabanlı bir e-ticaret REST API’si. JWT ile authentication, role-based authorization (user/admin), sepet & sipariş akışı, admin CRUD ve e-posta bildirimleri içerir.  
Geliştirme ortamı Docker containerization ile ayağa kalkar.

## Stack & Özellikler

- Laravel (API only style)
- PostgreSQL
- JWT Auth (tymon/jwt-auth)
- MailHog ile email preview (Order confirmation)
- Docker containerization (Nginx + PHP-FPM + Postgres + MailHog)
- Migrations/Seeders/Factories
- Standart JSON Response (ApiResponse)
- Rate limiting: Orders endpoint’i (10 istek/dk)
- Pagination & basic filtering (Products)
- Admin endpoints (stats, order status update, product/category CRUD)

---

## Kurulum (Repo’yu Klonlayanlar İçin)

Bu adımlar, projeyi **sıfırdan klonlayıp** Docker **containerization** ile ayağa kaldırmak ve **hazır demo verileri** (seed) ile çalıştırmak içindir.  
İsterseniz ayrıca **database dump** ile de veriyi insertleyebilirsiniz.

- **Base URL:** `http://localhost:8080`  
- **MailHog:** `http://localhost:8025` (gelen e-postaları burada görürsünüz)

---

## 1) Ön Koşullar
- Docker & Docker Compose
- Git

---

## 2) Repoyu Klonla
```bash
git clone https://github.com/ernydmr/ecommerce.git
cd ecommerce
```

---

## 3) Servisleri Başlat
```bash
docker compose up -d --build
docker compose ps
```

---

## 4) .env Dosyasını Ayarla
```bash
cp src/.env.example src/.env
```

---

## 5) Uygulama Anahtarı & Bağımlılıklar
```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

JWT kurulum:
```bash
docker compose exec app php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
docker compose exec app php artisan jwt:secret
```

---

## 6) Veritabanı & Seed
```bash
docker compose exec app php artisan migrate:fresh --seed
```

Seed ile yüklenen test kullanıcıları:

- Admin → `admin@test.com` / `admin1234`
- User → `user@test.com` / `user1234`

---

## 7) Database Dump ile Kurulum (Opsiyonel)
```bash
docker compose exec -T -e PGPASSWORD=secret db psql -h 127.0.0.1 -U ecommerce -d ecommerce_db -f /dev/stdin < database/dumps/ecommerce_demo.sql
```

---

## 8) Postman ile API Testleri

Repo kökünde `Ecommerce_API_postman.json` koleksiyonu vardır.  
Postman → Import → File ile yükleyebilirsiniz.
- **Koleksiyon içinde Collection Variables olarak bazı değişkenler hazır:
- **base_url, token_user, token_admin, category_id, product_id, order_id.
- **Aşağıdaki sırada istekleri çalıştırırsan token/id değişkenleri otomatik doldurulur:
- **Auth → Login Admin (200)
- **Başarılı olunca koleksiyon testleri token_admin değişkenini set eder.
- **Auth → Login User (200)
- **Başarılı olunca token_user değişkeni set edilir.
- **base_url:** `http://localhost:8080`
---

## 9) Veritabanı Şema Özeti

**users** → (id, name, email, password, role[admin|user], timestamps)  
**categories** → (id, name[unique], description, timestamps)  
**products** → (id, name, price, stock_quantity, category_id, timestamps)  
**carts** → (id, user_id, timestamps)  
**cart_items** → (id, cart_id, product_id, quantity, timestamps)  
**orders** → (id, user_id, total_amount, status[PENDING|PAID|CANCELLED], timestamps)  
**order_items** → (id, order_id, product_id, quantity, price, timestamps)

İlişkiler:
- Category 1-N Product
- User 1-1 Cart, Cart 1-N CartItem
- Order 1-N OrderItem, OrderItem N-1 Product

---

## 10) Response Standardı

Tüm endpoint’ler aynı gövde yapısını döner:
```json
{
  "success": true,
  "message": "Message",
  "data": {},
  "errors": []
}
```

### Başarılı Örnek
```json
{"success": true, "message": "Added to cart", "data": {"id": 1, "items": []}, "errors": []}
```

### Hata Örnekleri
- **401 Unauthorized**
```json
{"success": false, "message": "Unauthenticated", "data": null, "errors": []}
```
- **403 Forbidden**
```json
{"success": false, "message": "Forbidden", "data": null, "errors": []}
```
- **404 Not Found**
```json
{"success": false, "message": "Not Found", "data": null, "errors": []}
```
- **422 Validation**
```json
{"success": false, "message": "Validation Error", "data": null, "errors": {"email": ["Enter a valid email"]}}
```
- **429 Too Many Requests**
```json
{"success": false, "message": "Too many requests", "data": null, "errors": []}
```

---

## 11) Auth & Yetkilendirme

- **JWT:** `POST /api/login` ile alınır → Authorization: Bearer <token>  
- **Roles:** `users.role` alanı admin → admin yetkileri  
- **Rate limiting:** `POST /api/orders` → max 10/dk  

Test Users:
- Admin: `admin@test.com / admin1234`  
- User: `user@test.com / user1234`  

---

## 12) API Endpoint’leri

### 12.1 Auth
- `POST /api/register`
- `POST /api/login`
- `GET /api/profile`
- `PUT /api/profile`

### 12.2 Catalog (Public)
- `GET /api/categories`
- `GET /api/products?search=phone&limit=10&page=1`
- `GET /api/products/{id}`

### 12.3 Admin (Auth + Admin)
- `GET /api/admin/stats`
- `PUT /api/admin/orders/{order_id}/status`
- `POST /api/categories`
- `PUT /api/categories/{id}`
- `DELETE /api/categories/{id}`
- `POST /api/products`
- `PUT /api/products/{id}`
- `DELETE /api/products/{id}`

### 12.4 Cart (Auth)
- `GET /api/cart`
- `POST /api/cart/add`
- `PUT /api/cart/update`
- `DELETE /api/cart/remove/{product_id}`
- `DELETE /api/cart/clear`

### 12.5 Orders (Auth)
- `POST /api/orders`
- `GET /api/orders`
- `GET /api/orders/{id}`

---

## 13) Validation Kuralları
- **Register:** name (min:2), email (unique), password (min:8)  
- **Product Create/Update:** name (min:3), price (min:0.01), stock_quantity (>=1), category_id (exists)  
- **Cart:** product_id (exists), quantity (>=1)  
- **Order Status Update (Admin):** status in [PENDING, PAID, CANCELLED]  

---

## 14) Güvenlik
- Password hashing: bcrypt  
- SQL Injection: Eloquent binding  
- XSS: validation + JSON response  
- Rate limiting: orders 10/dk  
- JWT: stateless auth  

---

## 15) Email (Order Confirmation)
- Event: **OrderCreated**  
- Listener: **SendOrderConfirmation**  
- Mail: **OrderConfirmationMail**  
- Template: `resources/views/mail/order_confirmation.blade.php`  
- MailHog UI: [http://localhost:8025](http://localhost:8025)

---

## 16) Artisan / Yararlı Komutlar
```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan route:list
docker compose exec app php artisan config:cache
docker compose exec app php artisan cache:clear
```

# Order Management API

A simple REST API built with **Laravel** for managing orders, customers, products, and authentication using Laravel Sanctum.

---

## 🚀 Features
- User Registration & Login (Sanctum Authentication)
- Manage Customers
- Manage Products
- Create & Manage Orders
- Validate API requests
- Clean and organized folder structure

---

## 📦 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/vijaya-settipalli/order-management-api.git
cd order-management-api
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Copy Environment File
```bash
cp .env.example .env
```

### 4. Generate App Key
```bash
php artisan key:generate
```

### 5. Configure Database  
Update `.env` with your DB settings:
```
DB_DATABASE=order_management
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Run Migrations
```bash
php artisan migrate
```

---

## 🔐 Authentication (Sanctum)

### Login
```
POST /api/login
```

### Register
```
POST /api/register
```

Use the returned token as:

```
Authorization: Bearer YOUR_TOKEN
```

---

## 📮 API Collection

Postman collection is available in the project:

```
postman_collection.json
```

---

## ▶️ Start Server
```bash
php artisan serve
```

---

## 🧑‍💻 Author
**Vijaya Kiran**

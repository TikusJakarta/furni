# 🛋️ Furni — Online Furniture Marketplace

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

**Furni** is a furniture-focused marketplace web platform built with **Laravel** and **MySQL**. It was developed as a portfolio project during my vocational internship (PKL).

> 🔗 **Live demo:** _(add the link once deployed)_

---

## 📸 Preview

<!-- Replace with real screenshots. Put images in docs/screenshots/ and reference them here. -->

| Home Page | Product Catalog |
| :---: | :---: |
| ![Home](docs/screenshots/home.png) | ![Catalog](docs/screenshots/catalog.png) |

| Shopping Cart | Admin Dashboard |
| :---: | :---: |
| ![Cart](docs/screenshots/cart.png) | ![Admin](docs/screenshots/admin.png) |

---

## 🚀 Features

### 👤 Customer Side
- **Product Catalog** — browse furniture by category, price, and popularity.
- **Shopping Cart** — manage products before checkout.
- **Checkout** — place orders for selected products.

### 🔐 Admin Side
- **Admin Authentication** — protected login to prevent unauthorized access.
- **Product Management (CRUD)** — add, view, edit, and delete products, stock, and prices.
- **Category Management** — organize furniture (e.g. chairs, tables, cabinets).

---

## 🛠️ Tech Stack

| Layer | Technology |
| --- | --- |
| Backend | Laravel (PHP) |
| Database | MySQL + Eloquent ORM |
| Frontend | Blade Templating, _(fill in: Tailwind CSS / Bootstrap / plain CSS)_ |
| Testing | PHPUnit |

---

## 🗄️ Database Structure

| Table | Purpose |
| --- | --- |
| `users` / `admins` | Account data and access rights |
| `products` | Furniture data, prices, images, stock |
| `categories` | Product grouping (one-to-many relationship) |
| `orders` / `carts` | User transactions and shopping carts |

> Adjust this list to match the migrations in `database/migrations`.

---

## 💻 Running Locally

### Prerequisites
- PHP >= 8.2 _(adjust based on `composer.json`)_
- Composer
- MySQL

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/TikusJakarta/furni.git
cd furni

# 2. Install PHP dependencies
composer install

# 3. Copy the environment file
cp .env.example .env

# 4. Generate the application key
php artisan key:generate
```

Then open `.env` and configure your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=furni
DB_USERNAME=root
DB_PASSWORD=
```

Create a `furni` database in MySQL, then continue:

```bash
# 5. Run migrations (add --seed if you have seeders)
php artisan migrate

# 6. Start the Laravel server
php artisan serve
```

Open **http://localhost:8000** in your browser.

### Admin Account (Demo)

_Fill this in if you provide a seeder or demo account._

```
Email    : admin@example.com
Password : ********
```

---

## 🧪 Testing

```bash
php artisan test
```

---

## 📁 Project Structure

```
furni/
├── app/          # Application logic (Models, Controllers, etc.)
├── bootstrap/
├── config/
├── database/     # Migrations, seeders, factories
├── public/       # Entry point & public assets
├── resources/    # Views (Blade)
├── routes/       # Route definitions
├── storage/
└── tests/
```

---

## 🗺️ Roadmap

- [ ] Payment gateway integration
- [ ] Advanced search and filtering
- [ ] Product reviews and ratings
- [ ] User order history

_(Edit to match your actual plans.)_

---

## 👨‍💻 Author

**TikusJakarta**
GitHub: [@TikusJakarta](https://github.com/TikusJakarta)

---

⭐ If you find this project useful, feel free to give it a star!

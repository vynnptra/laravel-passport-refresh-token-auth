```markdown
# Laravel Passport Refresh Token Tutorial

> Learning OAuth2 with Laravel Passport and Refresh Tokens

This is a learning project that demonstrates how to implement **Laravel Passport** with **refresh token** functionality for secure and scalable API authentication in **Laravel 11**.

## 🧠 Features

- Laravel Passport authentication
- Access Token and Refresh Token flow
- Token revocation
- Protected API routes
- User login & logout handling
- Clean and readable code for learning purposes

## 🛠️ Stack

- Laravel 11
- Laravel Passport
- PHP 8.2+
- MySQL or SQLite

## 🚀 Getting Started

### 1. Clone the repo
```bash
git clone https://github.com/vynnptra/laravel-passport-refresh-token-tutorial.git
cd laravel-passport-refresh-token-tutorial
```

### 2. Install dependencies
```bash
composer install
```

### 3. Set up environment
```bash
cp .env.example .env
php artisan key:generate
```

Set your DB credentials in `.env`.

### 4. Run migration and passport install
```bash
php artisan migrate
php artisan passport:install
```

### 5. Serve the app
```bash
php artisan serve
```

## 🔐 How it Works

- Login returns an **access token** and **refresh token**.
- When the access token expires, the refresh token can be used to get a new access token.
- Tokens can be revoked on logout.
- Protected routes require valid access token in headers.

## 📂 Folder Structure

- `app/Http/Controllers/Api/AuthController.php` – Handles login, refresh, logout
- `routes/api.php` – Public and protected API routes

## 📚 Learning Resources

- [Laravel Passport Docs](https://laravel.com/docs/passport)
- [OAuth 2.0 Flow Explained](https://www.digitalocean.com/community/tutorials/an-introduction-to-oauth-2)

## 📌 License

MIT — for educational and personal use.

---

Made with ❤️ by [vynnptra](https://github.com/vynnptra)
```
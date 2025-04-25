# Laravel + Vue Product CRUD SPA

A modern single-page application (SPA) for product management, built with **Laravel** (API backend) and **Vue 3** (frontend). Features include user authentication, product creation/editing with multi-step forms, image uploads, and robust error handling.

---

## Features

- **User Authentication** (Laravel Sanctum)
- **Product CRUD** (Create, Read, Update, Delete)
- **Multi-step Forms** for product creation and editing
- **Image Uploads** with preview and validation
- **Friendly Alerts** for success and error messages
- **SPA Routing** (Vue Router)
- **Rich Text Editing** (Quill Editor)
- **Responsive UI**

---

## Getting Started

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL or compatible database

### Installation

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/yourusername/your-repo.git](https://github.com/yourusername/your-repo.git)
   cd your-repo
2. **Install backend dependencies:**
    ```bash
    composer install
3. **Install frontend dependencies:**
    ```bash
    npm install
4. **Copy and configure environment file:**   
    ```bash
    cp .env.example .env
    
    Set your database credentials in .env
    
    For local SPA auth, set:
    SANCTUM_STATEFUL_DOMAINS=localhost:8000,127.0.0.1:8000,localhost:5173,127.0.0.1:5173
    SESSION_DOMAIN=localhost
5. **Generate app key:**
    ```bash
    php artisan key:generate
6. **Run migrations:**
    ```bash
    php artisan migrate
7. **Create storage symlink:**
    ```bash
    php artisan storage:link

Running the Application:

Start the backend (Laravel):
```bash
php artisan serve

Start the frontend (Vite):
npm run dev

Visit http://localhost:8000 for the app.

Usage:
Register and log in.
Create, edit, or delete products.
Upload and manage product images.
Use multi-step forms for better UX.
Alerts and validation messages will guide you.

Project Structure:
app/Http/Controllers/ – API controllers (Product, Auth)
resources/js/ – Vue components and SPA logic
routes/api.php – API routes
routes/web.php – SPA and web routes
Security & Environment
Uses Laravel Sanctum for SPA authentication.
CORS and session domains configured for local development.

License:
MIT

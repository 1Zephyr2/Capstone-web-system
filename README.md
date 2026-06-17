# FURCARE Project

FURCARE is a modern pet care management system.

## Local Setup for Contributors

To get the project running on your local machine, please follow these steps after cloning the repository:

### 1. Environment Setup
Copy the example environment file and generate your application key:

```bash
cp .env.example .env
php artisan key:generate
```

### 2. Database Initialization
This project uses SQLite for local development. Follow these steps to initialize the database:

```bash
# 1. Create the local database file
touch database/database.sqlite

# 2. Run migrations to create tables
php artisan migrate

# 3. Seed the database with mock users and pets
php artisan db:seed
```

### 3. Running the Development Server
To launch the application, run the following command:

```bash
php artisan serve
```

You can then access the application at `http://127.0.0.1:8000`.

## Notes
- The database file is ignored by Git (`/database/database.sqlite`).
- Staff gateway is accessible at `/staff/login`.
- Staff credentials: `staff@furcare.com` / `password`
- Owner credentials: `owner@furcare.com` / `password`

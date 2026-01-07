# Laravel 12 + Vue 3 CRUD

## Requirements
- PHP 8.2+
- Composer
- Node.js + npm

## Install
```powershell
composer install
npm install
```

## Setup
```powershell
Copy-Item .env.example .env
php artisan key:generate
```

## Database (SQLite default)
```powershell
New-Item -ItemType File -Force -Path database\database.sqlite
php artisan migrate
```

## Run (recommended)
```powershell
composer run dev
```

Open `http://localhost:8000`.

## Run (separate terminals)
```powershell
php artisan serve
npm run dev
```

## Optional: Use MySQL/Postgres
Update `.env` (`DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) before `php artisan migrate`.

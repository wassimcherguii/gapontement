# Installation Guide

**Project:** Dashboard UI Management System  
**Framework:** Laravel 12.0  
**PHP Version:** 8.2+  
**Node.js Version:** 18+

---

## Prerequisites

Before installing, ensure you have the following installed on your system:

- **PHP 8.2 or higher** with extensions:
  - BCMath
  - Ctype
  - cURL
  - DOM
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PCRE
  - PDO
  - Tokenizer
  - XML

- **Composer** (PHP package manager)
- **Node.js 18+** and **npm** (JavaScript package manager)
- **SQLite** (included with PHP) or **MySQL/PostgreSQL** for production
- **Git** (optional, for version control)

---

## Installation Steps

### Step 1: Clone or Download the Project

If using Git:
```bash
git clone <repository-url>
cd Dashboardui_01_11_2025
```

Or download and extract the project to your desired directory.

---

### Step 2: Install PHP Dependencies

Navigate to the project directory and install Composer dependencies:

```bash
composer install
```

This will install all PHP packages defined in `composer.json`.

---

### Step 3: Install Node.js Dependencies

Install JavaScript dependencies:

```bash
npm install
```

This will install all packages defined in `package.json` (Tailwind CSS, Vite, Flowbite, etc.).

---

### Step 4: Environment Configuration

1. **Copy the environment file:**
   ```bash
   cp .env.example .env
   ```
   
   On Windows (PowerShell):
   ```powershell
   Copy-Item .env.example .env
   ```

2. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

3. **Configure your environment variables** in `.env` file:
   
   **Database Configuration:**
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   ```
   
   Or for MySQL/PostgreSQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```
   
   **Application Settings:**
   ```env
   APP_NAME="Your App Name"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8000
   ```
   
   **Mail Configuration** (optional):
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=mailpit
   MAIL_PORT=1025
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS="hello@example.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

---

### Step 5: Database Setup

**For SQLite (Default):**

1. Ensure the database file exists:
   ```bash
   touch database/database.sqlite
   ```
   
   On Windows:
   ```powershell
   New-Item -Path "database\database.sqlite" -ItemType File -Force
   ```

2. Run migrations:
   ```bash
   php artisan migrate
   ```

3. Seed the database with initial data:
   ```bash
   php artisan db:seed
   ```

**For MySQL/PostgreSQL:**

1. Create a database in your database management system
2. Update `.env` with your database credentials
3. Run migrations:
   ```bash
   php artisan migrate
   ```
4. Seed the database:
   ```bash
   php artisan db:seed
   ```

---

### Step 6: Storage Link

Create a symbolic link for public storage:

```bash
php artisan storage:link
```

This links `storage/app/public` to `public/storage` so uploaded files are accessible.

---

### Step 7: Build Frontend Assets

**For Development:**
```bash
npm run dev
```

This starts Vite development server with hot module replacement.

**For Production:**
```bash
npm run build
```

This compiles and minifies all assets for production use.

---

### Step 8: Set Permissions (Linux/Mac)

If you're on Linux or Mac, set proper permissions:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

### Step 9: Start the Development Server

**Option 1: Using Laravel's built-in server**
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

**Option 2: Using the dev script (recommended)**
```bash
composer dev
```

This starts:
- Laravel server (port 8000)
- Queue worker
- Log viewer (Pail)
- Vite dev server

**Option 3: Using Laravel Sail (Docker)**
```bash
./vendor/bin/sail up
```

---

## Quick Setup (One Command)

For a quick setup, you can use the setup script:

```bash
composer setup
```

This runs:
- `composer install`
- Creates `.env` if it doesn't exist
- Generates application key
- Runs migrations
- Installs npm dependencies
- Builds assets

---

## Default Login Credentials

After seeding, you can login with these test accounts:

**Super Admin:**
- Email: `superadmin@example.com`
- Password: `password`
- Role: Full system access

**Admin:**
- Email: `admin@example.com`
- Password: `password`
- Role: Administrative access

**Manager:**
- Email: `manager@example.com`
- Password: `password`
- Role: Management privileges

**Visitor:**
- Email: `visitor@example.com`
- Password: `password`
- Role: Basic access

**Note:** All accounts are created by `UserRoleSeeder`. Change passwords in production!

---

## Accessing the Application

### Public Routes

- **Home:** `http://localhost:8000/en/` (or `/fr/`, `/ar/`)
- **Client Pages:**
  - `http://localhost:8000/en/client`
  - `http://localhost:8000/en/client/products`
  - `http://localhost:8000/en/client/contact`

### Admin Routes

- **Login:** `http://localhost:8000/en/admin/login`
- **Dashboard:** `http://localhost:8000/en/admin/dashboard` (requires login)

### Test Pages

- **i18n Test:** `http://localhost:8000/en/testi18n`
- **RTL Test:** `http://localhost:8000/en/testrtl`
- **Theme Test:** `http://localhost:8000/en/testtheme`
- **Colors Test:** `http://localhost:8000/en/testcolors`
- **Flowbite Test:** `http://localhost:8000/en/testflowbite`

---

## Troubleshooting

### Issue: "Class not found" errors

**Solution:**
```bash
composer dump-autoload
```

### Issue: Routes not working

**Solution:**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
-- php artisan optimize:clear
```
### Issue: Assets not loading

**Solution:**
1. Make sure you ran `npm install`
2. Run `npm run build` or `npm run dev`
3. Clear view cache: `php artisan view:clear`

### Issue: Permission denied errors

**Solution (Linux/Mac):**
```bash
chmod -R 775 storage bootstrap/cache
```

### Issue: Database connection errors

**Solution:**
1. Check `.env` file has correct database credentials
2. For SQLite, ensure `database/database.sqlite` exists
3. For MySQL/PostgreSQL, ensure database exists and credentials are correct

### Issue: "Vite manifest not found"

**Solution:**
```bash
npm run build
```

Or if in development:
```bash
npm run dev
```

### Issue: Language not switching

**Solution:**
1. Clear all caches: `php artisan optimize:clear`
2. Clear browser cache
3. Check `jsonassets/languages.json` exists

---

## Development Workflow

### Daily Development

1. Start the development server:
   ```bash
   composer dev
   ```
   
   Or separately:
   ```bash
   php artisan serve
   npm run dev
   ```

2. Make your changes

3. Test in browser at `http://localhost:8000`

### After Pulling Updates

1. Update dependencies:
   ```bash
   composer install
   npm install
   ```

2. Run migrations (if any):
   ```bash
   php artisan migrate
   ```

3. Clear caches:
   ```bash
   php artisan optimize:clear
   ```

4. Rebuild assets:
   ```bash
   npm run build
   ```

---

## Production Deployment

### Pre-Deployment Checklist

1. **Set environment to production:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize for production:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```

3. **Build assets:**
   ```bash
   npm run build
   ```

4. **Set proper permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

5. **Run migrations:**
   ```bash
   php artisan migrate --force
   ```

### Server Requirements

- PHP 8.2+
- Composer
- Node.js 18+ (for building assets)
- Web server (Apache/Nginx)
- Database (MySQL/PostgreSQL recommended for production)

---

## Additional Resources

- **Laravel Documentation:** https://laravel.com/docs
- **Tailwind CSS Documentation:** https://tailwindcss.com/docs
- **Flowbite Documentation:** https://flowbite.com/docs
- **Vite Documentation:** https://vitejs.dev

---

## Support

If you encounter any issues during installation:

1. Check the **Troubleshooting** section above
2. Review Laravel logs: `storage/logs/laravel.log`
3. Check PHP error logs
4. Ensure all prerequisites are installed correctly

---

## Next Steps

After successful installation:

1. ✅ Login to admin panel
2. ✅ Configure company information (`/en/admin/assets/company`)
3. ✅ Upload brand assets (`/en/admin/assets/brand`)
4. ✅ Customize colors (`/en/admin/assets/colors`)
5. ✅ Set default language (`/en/admin/assets/languages`)
6. ✅ Explore the features!

---

**Installation Date:** January 11, 2025  
**Project Version:** Laravel 12.0  
**Status:** Ready for Development

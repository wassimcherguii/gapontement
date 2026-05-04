# Mobile App API Requirements

**Date:** February 20, 2026  
**Status:** 📋 **PLANNING**

---

## Summary

The current backend is **web-only** and lacks the necessary API infrastructure to support mobile applications. This document outlines what needs to be added to enable mobile app support.

---

## Current State Analysis

### ✅ What Exists:
- ✅ Laravel 12.0 framework
- ✅ User authentication (session-based)
- ✅ Role-based access control (visitor, manager, admin, superadmin)
- ✅ Database models and migrations
- ✅ Controllers for web routes
- ✅ Multi-language support (en, fr, ar)
- ✅ JSON asset management (colors, languages, brand)

### ❌ What's Missing for Mobile:
- ❌ **API routes** (`routes/api.php` doesn't exist)
- ❌ **API authentication** (no Sanctum/Passport)
- ❌ **API controllers** (only web controllers exist)
- ❌ **CORS configuration** (for cross-origin requests)
- ❌ **API versioning** (`/api/v1/`)
- ❌ **API documentation** (Swagger/OpenAPI)
- ❌ **API response formatting** (consistent JSON structure)
- ❌ **API rate limiting** (per user/IP)
- ❌ **Token-based authentication** (mobile apps can't use sessions)
- ❌ **Push notification support**
- ❌ **Mobile-specific endpoints**

---

## Required Components

### 1. **API Authentication System** 🔐

**Priority:** 🔴 **CRITICAL**

**What's Needed:**
- Install Laravel Sanctum or Passport
- Token-based authentication for mobile apps
- API token generation and management
- Token refresh mechanism
- Device registration/tracking

**Implementation:**
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

**Files to Create:**
- `app/Http/Controllers/Api/AuthController.php`
- `app/Http/Controllers/Api/TokenController.php`
- `app/Http/Middleware/EnsureApiToken.php`

---

### 2. **API Routes Structure** 🛣️

**Priority:** 🔴 **CRITICAL**

**What's Needed:**
- Create `routes/api.php`
- API versioning (`/api/v1/`)
- RESTful endpoint structure
- Separate API routes from web routes

**Structure:**
```
/api/v1/
  ├── /auth
  │   ├── POST   /login
  │   ├── POST   /register
  │   ├── POST   /logout
  │   ├── POST   /refresh-token
  │   └── GET    /me
  ├── /users
  │   ├── GET    /users
  │   ├── GET    /users/{id}
  │   ├── PUT    /users/{id}
  │   └── DELETE /users/{id}
  ├── /colors
  │   ├── GET    /colors
  │   ├── GET    /colors/{id}
  │   └── PUT    /colors/{id}
  ├── /brand
  │   ├── GET    /brand
  │   └── POST   /brand/upload-logo
  └── /settings
      ├── GET    /settings
      └── PUT    /settings
```

**Files to Create:**
- `routes/api.php`
- Update `bootstrap/app.php` to register API routes

---

### 3. **API Controllers** 🎮

**Priority:** 🔴 **CRITICAL**

**What's Needed:**
- Separate API controllers from web controllers
- Consistent JSON response format
- Proper error handling
- Resource transformers/formatters

**Controllers to Create:**
- `app/Http/Controllers/Api/AuthController.php`
- `app/Http/Controllers/Api/UserController.php`
- `app/Http/Controllers/Api/ColorController.php`
- `app/Http/Controllers/Api/BrandController.php`
- `app/Http/Controllers/Api/SettingsController.php`
- `app/Http/Controllers/Api/LanguageController.php`

**Response Format:**
```json
{
  "success": true,
  "data": { ... },
  "message": "Operation successful",
  "meta": {
    "timestamp": "2026-02-20T10:00:00Z",
    "version": "v1"
  }
}
```

---

### 4. **CORS Configuration** 🌐

**Priority:** 🟡 **HIGH**

**What's Needed:**
- Configure CORS for mobile app domains
- Allow credentials
- Set allowed methods and headers

**Files to Update:**
- `config/cors.php` (publish if needed)

**Configuration:**
```php
'paths' => ['api/*'],
'allowed_origins' => [
    'http://localhost:3000',  // React Native dev
    'https://your-mobile-app.com',
],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

---

### 5. **API Response Formatting** 📦

**Priority:** 🟡 **HIGH**

**What's Needed:**
- Consistent JSON response structure
- Error response formatting
- Success response formatting
- API resource classes (optional but recommended)

**Files to Create:**
- `app/Http/Resources/ApiResponse.php` (helper trait)
- `app/Http/Resources/UserResource.php`
- `app/Http/Resources/ColorResource.php`
- `app/Http/Resources/ErrorResource.php`

**Example:**
```php
trait ApiResponse {
    protected function success($data, $message = null, $code = 200) {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'timestamp' => now()->toIso8601String()
        ], $code);
    }
    
    protected function error($message, $code = 400, $errors = null) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String()
        ], $code);
    }
}
```

---

### 6. **API Middleware** 🛡️

**Priority:** 🟡 **HIGH**

**What's Needed:**
- API authentication middleware
- API rate limiting
- API versioning middleware
- Request logging middleware

**Files to Create:**
- `app/Http/Middleware/ApiAuthenticate.php`
- `app/Http/Middleware/ApiVersion.php`
- `app/Http/Middleware/ApiLogging.php`

---

### 7. **API Rate Limiting** ⏱️

**Priority:** 🟡 **HIGH**

**What's Needed:**
- Rate limiting per user/IP
- Different limits for authenticated vs guest
- Throttle specific endpoints

**Configuration:**
```php
// In routes/api.php
Route::middleware(['throttle:60,1'])->group(function () {
    // Public endpoints
});

Route::middleware(['throttle:api'])->group(function () {
    // Authenticated endpoints
});
```

---

### 8. **API Documentation** 📚

**Priority:** 🟢 **MEDIUM**

**What's Needed:**
- API documentation (Swagger/OpenAPI)
- Endpoint descriptions
- Request/response examples
- Authentication instructions

**Options:**
- **Laravel Scribe** (recommended for Laravel)
- **L5-Swagger** (Swagger UI)
- **API Blueprint**

**Installation:**
```bash
composer require knuckleswtf/scribe
php artisan vendor:publish --tag=scribe-config
php artisan scribe:generate
```

---

### 9. **Mobile-Specific Features** 📱

**Priority:** 🟢 **MEDIUM**

**What's Needed:**
- Push notification support
- Device registration
- Offline data sync endpoints
- Image optimization for mobile
- File upload endpoints optimized for mobile

**Files to Create:**
- `app/Http/Controllers/Api/NotificationController.php`
- `app/Http/Controllers/Api/DeviceController.php`
- `app/Models/Device.php` (migration)

---

### 10. **API Versioning** 🔢

**Priority:** 🟢 **MEDIUM**

**What's Needed:**
- Version prefix in routes (`/api/v1/`)
- Version header support
- Backward compatibility strategy

**Implementation:**
```php
// routes/api.php
Route::prefix('v1')->group(function () {
    // v1 routes
});

Route::prefix('v2')->group(function () {
    // v2 routes (future)
});
```

---

### 11. **Error Handling** ⚠️

**Priority:** 🟡 **HIGH**

**What's Needed:**
- Consistent error response format
- Proper HTTP status codes
- Validation error formatting
- Exception handling for API

**Files to Update:**
- `app/Exceptions/Handler.php`
- Create API-specific exception handlers

**Error Format:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 6 characters."]
  },
  "code": 422,
  "timestamp": "2026-02-20T10:00:00Z"
}
```

---

### 12. **Testing** 🧪

**Priority:** 🟢 **MEDIUM**

**What's Needed:**
- API endpoint tests
- Authentication tests
- Rate limiting tests
- Integration tests

**Files to Create:**
- `tests/Feature/Api/AuthTest.php`
- `tests/Feature/Api/UserTest.php`
- `tests/Feature/Api/ColorTest.php`

---

## Implementation Priority

### Phase 1: Core API (Week 1-2) 🔴
1. Install Laravel Sanctum
2. Create `routes/api.php`
3. Create API authentication endpoints
4. Create basic API controllers
5. Configure CORS
6. Implement API response formatting

### Phase 2: Features (Week 3-4) 🟡
7. Create all API endpoints
8. Implement rate limiting
9. Add API middleware
10. Error handling
11. API versioning

### Phase 3: Polish (Week 5-6) 🟢
12. API documentation
13. Mobile-specific features
14. Testing
15. Performance optimization

---

## Dependencies to Install

```bash
# API Authentication
composer require laravel/sanctum

# API Documentation (optional)
composer require knuckleswtf/scribe

# API Testing (optional)
composer require --dev phpunit/phpunit
```

---

## Configuration Changes

### 1. Update `bootstrap/app.php`:
```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',  // Add this
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

### 2. Update `config/auth.php`:
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'sanctum' => [  // Add this
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
],
```

### 3. Update `config/sanctum.php`:
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', '')),
'middleware' => [
    'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
    'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
],
```

---

## Example API Endpoints

### Authentication
```php
POST   /api/v1/auth/login
POST   /api/v1/auth/register
POST   /api/v1/auth/logout
POST   /api/v1/auth/refresh
GET    /api/v1/auth/me
```

### Users
```php
GET    /api/v1/users
GET    /api/v1/users/{id}
PUT    /api/v1/users/{id}
DELETE /api/v1/users/{id}
```

### Colors
```php
GET    /api/v1/colors
GET    /api/v1/colors/{id}
PUT    /api/v1/colors/{id}
```

### Brand
```php
GET    /api/v1/brand
POST   /api/v1/brand/logo
POST   /api/v1/brand/favicon
```

---

## Security Considerations

1. **Token Expiration**: Set appropriate token expiration times
2. **HTTPS Only**: Enforce HTTPS in production
3. **Rate Limiting**: Prevent abuse
4. **Input Validation**: Validate all API inputs
5. **SQL Injection**: Use Eloquent (already done)
6. **XSS Protection**: Sanitize outputs
7. **CORS**: Configure properly for mobile domains only

---

## Next Steps

1. ✅ Review this document
2. ⬜ Install Laravel Sanctum
3. ⬜ Create `routes/api.php`
4. ⬜ Create API authentication endpoints
5. ⬜ Create API controllers
6. ⬜ Configure CORS
7. ⬜ Test with mobile app client

---

## Notes

- The current web authentication uses sessions, which won't work for mobile apps
- Mobile apps need token-based authentication (Sanctum/Passport)
- All API responses should be JSON
- Consider pagination for list endpoints
- Implement caching for frequently accessed data
- Add API monitoring and logging

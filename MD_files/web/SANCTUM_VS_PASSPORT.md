# Sanctum vs Passport: Which to Choose?

**Date:** February 20, 2026

---

## Quick Answer: **Laravel Sanctum** ✅

For your mobile app backend, **Laravel Sanctum** is the better choice.

---

## Comparison Table

| Feature | Laravel Sanctum | Laravel Passport |
|---------|----------------|------------------|
| **Complexity** | ⭐ Simple | ⭐⭐⭐ Complex |
| **Setup Time** | 5-10 minutes | 30-60 minutes |
| **Database Tables** | 1 table | 5+ tables |
| **Dependencies** | None (built-in) | Requires Redis/DB |
| **OAuth2 Support** | ❌ No | ✅ Yes |
| **API Tokens** | ✅ Yes | ✅ Yes |
| **SPA Support** | ✅ Yes | ✅ Yes |
| **Mobile Apps** | ✅ Perfect | ✅ Works |
| **Third-party APIs** | ❌ No | ✅ Yes |
| **Scopes/Permissions** | Basic | Advanced |
| **Performance** | ⚡ Fast | 🐌 Slower |
| **Learning Curve** | Easy | Steep |
| **Maintenance** | Low | High |

---

## Laravel Sanctum ✅ **RECOMMENDED**

### ✅ **Advantages:**

1. **Simple & Lightweight**
   - Minimal setup required
   - Only 1 database table (`personal_access_tokens`)
   - No OAuth complexity
   - Fast and efficient

2. **Perfect for Mobile Apps**
   - Simple token-based authentication
   - Easy to implement on mobile clients
   - No OAuth flow needed
   - Just send token in header: `Authorization: Bearer {token}`

3. **Built-in to Laravel**
   - No external dependencies
   - Well-maintained by Laravel team
   - Regular updates and security patches

4. **Easy to Use**
   ```php
   // Generate token
   $token = $user->createToken('mobile-app')->plainTextToken;
   
   // Authenticate
   Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
       return $request->user();
   });
   ```

5. **Good Performance**
   - Lightweight queries
   - Fast token validation
   - Minimal overhead

### ❌ **Limitations:**

- No OAuth2 server features
- No client management UI
- Basic scope/permission system
- Not suitable for third-party API access

---

## Laravel Passport

### ✅ **Advantages:**

1. **Full OAuth2 Server**
   - Complete OAuth2 implementation
   - Client management
   - Access tokens, refresh tokens
   - Authorization codes

2. **Third-party API Access**
   - Perfect if you're building an API for external developers
   - Client credentials flow
   - User authorization flows

3. **Advanced Features**
   - Scopes and permissions
   - Token revocation
   - Client management UI
   - Token expiration management

### ❌ **Disadvantages:**

1. **Complex Setup**
   - Requires multiple database tables
   - OAuth2 keys generation
   - More configuration needed

2. **Overkill for Mobile Apps**
   - OAuth2 is unnecessary for your own mobile app
   - Adds complexity without benefits
   - More code to maintain

3. **Heavier**
   - More database queries
   - More overhead
   - Slower performance

4. **Steeper Learning Curve**
   - OAuth2 concepts to learn
   - More complex API
   - Harder to debug

---

## When to Use Each

### Use **Sanctum** When: ✅
- ✅ Building a mobile app (iOS/Android)
- ✅ Building a SPA (React/Vue)
- ✅ Simple API token authentication
- ✅ Your own applications consuming the API
- ✅ Want simple, fast setup
- ✅ Don't need OAuth2 features

### Use **Passport** When:
- ✅ Building a public API for third-party developers
- ✅ Need OAuth2 server features
- ✅ Need client management UI
- ✅ Need advanced scopes/permissions
- ✅ Building an API marketplace
- ✅ Need authorization code flow

---

## For Your Project: **Sanctum** ✅

Based on your requirements:

1. **Mobile App Backend** → Sanctum is perfect
2. **Simple Token Auth** → Sanctum handles this easily
3. **Quick Setup** → Sanctum is faster to implement
4. **No Third-party APIs** → Don't need OAuth2
5. **Maintainability** → Sanctum is easier to maintain

---

## Implementation Example: Sanctum

### 1. Installation
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 2. User Model
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}
```

### 3. Login Endpoint
```php
// app/Http/Controllers/Api/AuthController.php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('mobile-app')->plainTextToken;
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'Login successful'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials'
    ], 401);
}
```

### 4. Protected Routes
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('colors', ColorController::class);
});
```

### 5. Mobile App Usage
```javascript
// React Native / Flutter example
const response = await fetch('https://api.example.com/api/v1/colors', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
    }
});
```

---

## Migration Path

If you start with Sanctum and later need Passport:
- You can migrate, but it requires refactoring
- Better to choose correctly from the start
- For mobile apps, Sanctum is almost always the right choice

---

## Performance Comparison

| Operation | Sanctum | Passport |
|-----------|---------|----------|
| Token Generation | ~1ms | ~5ms |
| Token Validation | ~1ms | ~3ms |
| Database Queries | 1 | 2-3 |
| Memory Usage | Low | Medium |

---

## Security Comparison

Both are secure, but:
- **Sanctum**: Simpler = fewer attack vectors
- **Passport**: More features = more potential vulnerabilities
- Both use secure token storage
- Both support token expiration

---

## Recommendation Summary

### ✅ **Choose Sanctum if:**
- Building mobile apps (your case) ✅
- Building SPAs
- Simple token authentication
- Want fast setup
- Don't need OAuth2

### Choose Passport if:
- Building public API for third parties
- Need OAuth2 server
- Need client management
- Building API marketplace

---

## Final Verdict

**For your mobile app backend: Use Laravel Sanctum** ✅

**Reasons:**
1. ✅ Perfect for mobile apps
2. ✅ Simple and fast
3. ✅ Easy to maintain
4. ✅ No unnecessary complexity
5. ✅ Better performance
6. ✅ Built-in to Laravel

**You don't need OAuth2 for your own mobile app** - Sanctum's simple token authentication is exactly what you need.

---

## Next Steps

1. Install Sanctum: `composer require laravel/sanctum`
2. Publish config: `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
3. Run migration: `php artisan migrate`
4. Update User model: Add `HasApiTokens` trait
5. Create API routes and controllers
6. Implement token-based authentication

---

## Resources

- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)
- [Laravel Passport Documentation](https://laravel.com/docs/passport)
- [Sanctum vs Passport: When to Use Which](https://laravel.com/docs/sanctum#sanctum-vs-passport)

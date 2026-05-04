# Mobile App Setup Guide

**Date:** February 20, 2026  
**Status:** ✅ **READY**

---

## Project Structure

```
web_mobile/
├── Dashboardui_Backend_API/    # Laravel Backend
└── MobileApp/                   # React Native Expo App
    ├── App.js
    ├── config/
    │   └── api.js              # API configuration
    ├── services/
    │   └── api.js              # API service
    └── package.json
```

---

## Quick Start

### 1. Start Backend Server

```bash
cd Dashboardui_Backend_API
php artisan serve
```

Backend will run on: `http://localhost:8000`

### 2. Start Mobile App

```bash
cd MobileApp
npm start
```

Then:
- Press `a` for Android
- Press `i` for iOS
- Press `w` for Web
- Scan QR code with Expo Go app

---

## API Configuration

The API is configured in `MobileApp/config/api.js`:

```javascript
export const API_BASE_URL = __DEV__
  ? 'http://localhost:8000/api/v1'  // Development
  : 'https://your-production-api.com/api/v1';  // Production
```

**Important for Android:**
- Use `http://10.0.2.2:8000` instead of `localhost` for Android emulator
- Or use your computer's local IP address (e.g., `http://192.168.1.100:8000`)

---

## Available API Methods

The `ApiService` provides these methods:

### Authentication
- `ApiService.login(email, password)`
- `ApiService.register(name, email, password, passwordConfirmation)`
- `ApiService.logout()`
- `ApiService.getCurrentUser()`

### Users
- `ApiService.getUsers(params)`
- `ApiService.getUser(id)`
- `ApiService.updateUser(id, data)`

### Colors
- `ApiService.getColors(params)` - params: `{ theme: 'light'|'dark', category: '...' }`
- `ApiService.getColor(id)`
- `ApiService.updateColor(id, hexValue)`

### Brand
- `ApiService.getBrand()`

### Settings
- `ApiService.getSettings()`
- `ApiService.getLanguages()`
- `ApiService.getSettingsColors()`

---

## Example Usage

```javascript
import ApiService from './services/api';

// Login
try {
  const response = await ApiService.login('admin@example.com', 'password');
  if (response.success) {
    console.log('User:', response.data.user);
    console.log('Token:', response.data.token);
  }
} catch (error) {
  console.error('Login failed:', error);
}

// Get colors
try {
  const response = await ApiService.getColors({ theme: 'light' });
  if (response.success) {
    console.log('Colors:', response.data);
  }
} catch (error) {
  console.error('Failed to fetch colors:', error);
}
```

---

## Testing

1. **Start Backend:**
   ```bash
   cd Dashboardui_Backend_API
   php artisan serve
   ```

2. **Start Mobile App:**
   ```bash
   cd MobileApp
   npm start
   ```

3. **Test Login:**
   - Open the app
   - Click "Test Login" button
   - Use valid credentials from your database

---

## Troubleshooting

### Connection Issues

**Problem:** Can't connect to backend  
**Solution:**
- Check if backend is running: `php artisan serve`
- For Android emulator, use `http://10.0.2.2:8000`
- For physical device, use your computer's IP: `http://192.168.1.X:8000`
- Check CORS configuration in `Dashboardui_Backend_API/config/cors.php`

### Authentication Issues

**Problem:** Token not persisting  
**Solution:**
- AsyncStorage is installed and configured
- Check if token is being saved: `await ApiService.getToken()`

### API Errors

**Problem:** 401 Unauthorized  
**Solution:**
- Token might be expired
- Try logging in again
- Check if token is being sent in headers

---

## Next Steps

1. ✅ App structure created
2. ✅ API service configured
3. ⬜ Create authentication screens
4. ⬜ Create main app screens
5. ⬜ Add navigation (React Navigation)
6. ⬜ Style the app
7. ⬜ Add error handling UI
8. ⬜ Add loading states

---

## Dependencies

Current dependencies:
- `expo` - Expo framework
- `react-native` - React Native
- `@react-native-async-storage/async-storage` - Token storage

Recommended additions:
- `@react-navigation/native` - Navigation
- `@react-navigation/stack` - Stack navigation
- `axios` - Alternative to fetch (optional)

---

## Notes

- The app automatically stores authentication tokens
- All API requests include the token in headers
- Error handling is built into the ApiService
- The app checks for existing authentication on startup

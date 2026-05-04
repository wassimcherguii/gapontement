# Mobile App

React Native Expo app connected to Laravel backend API.

## Setup

1. Install dependencies:
```bash
npm install
```

2. Configure API URL:
   - Edit `config/api.js`
   - Update `API_BASE_URL` to match your backend URL

3. Start the development server:
```bash
npm start
```

## Available Scripts

- `npm start` - Start Expo development server
- `npm run android` - Run on Android device/emulator
- `npm run ios` - Run on iOS device/simulator (macOS only)
- `npm run web` - Run in web browser

## Project Structure

```
MobileApp/
├── App.js              # Main app component
├── config/
│   └── api.js         # API configuration
├── services/
│   └── api.js         # API service (handles all API calls)
├── assets/            # Images and other assets
└── package.json       # Dependencies
```

## API Integration

The app uses the `ApiService` class to communicate with the Laravel backend:

```javascript
import ApiService from './services/api';

// Login
const response = await ApiService.login('email@example.com', 'password');

// Get current user
const user = await ApiService.getCurrentUser();

// Get colors
const colors = await ApiService.getColors({ theme: 'light' });

// Logout
await ApiService.logout();
```

## Backend Connection

Make sure your Laravel backend is running:
```bash
cd Dashboardui_Backend_API
php artisan serve
```

The backend should be accessible at `http://localhost:8000`

## Testing

1. Start the backend server
2. Start the Expo app: `npm start`
3. Scan the QR code with Expo Go app (iOS/Android)
4. Test the login functionality

## Notes

- The app uses AsyncStorage to persist authentication tokens
- API base URL is configured in `config/api.js`
- All API requests include authentication headers automatically
- Error handling is built into the ApiService

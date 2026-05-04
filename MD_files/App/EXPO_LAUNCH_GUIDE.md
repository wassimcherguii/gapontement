# Expo App Launch Guide

**Date:** February 20, 2026

---

## Prerequisites

Before launching the app, make sure you have:

1. ✅ Node.js installed (v18 or higher recommended)
2. ✅ npm or yarn installed
3. ✅ Expo CLI (installed globally or via npx)
4. ✅ Expo Go app on your phone (iOS/Android) - Optional but recommended
5. ✅ Backend server running (for API calls)

---

## Step-by-Step Launch Instructions

### Step 1: Navigate to Mobile App Directory

```bash
cd MobileApp
```

### Step 2: Install Dependencies (First Time Only)

If you haven't installed dependencies yet:

```bash
npm install
```

This installs all required packages including:
- Expo framework
- React Native
- AsyncStorage
- Other dependencies

### Step 3: Start the Backend Server (Required for API)

In a **separate terminal**, start your Laravel backend:

```bash
cd Dashboardui_Backend_API
php artisan serve
```

The backend will run on: `http://localhost:8000`

**Keep this terminal running!**

### Step 4: Start Expo Development Server

In the MobileApp directory, run:

```bash
npm start
```

Or:

```bash
npx expo start
```

### Step 5: Choose Your Platform

After running `npm start`, you'll see a QR code and menu options:

```
› Metro waiting on exp://192.168.1.100:8081
› Scan the QR code above with Expo Go (Android) or the Camera app (iOS)

› Press a │ open Android
› Press i │ open iOS simulator
› Press w │ open web

› Press r │ reload app
› Press m │ toggle menu
› Press o │ open project code in your editor
› Press ? │ show all commands
```

**Options:**

#### Option A: Run on Physical Device (Recommended for Testing)

1. **For iOS:**
   - Open Camera app on iPhone
   - Scan the QR code
   - Tap the notification to open in Expo Go

2. **For Android:**
   - Open Expo Go app
   - Tap "Scan QR code"
   - Scan the QR code from terminal

#### Option B: Run on Emulator/Simulator

1. **For Android:**
   - Press `a` in the terminal
   - Or run: `npm run android`
   - Requires Android Studio and emulator set up

2. **For iOS (macOS only):**
   - Press `i` in the terminal
   - Or run: `npm run ios`
   - Requires Xcode and iOS Simulator

#### Option C: Run in Web Browser

- Press `w` in the terminal
- Or run: `npm run web`
- Opens in default browser

---

## Quick Commands Reference

```bash
# Start development server
npm start

# Start with specific platform
npm run android    # Android emulator
npm run ios        # iOS simulator (macOS only)
npm run web        # Web browser

# Clear cache and restart
npx expo start --clear
```

---

## Troubleshooting

### Issue: "Port 8081 already in use"

**Solution:**
```bash
# Kill the process using port 8081
# Windows:
netstat -ano | findstr :8081
taskkill /PID <PID> /F

# Or use a different port:
npx expo start --port 8082
```

### Issue: Can't connect to backend API

**Solution:**
1. Make sure backend is running: `php artisan serve`
2. For Android emulator, update `config/api.js`:
   ```javascript
   export const API_BASE_URL = 'http://10.0.2.2:8000/api/v1';
   ```
3. For physical device, use your computer's IP:
   ```javascript
   export const API_BASE_URL = 'http://192.168.1.100:8000/api/v1';
   ```
   (Replace with your actual IP address)

### Issue: "Expo Go not found" or "Unable to connect"

**Solution:**
1. Make sure phone and computer are on the same WiFi network
2. Try using tunnel mode:
   ```bash
   npx expo start --tunnel
   ```
3. Or use LAN mode explicitly:
   ```bash
   npx expo start --lan
   ```

### Issue: App crashes on startup

**Solution:**
1. Clear cache:
   ```bash
   npx expo start --clear
   ```
2. Reinstall dependencies:
   ```bash
   rm -rf node_modules
   npm install
   ```
3. Check console for error messages

### Issue: Metro bundler errors

**Solution:**
```bash
# Clear Metro cache
npx expo start --clear

# Reset watchman (if installed)
watchman watch-del-all

# Clear npm cache
npm start -- --reset-cache
```

---

## Development Workflow

### Typical Development Session:

1. **Terminal 1 - Backend:**
   ```bash
   cd Dashboardui_Backend_API
   php artisan serve
   ```

2. **Terminal 2 - Mobile App:**
   ```bash
   cd MobileApp
   npm start
   ```

3. **In Expo:**
   - Scan QR code or press platform key
   - App opens on device/emulator
   - Make code changes
   - App auto-reloads (Hot Reload)

### Hot Reload

- Changes to code automatically reload the app
- Press `r` in terminal to manually reload
- Press `m` to toggle developer menu
- Shake device or press `Cmd+D` (iOS) / `Cmd+M` (Android) for dev menu

---

## Network Configuration

### For Android Emulator:
- Use `10.0.2.2` instead of `localhost`
- Update `config/api.js`:
  ```javascript
  export const API_BASE_URL = 'http://10.0.2.2:8000/api/v1';
  ```

### For Physical Device:
- Use your computer's local IP address
- Find your IP:
  - **Windows:** `ipconfig` (look for IPv4 Address)
  - **macOS/Linux:** `ifconfig` or `ip addr`
- Update `config/api.js`:
  ```javascript
  export const API_BASE_URL = 'http://192.168.1.100:8000/api/v1';
  ```

### For iOS Simulator:
- Use `localhost` (works directly)
- No changes needed

---

## Production Build

When ready to build for production:

```bash
# Build for Android
npx expo build:android

# Build for iOS
npx expo build:ios

# Or use EAS Build (recommended)
npm install -g eas-cli
eas build --platform android
eas build --platform ios
```

---

## Useful Expo Commands

```bash
# Start with specific options
npx expo start --tunnel    # Use tunnel (works across networks)
npx expo start --lan        # Use LAN only
npx expo start --localhost  # Localhost only
npx expo start --clear      # Clear cache

# Development tools
npx expo install <package>  # Install Expo-compatible package
npx expo doctor            # Check for common issues

# Build and publish
npx expo publish            # Publish OTA update
```

---

## Next Steps After Launch

1. ✅ App is running
2. ⬜ Test API connection (click "Test Login" button)
3. ⬜ Verify authentication works
4. ⬜ Test API endpoints
5. ⬜ Start building your app screens

---

## Notes

- The development server must stay running while developing
- Changes to code trigger automatic reload
- Use Expo Go app for quick testing (no build required)
- For production, you'll need to create standalone builds
- Keep backend server running for API calls to work

---

## Quick Reference Card

```
┌─────────────────────────────────────┐
│  EXPO LAUNCH QUICK REFERENCE        │
├─────────────────────────────────────┤
│  1. cd MobileApp                    │
│  2. npm install (first time)        │
│  3. npm start                       │
│  4. Press:                          │
│     - a = Android                   │
│     - i = iOS                       │
│     - w = Web                       │
│     - Scan QR = Physical device     │
└─────────────────────────────────────┘
```

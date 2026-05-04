/**
 * API Configuration
 * 
 * This file contains the API base URL and configuration
 * for connecting to the Laravel backend.
 */

// Development API URL (change this to your backend URL)
// For Android emulator, use 10.0.2.2 instead of localhost
// For iOS simulator, use localhost
// For physical device, use your computer's IP address
import { Platform } from 'react-native';

// Your PC LAN IP for Expo Go on a real phone (same Wi-Fi network)
const DEV_LAN_IP = '192.168.1.2';

const getApiBaseUrl = () => {
  if (__DEV__) {
    if (Platform.OS === 'android') {
      // Expo Go on a physical Android device should use your PC LAN IP
      return `http://${DEV_LAN_IP}:8000/api/v1`;
    } else if (Platform.OS === 'ios') {
      // Physical iPhone on same Wi-Fi should also use LAN IP
      return `http://${DEV_LAN_IP}:8000/api/v1`;
    } else {
      // Web or other platforms
      return 'http://localhost:8000/api/v1';
    }
  }
  return 'https://your-production-api.com/api/v1';
};

export const API_BASE_URL = getApiBaseUrl();

// API Endpoints
export const API_ENDPOINTS = {
  // Authentication
  AUTH: {
    LOGIN: '/auth/login',
    REGISTER: '/auth/register',
    LOGOUT: '/auth/logout',
    ME: '/auth/me',
  },
  // Users
  USERS: {
    LIST: '/users',
    SHOW: (id) => `/users/${id}`,
    UPDATE: (id) => `/users/${id}`,
  },
  // Colors
  COLORS: {
    LIST: '/colors',
    SYNC: '/colors/sync',
    SHOW: (id) => `/colors/${id}`,
    UPDATE: (id) => `/colors/${id}`,
  },
  // Brand
  BRAND: {
    INDEX: '/brand',
    UPLOAD_LOGO: '/brand/logo',
    UPLOAD_FAVICON: '/brand/favicon',
  },
  // Settings
  SETTINGS: {
    INDEX: '/settings',
    LANGUAGES: '/settings/languages',
    COLORS: '/settings/colors',
    APP_INFO: '/app-info',
    COMPANY: '/company',
    BRAND_ASSETS: '/brand-assets',
  },
  I18N: {
    LANGUAGES: (domain) => `/i18n/${domain}/languages`,
    BUNDLE: (domain, locale) => `/i18n/${domain}/${locale}`,
  },
};

// API Request Headers
export const getHeaders = (token = null) => {
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  return headers;
};

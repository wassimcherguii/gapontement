import { API_BASE_URL, API_ENDPOINTS, getHeaders } from '../config/api';
import AsyncStorage from '@react-native-async-storage/async-storage';

const TOKEN_KEY = '@auth_token';

/**
 * API Service
 * Handles all API requests to the Laravel backend
 */
class ApiService {
  /**
   * Get stored authentication token
   */
  async getToken() {
    try {
      return await AsyncStorage.getItem(TOKEN_KEY);
    } catch (error) {
      console.error('Error getting token:', error);
      return null;
    }
  }

  /**
   * Store authentication token
   */
  async setToken(token) {
    try {
      await AsyncStorage.setItem(TOKEN_KEY, token);
    } catch (error) {
      console.error('Error storing token:', error);
    }
  }

  /**
   * Remove authentication token
   */
  async removeToken() {
    try {
      await AsyncStorage.removeItem(TOKEN_KEY);
    } catch (error) {
      console.error('Error removing token:', error);
    }
  }

  /**
   * Make API request
   * @param {boolean} requireAuth - Whether to require authentication (default: true)
   */
  async request(endpoint, options = {}, requireAuth = true) {
    const token = requireAuth ? await this.getToken() : null;
    const url = `${API_BASE_URL}${endpoint}`;

    const config = {
      ...options,
      headers: {
        ...getHeaders(token),
        ...options.headers,
      },
    };

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Request failed');
      }

      return data;
    } catch (error) {
      console.error('API Request Error:', error);
      throw error;
    }
  }

  /**
   * Authentication Methods
   */
  async login(email, password) {
    const response = await this.request(API_ENDPOINTS.AUTH.LOGIN, {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    });

    if (response.success && response.data.token) {
      await this.setToken(response.data.token);
    }

    return response;
  }

  async register(name, email, password, passwordConfirmation) {
    const response = await this.request(API_ENDPOINTS.AUTH.REGISTER, {
      method: 'POST',
      body: JSON.stringify({
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
      }),
    });

    if (response.success && response.data.token) {
      await this.setToken(response.data.token);
    }

    return response;
  }

  async logout() {
    try {
      await this.request(API_ENDPOINTS.AUTH.LOGOUT, {
        method: 'POST',
      });
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      await this.removeToken();
    }
  }

  async getCurrentUser() {
    return await this.request(API_ENDPOINTS.AUTH.ME);
  }

  /**
   * User Methods
   */
  async getUsers(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const endpoint = queryString
      ? `${API_ENDPOINTS.USERS.LIST}?${queryString}`
      : API_ENDPOINTS.USERS.LIST;
    return await this.request(endpoint);
  }

  async getUser(id) {
    return await this.request(API_ENDPOINTS.USERS.SHOW(id));
  }

  async updateUser(id, data) {
    return await this.request(API_ENDPOINTS.USERS.UPDATE(id), {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  }

  /**
   * Color Methods
   * Get colors from API (public route, no auth required)
   */
  async getColors(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const endpoint = queryString
      ? `${API_ENDPOINTS.COLORS.LIST}?${queryString}`
      : API_ENDPOINTS.COLORS.LIST;
    // Public route - no authentication required
    return await this.request(endpoint, {}, false);
  }

  async getColor(id) {
    return await this.request(API_ENDPOINTS.COLORS.SHOW(id));
  }

  async updateColor(id, hexValue) {
    return await this.request(API_ENDPOINTS.COLORS.UPDATE(id), {
      method: 'PUT',
      body: JSON.stringify({ hex_value: hexValue }),
    });
  }

  /**
   * Sync colors from backend
   * @param {number|null} localVersion - Local version to check against
   */
  async syncColors(localVersion = null) {
    const queryString = localVersion
      ? `?version=${localVersion}`
      : '';
    const endpoint = `${API_ENDPOINTS.COLORS.SYNC}${queryString}`;
    return await this.request(endpoint);
  }

  /**
   * Brand Methods
   */
  async getBrand() {
    return await this.request(API_ENDPOINTS.BRAND.INDEX);
  }

  /**
   * Settings Methods
   */
  async getSettings() {
    return await this.request(API_ENDPOINTS.SETTINGS.INDEX);
  }

  async getLanguages() {
    return await this.request(API_ENDPOINTS.SETTINGS.LANGUAGES);
  }

  async getSettingsColors() {
    return await this.request(API_ENDPOINTS.SETTINGS.COLORS);
  }

  async getAppInfo() {
    return await this.request(API_ENDPOINTS.SETTINGS.APP_INFO, {}, false);
  }

  async getCompanyInfo() {
    return await this.request(API_ENDPOINTS.SETTINGS.COMPANY, {}, false);
  }

  async getBrandAssets() {
    return await this.request(API_ENDPOINTS.SETTINGS.BRAND_ASSETS, {}, false);
  }

  /**
   * Public client i18n (no auth)
   */
  async getClientLanguages(domain = 'mobile') {
    return await this.request(API_ENDPOINTS.I18N.LANGUAGES(domain), {}, false);
  }

  async getClientBundle(domain = 'mobile', locale = 'en') {
    return await this.request(API_ENDPOINTS.I18N.BUNDLE(domain, locale), {}, false);
  }
}

export default new ApiService();

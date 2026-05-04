import React from 'react';
import { AppState } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { translations } from '../i18n/translations';
import ApiService from '../services/api';

const STORAGE_LANG = '@app_language';
const BUNDLE_STORAGE_KEY = '@mobile_i18n_bundle_cache';
const I18N_MAINTENANCE_KEY = '@i18n_last_maintenance_at';
const FALLBACK_LANGS = ['en', 'fr', 'ar'];
const THIRTY_DAYS_MS = 30 * 24 * 60 * 60 * 1000;

const resolvePath = (obj, key) =>
  key.split('.').reduce((acc, segment) => {
    if (acc && typeof acc === 'object' && Object.prototype.hasOwnProperty.call(acc, segment)) {
      return acc[segment];
    }
    return undefined;
  }, obj);

const LanguageContext = React.createContext({
  language: 'en',
  isRTL: false,
  supportedLocales: FALLBACK_LANGS,
  setLanguage: () => {},
  cycleLanguage: () => {},
  t: (key) => key,
});

export function LanguageProvider({ children }) {
  const [language, setLanguageState] = React.useState('en');
  const [bundleCache, setBundleCache] = React.useState({ bundles: {}, meta: {} });
  const [supportedLocales, setSupportedLocales] = React.useState(FALLBACK_LANGS);
  const [languageCatalog, setLanguageCatalog] = React.useState(null);

  const isRTL = React.useMemo(() => {
    const dir = languageCatalog?.supported?.[language]?.direction;
    if (dir === 'rtl') {
      return true;
    }
    if (dir === 'ltr') {
      return false;
    }
    return language === 'ar';
  }, [language, languageCatalog]);

  const fetchAndCacheBundle = React.useCallback(async (targetLanguage, options = {}) => {
    const { force = false } = options;
    try {
      const response = await ApiService.getClientBundle('mobile', targetLanguage);
      const bundle = response?.data?.bundle;
      const meta = response?.data?.meta || {};
      if (!bundle || typeof bundle !== 'object') {
        return;
      }

      setBundleCache((prev) => {
        if (
          !force &&
          prev.meta[targetLanguage]?.checksum &&
          meta?.checksum &&
          prev.meta[targetLanguage].checksum === meta.checksum
        ) {
          return prev;
        }
        const next = {
          bundles: { ...prev.bundles, [targetLanguage]: bundle },
          meta: { ...prev.meta, [targetLanguage]: meta },
        };
        AsyncStorage.setItem(BUNDLE_STORAGE_KEY, JSON.stringify(next)).catch(() => {});

        return next;
      });
    } catch {
      // keep cached / fallback strings
    }
  }, []);

  const runMonthlyMaintenanceIfDue = React.useCallback(async () => {
    try {
      const lastRaw = await AsyncStorage.getItem(I18N_MAINTENANCE_KEY);
      const now = Date.now();
      if (lastRaw) {
        const last = new Date(lastRaw).getTime();
        if (!Number.isNaN(last) && now - last < THIRTY_DAYS_MS) {
          return;
        }
      }

      let langRes;
      try {
        langRes = await ApiService.getClientLanguages('mobile');
      } catch {
        return;
      }

      const supported = langRes?.data?.supported;
      const locales =
        supported && typeof supported === 'object' ? Object.keys(supported) : FALLBACK_LANGS;
      setSupportedLocales(locales);
      setLanguageCatalog(langRes.data);

      const raw = await AsyncStorage.getItem(BUNDLE_STORAGE_KEY);
      let merged = { bundles: {}, meta: {} };
      if (raw) {
        try {
          const parsed = JSON.parse(raw);
          if (parsed?.bundles) {
            merged.bundles = { ...parsed.bundles };
          }
          if (parsed?.meta) {
            merged.meta = { ...parsed.meta };
          }
        } catch {
          // ignore
        }
      }

      for (const loc of locales) {
        try {
          const response = await ApiService.getClientBundle('mobile', loc);
          const bundle = response?.data?.bundle;
          const meta = response?.data?.meta || {};
          if (bundle && typeof bundle === 'object') {
            merged.bundles[loc] = bundle;
            merged.meta[loc] = meta;
          }
        } catch {
          // keep previous bundle for this locale if any
        }
      }

      await AsyncStorage.setItem(BUNDLE_STORAGE_KEY, JSON.stringify(merged));
      setBundleCache(merged);
      await AsyncStorage.setItem(I18N_MAINTENANCE_KEY, new Date().toISOString());
    } catch {
      // ignore
    }
  }, []);

  React.useEffect(() => {
    let cancelled = false;

    const boot = async () => {
      try {
        const [langStored, bundleRaw] = await Promise.all([
          AsyncStorage.getItem(STORAGE_LANG),
          AsyncStorage.getItem(BUNDLE_STORAGE_KEY),
        ]);

        if (langStored && FALLBACK_LANGS.includes(langStored)) {
          setLanguageState(langStored);
        }

        if (bundleRaw) {
          try {
            const parsed = JSON.parse(bundleRaw);
            if (parsed && typeof parsed === 'object' && parsed.bundles) {
              setBundleCache({
                bundles: parsed.bundles || {},
                meta: parsed.meta || {},
              });
            }
          } catch {
            // ignore
          }
        }

        try {
          const langRes = await ApiService.getClientLanguages('mobile');
          if (!cancelled && langRes?.data?.supported) {
            setSupportedLocales(Object.keys(langRes.data.supported));
            setLanguageCatalog(langRes.data);
          }
        } catch {
          // keep FALLBACK_LANGS
        }

        if (!cancelled) {
          await runMonthlyMaintenanceIfDue();
        }
      } catch {
        // keep defaults
      }
    };

    boot();

    return () => {
      cancelled = true;
    };
  }, [runMonthlyMaintenanceIfDue]);

  React.useEffect(() => {
    const sub = AppState.addEventListener('change', (next) => {
      if (next === 'active') {
        runMonthlyMaintenanceIfDue().catch(() => {});
      }
    });
    return () => sub.remove();
  }, [runMonthlyMaintenanceIfDue]);

  React.useEffect(() => {
    fetchAndCacheBundle(language).catch(() => {});
  }, [language, fetchAndCacheBundle]);

  const setLanguage = React.useCallback(
    async (nextLanguage) => {
      const allowed = supportedLocales.length ? supportedLocales : FALLBACK_LANGS;
      if (!allowed.includes(nextLanguage)) {
        return;
      }
      setLanguageState(nextLanguage);
      try {
        await AsyncStorage.setItem(STORAGE_LANG, nextLanguage);
      } catch {
        // ignore
      }
      fetchAndCacheBundle(nextLanguage).catch(() => {});
    },
    [supportedLocales, fetchAndCacheBundle]
  );

  const cycleLanguage = React.useCallback(() => {
    const allowed = supportedLocales.length ? supportedLocales : FALLBACK_LANGS;
    const idx = allowed.indexOf(language);
    const next = allowed[(idx + 1) % allowed.length] || 'en';
    setLanguage(next);
  }, [language, supportedLocales, setLanguage]);

  const t = React.useCallback(
    (key) => {
      const remoteLocalized = resolvePath(bundleCache.bundles[language], key);
      if (remoteLocalized !== undefined) {
        return remoteLocalized;
      }

      const localized = resolvePath(translations[language], key);
      if (localized !== undefined) {
        return localized;
      }

      const fallback = resolvePath(translations.en, key);
      return fallback !== undefined ? fallback : key;
    },
    [language, bundleCache.bundles]
  );

  const value = React.useMemo(
    () => ({
      language,
      isRTL,
      supportedLocales: supportedLocales.length ? supportedLocales : FALLBACK_LANGS,
      setLanguage,
      cycleLanguage,
      t,
    }),
    [language, isRTL, supportedLocales, setLanguage, cycleLanguage, t]
  );

  return <LanguageContext.Provider value={value}>{children}</LanguageContext.Provider>;
}

export function useLanguage() {
  return React.useContext(LanguageContext);
}

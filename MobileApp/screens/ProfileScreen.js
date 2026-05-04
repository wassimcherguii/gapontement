import React from 'react';
import { StyleSheet, View } from 'react-native';
import { Button, Card, Text } from 'react-native-paper';
import { useLanguage } from '../contexts/LanguageContext';

export default function ProfileScreen({ onLogout }) {
  const { t, language, setLanguage, supportedLocales } = useLanguage();

  return (
    <View style={styles.container}>
      <Card>
        <Card.Content>
          <Text variant="titleLarge">{t('profile.title')}</Text>
          <Text style={styles.subtitle}>{t('profile.subtitle')}</Text>

          <Text style={styles.sectionLabel}>{t('profile.language')}</Text>
          <Text style={styles.hint}>{t('profile.languageHint')}</Text>
          <View style={styles.langRow}>
            {supportedLocales.map((code) => (
              <Button
                key={code}
                mode={language === code ? 'contained' : 'outlined'}
                compact
                style={styles.langButton}
                onPress={() => setLanguage(code)}
              >
                {code === 'en'
                  ? t('profile.languageEnglish')
                  : code === 'fr'
                    ? t('profile.languageFrench')
                    : code === 'ar'
                      ? t('profile.languageArabic')
                      : code.toUpperCase()}
              </Button>
            ))}
          </View>

          <Button mode="outlined" onPress={onLogout} style={styles.button}>
            {t('profile.logout')}
          </Button>
        </Card.Content>
      </Card>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
    justifyContent: 'center',
  },
  subtitle: {
    marginTop: 8,
    marginBottom: 12,
  },
  sectionLabel: {
    marginTop: 8,
    fontWeight: '600',
  },
  hint: {
    marginTop: 4,
    marginBottom: 8,
    opacity: 0.75,
    fontSize: 12,
  },
  langRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    marginBottom: 16,
  },
  langButton: {
    marginRight: 8,
    marginBottom: 8,
  },
  button: {
    marginTop: 8,
  },
});

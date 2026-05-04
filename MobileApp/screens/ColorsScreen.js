import React from 'react';
import { ScrollView, StyleSheet, View } from 'react-native';
import { Button, Card, Text } from 'react-native-paper';
import { useLanguage } from '../contexts/LanguageContext';

const storedColors = require('../assets/colors.json');

export default function ColorsScreen({ onGoHome }) {
  const { t } = useLanguage();

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <Card>
          <Card.Content>
            <Text variant="titleLarge">{t('colors.title')}</Text>
            <Text style={styles.subtitle}>{t('colors.subtitle')}</Text>
            <Text style={styles.jsonText}>{JSON.stringify(storedColors, null, 2)}</Text>
            {typeof onGoHome === 'function' ? (
              <Button mode="outlined" onPress={onGoHome} style={styles.button}>
                {t('colors.backHome')}
              </Button>
            ) : null}
          </Card.Content>
        </Card>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc',
  },
  scrollContent: {
    padding: 16,
    paddingBottom: 32,
  },
  subtitle: {
    marginTop: 8,
    marginBottom: 12,
  },
  jsonText: {
    fontSize: 12,
    lineHeight: 18,
  },
  button: {
    marginTop: 12,
  },
});

import React from 'react';
import { StyleSheet, View } from 'react-native';
import { Button, Card, Text } from 'react-native-paper';
import { useLanguage } from '../contexts/LanguageContext';

export default function HomeScreen({ navigation, onOpenColors }) {
  const { t } = useLanguage();

  const handleOpenColors = () => {
    if (typeof onOpenColors === 'function') {
      onOpenColors();
      return;
    }
    if (navigation && typeof navigation.navigate === 'function') {
      navigation.navigate('Colors');
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.content}>
        <Card>
          <Card.Content>
            <Text variant="titleLarge">{t('home.title')}</Text>
            <Text style={styles.subtitle}>{t('home.subtitle')}</Text>
            <Button
              mode="contained"
              onPress={handleOpenColors}
              style={styles.button}
            >
              {t('home.showStoredColors')}
            </Button>
          </Card.Content>
        </Card>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f8fafc',
  },
  content: {
    flex: 1,
    padding: 16,
    justifyContent: 'center',
  },
  subtitle: {
    marginTop: 8,
    marginBottom: 16,
  },
  button: {
    marginTop: 4,
  },
});

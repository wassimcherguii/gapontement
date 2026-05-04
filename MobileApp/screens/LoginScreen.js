import React, { useState } from 'react';
import { StyleSheet, View } from 'react-native';
import { Button, Card, Text, TextInput } from 'react-native-paper';
import ApiService from '../services/api';
import { useLanguage } from '../contexts/LanguageContext';

export default function LoginScreen({ onLoginSuccess, onGoToSignup }) {
  const { t } = useLanguage();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleLogin = async () => {
    try {
      setLoading(true);
      setError('');
      const response = await ApiService.login(email, password);
      if (response && response.success) {
        if (typeof onLoginSuccess === 'function') {
          onLoginSuccess();
        }
      } else {
        setError(t('auth.loginFailed'));
      }
    } catch (e) {
      setError(t('auth.invalidCredentials'));
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <Card>
        <Card.Content>
          <Text variant="titleLarge">{t('auth.loginTitle')}</Text>
          <TextInput
            label={t('auth.email')}
            value={email}
            onChangeText={setEmail}
            autoCapitalize="none"
            keyboardType="email-address"
            style={styles.input}
          />
          <TextInput
            label={t('auth.password')}
            value={password}
            onChangeText={setPassword}
            secureTextEntry
            style={styles.input}
          />
          {error ? <Text style={styles.error}>{error}</Text> : null}
          <Button mode="contained" onPress={handleLogin} loading={loading} style={styles.button}>
            {t('auth.loginButton')}
          </Button>
          <Button mode="text" onPress={onGoToSignup} style={styles.linkButton}>
            {t('auth.goSignup')}
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
  input: {
    marginTop: 12,
  },
  button: {
    marginTop: 16,
  },
  error: {
    color: '#dc2626',
    marginTop: 10,
  },
  linkButton: {
    marginTop: 6,
  },
});

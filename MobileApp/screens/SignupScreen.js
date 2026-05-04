import React, { useState } from 'react';
import { StyleSheet, View } from 'react-native';
import { Button, Card, Text, TextInput } from 'react-native-paper';
import ApiService from '../services/api';
import { useLanguage } from '../contexts/LanguageContext';

export default function SignupScreen({ onSignupSuccess, onGoToLogin }) {
  const { t } = useLanguage();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSignup = async () => {
    try {
      setLoading(true);
      setError('');
      const response = await ApiService.register(name, email, password, confirmPassword);
      if (response && response.success) {
        if (typeof onSignupSuccess === 'function') {
          onSignupSuccess();
        }
      } else {
        setError(t('auth.signupFailed'));
      }
    } catch (e) {
      setError(t('auth.signupDataError'));
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <Card>
        <Card.Content>
          <Text variant="titleLarge">{t('auth.signupTitle')}</Text>
          <TextInput
            label={t('auth.name')}
            value={name}
            onChangeText={setName}
            style={styles.input}
          />
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
          <TextInput
            label={t('auth.confirmPassword')}
            value={confirmPassword}
            onChangeText={setConfirmPassword}
            secureTextEntry
            style={styles.input}
          />
          {error ? <Text style={styles.error}>{error}</Text> : null}
          <Button mode="contained" onPress={handleSignup} loading={loading} style={styles.button}>
            {t('auth.createAccount')}
          </Button>
          <Button mode="text" onPress={onGoToLogin} style={styles.linkButton}>
            {t('auth.goLogin')}
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
  linkButton: {
    marginTop: 6,
  },
  error: {
    color: '#dc2626',
    marginTop: 10,
  },
});

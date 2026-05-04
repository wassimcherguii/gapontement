import React from 'react';
import { Image, StyleSheet, View } from 'react-native';
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';
import {
  ActivityIndicator,
  Appbar,
  configureFonts,
  MD3LightTheme,
  Provider as PaperProvider,
  Surface,
  Text,
  TouchableRipple,
} from 'react-native-paper';
import { useFonts, Poppins_400Regular, Poppins_500Medium, Poppins_600SemiBold } from '@expo-google-fonts/poppins';
import HomeScreen from './screens/HomeScreen';
import ColorsScreen from './screens/ColorsScreen';
import LoginScreen from './screens/LoginScreen';
import SignupScreen from './screens/SignupScreen';
import ProfileScreen from './screens/ProfileScreen';
import ApiService from './services/api';
import { LanguageProvider, useLanguage } from './contexts/LanguageContext';

function AppShell() {
  const { t, isRTL } = useLanguage();

  const theme = React.useMemo(
    () => ({
      ...MD3LightTheme,
      fonts: configureFonts({
        config: {
          fontFamily: 'Poppins_400Regular',
        },
      }),
    }),
    []
  );

  const [screen, setScreen] = React.useState('home');
  const [appName, setAppName] = React.useState('Dashboard');
  const [logoUrl, setLogoUrl] = React.useState(null);
  const [isLoggedIn, setIsLoggedIn] = React.useState(false);

  React.useEffect(() => {
    const loadTopBarData = async () => {
      try {
        const [companyResponse, brandResponse, token] = await Promise.all([
          ApiService.getCompanyInfo(),
          ApiService.getBrandAssets(),
          ApiService.getToken(),
        ]);

        const company = companyResponse && companyResponse.data ? companyResponse.data : null;
        const brand = brandResponse && brandResponse.data ? brandResponse.data : null;

        if (company && company.name) {
          setAppName(company.name);
        }
        if (brand && brand.logo_url) {
          setLogoUrl(brand.logo_url);
        }
        setIsLoggedIn(!!token);
      } catch (error) {
        setIsLoggedIn(false);
      }
    };

    loadTopBarData();
  }, []);

  const renderScreen = () => {
    if (screen === 'home') {
      return <HomeScreen onOpenColors={() => setScreen('colors')} />;
    }
    if (screen === 'login') {
      return (
        <LoginScreen
          onLoginSuccess={() => {
            setIsLoggedIn(true);
            setScreen('home');
          }}
          onGoToSignup={() => setScreen('signup')}
        />
      );
    }
    if (screen === 'signup') {
      return (
        <SignupScreen
          onSignupSuccess={() => {
            setIsLoggedIn(true);
            setScreen('home');
          }}
          onGoToLogin={() => setScreen('login')}
        />
      );
    }
    if (screen === 'profile') {
      return (
        <ProfileScreen
          onLogout={async () => {
            await ApiService.logout();
            setIsLoggedIn(false);
            setScreen('home');
          }}
        />
      );
    }
    return <ColorsScreen onGoHome={() => setScreen('home')} />;
  };

  const writingDirection = isRTL ? 'rtl' : 'ltr';

  return (
    <PaperProvider theme={theme}>
      <SafeAreaView style={[styles.container, { writingDirection }]} edges={['top', 'bottom']}>
        <Appbar.Header style={isRTL ? { flexDirection: 'row-reverse' } : undefined}>
          {logoUrl ? (
            <Image
              source={{ uri: logoUrl }}
              style={[styles.logo, isRTL ? { marginLeft: 0, marginRight: 8 } : { marginLeft: 8 }]}
            />
          ) : (
            <Appbar.Action icon="monitor-dashboard" onPress={() => {}} />
          )}
          <Appbar.Content title={appName} />
          <Appbar.Action
            icon={isLoggedIn ? 'account-circle' : 'login'}
            onPress={() => setScreen(isLoggedIn ? 'profile' : 'login')}
          />
        </Appbar.Header>

        <View style={[styles.content, { writingDirection }]}>{renderScreen()}</View>

        <Surface
          style={[styles.bottomBar, isRTL ? { flexDirection: 'row-reverse' } : { flexDirection: 'row' }]}
          elevation={2}
        >
          <TouchableRipple onPress={() => setScreen('home')} style={styles.tabButton}>
            <View style={styles.tabContent}>
              <Appbar.Action icon="home" onPress={() => setScreen('home')} />
              <Text style={styles.tabText}>{t('tabs.home')}</Text>
            </View>
          </TouchableRipple>
          <TouchableRipple onPress={() => setScreen('colors')} style={styles.tabButton}>
            <View style={styles.tabContent}>
              <Appbar.Action icon="palette" onPress={() => setScreen('colors')} />
              <Text style={styles.tabText}>{t('tabs.colors')}</Text>
            </View>
          </TouchableRipple>
        </Surface>
      </SafeAreaView>
    </PaperProvider>
  );
}

export default function App() {
  const [fontsLoaded] = useFonts({
    Poppins_400Regular,
    Poppins_500Medium,
    Poppins_600SemiBold,
  });

  if (!fontsLoaded) {
    return (
      <SafeAreaProvider>
        <View style={styles.loaderContainer}>
          <ActivityIndicator size="large" />
        </View>
      </SafeAreaProvider>
    );
  }

  return (
    <SafeAreaProvider>
      <LanguageProvider>
        <AppShell />
      </LanguageProvider>
    </SafeAreaProvider>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  loaderContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  logo: {
    width: 32,
    height: 32,
    borderRadius: 6,
  },
  content: {
    flex: 1,
  },
  bottomBar: {
    justifyContent: 'space-around',
    alignItems: 'center',
    paddingVertical: 6,
  },
  tabButton: {
    flex: 1,
    borderRadius: 8,
    marginHorizontal: 8,
  },
  tabContent: {
    alignItems: 'center',
    justifyContent: 'center',
  },
  tabText: {
    marginTop: -8,
    marginBottom: 4,
  },
});

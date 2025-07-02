import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import DashboardScreen from '../components/screens/DashboardScreen';
import HomeScreen from '../components/screens/HomeScreen';
import LoginScreen from '../components/screens/LoginScreen';
import RegisterScreen from '../components/screens/RegisterScreen';
import ActiveSubscriptionScreen from '../components/screens/ActiveSubscriptionScreen';
import AddSubscriptionScreen from '../components/screens/AddSubscriptionScreen';
import AddSubscription2Screen from '../components/screens/AddSubscription2Screen';
import SubscriptionListScreen from '../components/screens/SubscriptionListScreen';
import CustomSubscriptionScreen from '../components/screens/CustomSubscriptionScreen';
import ProfileScreen from '../components/screens/ProfileScreen';
import { TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';

const Stack = createStackNavigator();

const AppNavigator = () => {
  return (
    <Stack.Navigator
      screenOptions={({ navigation }) => ({
        headerTitleAlign: 'center',
        headerTintColor: 'white',
        headerTitleStyle: { fontSize: 20, fontWeight: 'bold', fontFamily: 'OutfitBold' },
        headerStyle: { backgroundColor: '#A6FF00' },
        headerLeft: () => (
          <TouchableOpacity onPress={() => navigation.goBack()} style={{ paddingLeft: 15 }}>
            <Ionicons name="arrow-back" size={24} color="white" />
          </TouchableOpacity>
        ),
        headerRight: () => (
          <TouchableOpacity onPress={() => navigation.navigate('Home')} style={{ paddingRight: 15 }}>
            <Ionicons name="home" size={24} color="white" />
          </TouchableOpacity>
        ),
      })}
    >
      <Stack.Screen name="Home" component={HomeScreen} />
      <Stack.Screen name="Login" component={LoginScreen} />
      <Stack.Screen name="Register" component={RegisterScreen} />
      <Stack.Screen name="Profile" component={ProfileScreen} />
      <Stack.Screen name="Dashboard" component={DashboardScreen} />
      <Stack.Screen name="SubscriptionList" component={SubscriptionListScreen} />
      <Stack.Screen name="ActiveSubscription" component={ActiveSubscriptionScreen} />
      <Stack.Screen name="AddSubscription" component={AddSubscriptionScreen} />
      <Stack.Screen name="AddSubscription2" component={AddSubscription2Screen} />
      <Stack.Screen name="CustomSubscription" component={CustomSubscriptionScreen} />
    </Stack.Navigator>
  );
};

export default AppNavigator;

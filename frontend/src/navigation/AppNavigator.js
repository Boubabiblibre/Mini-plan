// src/navigation/AppNavigator.js
import React from "react";
import { TouchableOpacity, View, Text } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { createStackNavigator } from "@react-navigation/stack";
import { useAccountStore } from "../store/account";

// Screens
import HomeScreen from "../components/screens/HomeScreen";
import LoginScreen from "../components/screens/LoginScreen";
import RegisterScreen from "../components/screens/RegisterScreen";
import ProfileScreen from "../components/screens/ProfileScreen";
import DashboardScreen from "../components/screens/DashboardScreen";

import SubscriptionListScreen from "../components/screens/SubscriptionListScreen";
import SubscriptionDetailsScreen from "../components/screens/SubscriptionDetailsScreen";
import ActiveSubscriptionScreen from "../components/screens/ActiveSubscriptionScreen";
import AddSubscriptionScreen from "../components/screens/AddSubscriptionScreen";
import AddSubscription2Screen from "../components/screens/AddSubscription2Screen";
import CustomSubscriptionScreen from "../components/screens/CustomSubscriptionScreen";

import SpacesScreen from "../components/screens/SpacesScreen";
import SpaceDetailsScreen from "../components/screens/SpaceDetailsScreen";
import CreateSpaceScreen from "../components/screens/SpaceCreateScreen";

const Stack = createStackNavigator();

export default function AppNavigator() {
  const { account } = useAccountStore();
  const isAdmin = Array.isArray(account?.roles) && account.roles.includes("ROLE_ADMIN");

  return (
    <Stack.Navigator
      initialRouteName="Dashboard"   // <-- pas AddSubscription :)
      screenOptions={({ navigation, route }) => ({
        headerTitleAlign: "center",
        headerTintColor: "white",
        headerTitleStyle: { fontSize: 20, fontWeight: "bold", fontFamily: "OutfitBold" },
        headerStyle: { backgroundColor: "#A6FF00" },

        headerLeft: () => (
          <TouchableOpacity
            onPress={() => {
              if (navigation.canGoBack()) navigation.goBack();
              else navigation.navigate("Dashboard");
            }}
            style={{ paddingLeft: 15 }}
          >
            <Ionicons
              name={navigation.canGoBack() ? "arrow-back" : "home"}
              size={24}
              color="white"
            />
          </TouchableOpacity>
        ),

        // ADMIN badge + Home button (not on Home)
        headerRight: () => (
          <View style={{ flexDirection: "row", alignItems: "center", paddingRight: 15 }}>
            {isAdmin && (
              <View
                style={{
                  backgroundColor: "#111",
                  borderColor: "#A6FF00",
                  borderWidth: 1,
                  paddingHorizontal: 8,
                  paddingVertical: 4,
                  borderRadius: 999,
                  marginRight: 8,
                }}
              >
                <Text style={{ color: "#A6FF00", fontWeight: "700" }}>ADMIN</Text>
              </View>
            )}
            {route.name !== "Home" && (
              <TouchableOpacity onPress={() => navigation.navigate("Home")}>
                <Ionicons name="home" size={24} color="white" />
              </TouchableOpacity>
            )}
          </View>
        ),
      })}
    >

      {/* Auth / profil */}
      <Stack.Screen name="Home" component={HomeScreen} options={{ title: "Home" }} />
      <Stack.Screen name="Login" component={LoginScreen} options={{ title: "Se connecter" }} />
      <Stack.Screen name="Register" component={RegisterScreen} options={{ title: "S'inscrire" }} />
      <Stack.Screen name="Profile" component={ProfileScreen} options={{ title: "Profil" }} />

      {/* App */}
      <Stack.Screen name="Dashboard" component={DashboardScreen} options={{ title: "Dashboard" }} />

      {/* Subscriptions */}
      <Stack.Screen
        name="SubscriptionList"
        component={SubscriptionListScreen}
        options={{ title: "Liste des abonnements" }}
      />
      <Stack.Screen
        name="SubscriptionDetails"
        component={SubscriptionDetailsScreen}
        options={{ title: "Détails de l'abonnement" }}
      />
      <Stack.Screen
        name="ActiveSubscription"
        component={ActiveSubscriptionScreen}
        options={{ title: "Abonnements actifs" }}
      />
      <Stack.Screen
        name="AddSubscription"
        component={AddSubscriptionScreen}
        options={{ title: "Nouvel abonnement" }}
      />
      <Stack.Screen
        name="AddSubscription2"
        component={AddSubscription2Screen}
        options={{ title: "Ajouter un abonnement" }}
      />
      <Stack.Screen
        name="CustomSubscription"
        component={CustomSubscriptionScreen}
        options={{ title: "Modifier un abonnement" }}
      />

      {/* Spaces */}
      <Stack.Screen name="SpacesScreen" component={SpacesScreen} options={{ title: "Espaces" }} />
      <Stack.Screen name="SpaceDetails" component={SpaceDetailsScreen} options={{ title: "Détails de l'espace" }} />
      <Stack.Screen name="SpaceCreate" component={CreateSpaceScreen} options={{ title: "Créer un espace" }} />
    </Stack.Navigator>
  );
}

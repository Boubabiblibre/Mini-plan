/* App.js ---------------------------------------------------------------- */
import React from "react";
import { View, ActivityIndicator, StyleSheet } from "react-native";
import { gestureHandlerRootHOC } from "react-native-gesture-handler";

import { NavigationContainer } from "@react-navigation/native";
import * as Linking from "expo-linking";

import {
  useFonts,
  Outfit_400Regular,
  Outfit_700Bold,
} from "@expo-google-fonts/outfit";

/* ⇒ ton navigator d’écrans (sans NavigationContainer à l’intérieur !) */
import RootStackNavigator from "./src/navigation/AppNavigator";

/* ---------- Linking : URL <-> navigation ------------------------------ */
const linking = {
  prefixes: [Linking.createURL("/")],        // ex. http://localhost:8081/
  config: {
    screens: {
      Home: "",
      Login: "login",
      Register: "register",
      Dashboard: "dashboard",
      SubscriptionList: "subscriptions",
      /* ajoute d'autres écrans si besoin */
    },
  },
};

function App() {
  /* Chargement des polices Google -------------------------------------- */
  const [fontsLoaded] = useFonts({
    OutfitRegular: Outfit_400Regular,
    OutfitBold: Outfit_700Bold,
  });

  if (!fontsLoaded) {
    return (
      <View style={styles.loader}>
        <ActivityIndicator size="large" />
      </View>
    );
  }

  /* --------------------- Rendu principal ------------------------------ */
  return (
    <NavigationContainer linking={linking}>
      <RootStackNavigator />
    </NavigationContainer>
  );
}

/* -------------- Styles auxiliaires ----------------------------------- */
const styles = StyleSheet.create({
  loader: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
});

/* Export avec gesture-handler pour le web ----------------------------- */
export default gestureHandlerRootHOC(App);

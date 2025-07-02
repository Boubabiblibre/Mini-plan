import React from "react";
import { View, Text, TouchableOpacity } from "react-native";
import styles from "../../styles/HomeStyles";
import useCookieHook from "../../hooks/use-cookie";
import { useAccountStore } from "../../store/account";

const HomeScreen = ({ navigation }) => {
  const { isLogged, logout } = useCookieHook();
  const { account } = useAccountStore();

  return (
    <View style={styles.container}>
      <View style={styles.introContainer}>
        <Text style={styles.introTitle}>
          Gérez facilement vos abonnements en famille
        </Text>
        <Text style={styles.introText}>
          Gardez le contrôle de vos paiements et évitez les mauvaises surprises
        </Text>
      </View>

      {/* ---------------- Visiteur ---------------- */}
      {!isLogged && (
        <>
          <TouchableOpacity
            style={styles.button}
            onPress={() => navigation.navigate("Login")}
          >
            <Text style={styles.buttonText}>CONNEXION</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.button, styles.secondaryButton]}
            onPress={() => navigation.navigate("Register")}
          >
            <Text style={styles.buttonText}>INSCRIPTION</Text>
          </TouchableOpacity>
        </>
      )}

      {/* ---------------- Utilisateur connecté ---------------- */}
      {isLogged && (
        <>
          <Text style={styles.welcomeText}>
            Bienvenue {account?.firstname ?? ""} !
          </Text>

          <TouchableOpacity
            style={[styles.button, styles.secondaryButton]}
            onPress={() => navigation.navigate("Dashboard")}
          >
            <Text style={styles.buttonText}>DASHBOARD</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.button, styles.secondaryButton]}
            onPress={() => navigation.navigate("SubscriptionList")}
          >
            <Text style={styles.buttonText}>MES ABONNEMENTS</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.button, styles.dangerButton]}
            onPress={() => {
              logout();
              navigation.navigate("Login");
            }}
          >
            <Text style={styles.buttonText}>SE DÉCONNECTER</Text>
          </TouchableOpacity>
        </>
      )}
    </View>
  );
};

export default HomeScreen;

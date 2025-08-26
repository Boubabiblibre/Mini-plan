import React, { useEffect, useState } from "react";
import { View, Text, TouchableOpacity } from "react-native";
import styles from "../../styles/HomeStyles";
import useCookieHook from "../../hooks/use-cookie";
import { useAccountStore } from "../../store/account";
import { fetchAccount } from "../../services/account";

const HomeScreen = ({ navigation }) => {
  const { isLogged, logout } = useCookieHook();
  const { setAccount, restoreAccount } = useAccountStore();

  const [me, setMe] = useState(null);

  useEffect(() => {
    const load = async () => {
      if (!isLogged) {
        restoreAccount();
        setMe(null);
        return;
      }
      try {
        const user = await fetchAccount();
        console.log("👤 fetchAccount a retourné:", user);
        if (user) {
          setMe(user);       // local
          setAccount(user);  // global aussi
        } else {
          restoreAccount();
          setMe(null);
        }
      } catch (e) {
        console.error("Erreur fetchAccount:", e);
        restoreAccount();
        setMe(null);
      }
    };
    load();
  }, [isLogged, restoreAccount, setAccount]);

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

      {!isLogged ? (
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
      ) : (
        <>
          <Text style={styles.welcomeText}>
            Bienvenue {me?.firstname ?? me?.email ?? "Utilisateur"} !
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
              restoreAccount();
              setMe(null);
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

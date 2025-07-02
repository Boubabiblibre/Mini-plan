import React, { useState } from "react";
import { View, Text, TextInput, TouchableOpacity } from "react-native";
import { Button, Snackbar, ActivityIndicator } from "react-native-paper";
import { useNavigation } from "@react-navigation/native";
import AsyncStorage from "@react-native-async-storage/async-storage";
import styles from "../../styles/LoginStyles";

const LoginScreen = () => {
  /* Champs */
  const [email,    setEmail]    = useState("");
  const [password, setPassword] = useState("");

  /* Feedback */
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(null);   // plus de <string | null>
  const [error,   setError]   = useState(null);

  const navigation = useNavigation();

  /* --------------------------------------------------------- */
  const handleClickLogin = async () => {
    setError(null);
    setSuccess(null);
    setLoading(true);

    if (!email.trim() || !password) {
      setError("Veuillez remplir tous les champs.");
      setLoading(false);
      return;
    }

    try {
      const response = await fetch("http://127.0.0.1:8000/api/auth/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json(); // <--- manquait ici

      if (response.ok && data.success) {
        await AsyncStorage.setItem("token", data.token); // <-- maintenant data.token existe
        setSuccess("Connexion réussie !");
        setTimeout(() => navigation.navigate("Home"), 500);
      } else {
        setError(data.message || "Échec de la connexion");
      }
    } catch (err) {
      console.log(err);
      
      setError("Impossible de contacter le serveur");
    } finally {
      setLoading(false);
    }
  };


  /* --------------------------------------------------------- */
  return (
    <View style={styles.container}>
      <Text style={styles.label}>Email</Text>
      <TextInput
        value={email}
        onChangeText={setEmail}
        keyboardType="email-address"
        autoCapitalize="none"
        style={styles.input}
      />

      <Text style={styles.label}>Mot de passe</Text>
      <TextInput
        value={password}
        onChangeText={setPassword}
        secureTextEntry
        style={styles.input}
      />

      <Button
        mode="contained"
        onPress={handleClickLogin}
        style={styles.button}
        labelStyle={styles.buttonText}
      >
        Se connecter
      </Button>

      <TouchableOpacity onPress={() => navigation.navigate("Register")}>
        <Text style={styles.link}>
          Pas encore inscrit ? Inscris-toi
        </Text>
      </TouchableOpacity>

      {/* Snackbar succès */}
      <Snackbar
        visible={!!success}
        onDismiss={() => setSuccess(null)}
        duration={3000}
        style={{ backgroundColor: "#4caf50" }}
      >
        {success}
      </Snackbar>

      {/* Snackbar erreur */}
      <Snackbar
        visible={!!error}
        onDismiss={() => setError(null)}
        duration={3000}
        style={{ backgroundColor: "#f44336" }}
      >
        {error}
      </Snackbar>

      {/* Loader plein écran (optionnel) */}
      {loading && (
        <View style={styles.loadingOverlay}>
          <ActivityIndicator animating size="large" />
        </View>
      )}
    </View>
  );
};

export default LoginScreen;

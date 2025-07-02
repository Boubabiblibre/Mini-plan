import React, { useState } from "react";
import { View, Text, TextInput, TouchableOpacity } from "react-native";
import { Button, Snackbar, ActivityIndicator } from "react-native-paper";
import styles from "../../styles/RegisterStyles";

const RegisterScreen = ({ navigation }) => {
  const [firstname, setFirstname] = useState("");
  const [lastname, setLastname]   = useState("");
  const [email, setEmail]         = useState("");
  const [username, setUsername]   = useState("");
  const [password, setPassword]   = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");

  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(null);  
  const [error,   setError]   = useState(null);

  const handleRegister = async () => {
    setError(null);
    setSuccess(null);

    // Vérifier si tous les champs sont remplis
    if (
      !firstname.trim() ||
      !lastname.trim() ||
      !email.trim() ||
      !username.trim() ||
      !password ||
      !confirmPassword
    ) {
      setError("Veuillez remplir tous les champs.");
      return;
    }

    setLoading(true);

    try {
      const res = await fetch("http://127.0.0.1:8000/api/user/create", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          firstname,
          lastname,
          email,
          username,
          password,
          confirmPassword,
        }),
      });

      if (res.ok) {
        setSuccess("Inscription réussie !");
        setTimeout(() => navigation.navigate("Login"), 700);
      } else {
        const { error: msg } = await res.json();
        setError(msg || "Erreur lors de l'inscription");
      }
    } catch {
      setError("Impossible de contacter le serveur");
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      {/* --- autres boutons & séparateur --- */}

      {[
        { label: "Prénom",        value: firstname, setter: setFirstname },
        { label: "Nom de famille",value: lastname,  setter: setLastname },
        { label: "Email",         value: email,     setter: setEmail, keyboard: "email-address" },
        { label: "Pseudo",        value: username,  setter: setUsername },
      ].map(({ label, value, setter, keyboard }) => (
        <View key={label}>
          <Text style={styles.label}>{label}</Text>
          <TextInput
            value={value}
            onChangeText={setter}
            keyboardType={keyboard}
            autoCapitalize="none"
            style={styles.input}
          />
        </View>
      ))}

      <Text style={styles.label}>Mot de passe</Text>
      <TextInput
        value={password}
        onChangeText={setPassword}
        secureTextEntry
        style={styles.input}
      />

      <Text style={styles.label}>Confirmer le mot de passe</Text>
      <TextInput
        value={confirmPassword}
        onChangeText={setConfirmPassword}
        secureTextEntry
        style={styles.input}
      />

      <Button
        mode="contained"
        onPress={handleRegister}
        loading={loading}
        disabled={loading}
        style={styles.button}
        labelStyle={styles.buttonText}
      >
        S'inscrire
      </Button>

      <TouchableOpacity onPress={() => navigation.navigate("Login")}>
        <Text style={styles.link}>Déjà un compte ? Connecte-toi</Text>
      </TouchableOpacity>

      <Snackbar
        visible={!!success}
        onDismiss={() => setSuccess(null)}
        duration={3000}
        style={{ backgroundColor: "#4caf50" }}
      >
        {success}
      </Snackbar>

      <Snackbar
        visible={!!error}
        onDismiss={() => setError(null)}
        duration={3000}
        style={{ backgroundColor: "#f44336" }}
      >
        {error}
      </Snackbar>

      {loading && (
        <View style={styles.loadingContainer}>
          <ActivityIndicator animating size="large" />
        </View>
      )}
    </View>
  );
};

export default RegisterScreen;

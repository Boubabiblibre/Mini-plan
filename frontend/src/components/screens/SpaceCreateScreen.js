// src/components/screens/SpaceCreateScreen.js
import React, { useState } from "react";
import { View, Text, TextInput, TouchableOpacity, Alert } from "react-native";
import styles from "../../styles/SpaceCreateStyles";
import { createSpace } from "../../services/spaces";
import { api } from "../../services/api";
import { useNavigation } from "@react-navigation/native";
import { Picker } from "@react-native-picker/picker";
import { Snackbar } from "react-native-paper";

export default function SpaceCreateScreen() {
  const navigation = useNavigation();
  const [name, setName] = useState("");
  const [description, setDescription] = useState("");
  const [visibility, setVisibility] = useState("private");
  const [pending, setPending] = useState(false);
  const [formError, setFormError] = useState(null);

  const onSubmit = async () => {
    if (!name.trim()) {
      Alert.alert("Nom requis", "Merci de saisir un nom.");
      return;
    }
    setPending(true);
    try {
      // 1) Créer l'espace
      const space = await createSpace({
        name: name.trim(),
        description: description.trim() || null,
        logo: "default.png",
        visibility,
        status: "active",
      });

      // securité au cas où le service renverrait {space:{...}}
      const spaceObj = space?.id ? space : space?.space || space;
      const spaceId = spaceObj?.id;
      if (!spaceId) {
        throw new Error("ID de l'espace introuvable après création");
      }

      // 2) (optionnel) afficher le vrai nom si tu le veux
      let displayName = "Moi";
      try {
        const { data: me } = await api.get("/user/me"); // baseURL contient déjà /api
        displayName = [me?.firstname, me?.lastname].filter(Boolean).join(" ") || "Moi";
      } catch {
        // pas bloquant
      }

      // 3) Créer le membre owner — EN JSON avec les CLÉS ATTENDUES
      try {
        await api.post("/member/create", {
          name: displayName,        // requis
          relationship: "self",     // requis (⚠️ pas "relation")
          space_id: String(spaceId) // requis (⚠️ pas "space")
          // date_of_birth: "1990-01-01" // optionnel
        });
      } catch (e) {
        console.log("⚠️ createMember(owner) a échoué:", e?.response?.data || e.message);
        // On continue: l'espace est créé quand même
      }

      // 4) Aller sur les détails
      navigation.replace("SpaceDetails", { id: spaceId, refreshAt: Date.now() });
    } catch (e) {
    const status = e?.response?.status;
    const msg = e?.response?.data?.error || e.message || "Création impossible";
    setFormError(msg);

    // Alert cross-platform (web + natif)
    if (typeof window !== "undefined") {
      // web
      window.alert(msg);
    } else {
      Alert.alert("Erreur", msg);
    }
  } finally {
    setPending(false);
  }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Créer un espace</Text>

      <Text style={styles.label}>Nom</Text>
      <TextInput
        value={name}
        onChangeText={setName}
        placeholder="Ex: Famille, Coloc, Projet…"
        placeholderTextColor="#777"
        style={styles.input}
      />
      <Snackbar visible={!!formError} onDismiss={()=>setFormError(null)}>
        {formError}
      </Snackbar>


      <Text style={styles.label}>Description (optionnel)</Text>
      <TextInput
        value={description}
        onChangeText={setDescription}
        placeholder="Quelques mots…"
        placeholderTextColor="#777"
        style={[styles.input, { height: 100 }]}
        multiline
      />

      <Text style={styles.label}>Visibilité</Text>
      <Picker
        selectedValue={visibility}
        onValueChange={(v) => setVisibility(v)}
        style={styles.input}
      >
        <Picker.Item label="Privé" value="private" />
        <Picker.Item label="Public" value="public" />
      </Picker>

      <TouchableOpacity style={styles.cta} onPress={onSubmit} disabled={pending}>
        <Text style={styles.ctaText}>{pending ? "Création..." : "Créer"}</Text>
      </TouchableOpacity>

      <TouchableOpacity style={styles.link} onPress={() => navigation.goBack()}>
        <Text style={styles.linkText}>Annuler</Text>
      </TouchableOpacity>
    </View>
  );
}

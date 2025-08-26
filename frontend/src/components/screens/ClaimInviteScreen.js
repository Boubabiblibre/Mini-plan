// src/components/screens/ClaimInviteScreen.js
import React, { useEffect, useState } from "react";
import { View, Text, ActivityIndicator } from "react-native";
import { useRoute, useNavigation } from "@react-navigation/native";
import { claimInvite } from "../../services/invitations";
import styles from "../../styles/SpaceDetailsStyles";

export default function ClaimInviteScreen() {
  const { params } = useRoute();
  const navigation = useNavigation();
  const token = params?.token;
  const [msg, setMsg] = useState("Validation de l’invitation…");

  useEffect(() => {
    (async () => {
      try {
        const res = await claimInvite(token);
        setMsg("Invitation acceptée !");
        navigation.replace("SpaceDetails", { id: res.space.id, refreshAt: Date.now() });
      } catch (e) {
        setMsg(e?.response?.data?.error || "Invitation invalide");
      }
    })();
  }, [token]);

  return (
    <View style={[styles.container, { alignItems: "center", justifyContent: "center" }]}>
      <ActivityIndicator />
      <Text style={{ color: "#fff", marginTop: 10 }}>{msg}</Text>
    </View>
  );
}

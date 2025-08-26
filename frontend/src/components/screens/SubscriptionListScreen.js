// frontend/src/components/screens/SubscriptionListScreen.js
import React, { useEffect, useState, useCallback } from "react";
import { useFocusEffect } from "@react-navigation/native";
import {
  View, Text, TextInput, TouchableOpacity, FlatList,
  SafeAreaView, ActivityIndicator, Alert,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import styles from "../../styles/SubscriptionListStyles";
import useCookieHook from "../../hooks/use-cookie";
import { Platform } from "react-native";
import { useRoute } from '@react-navigation/native';
import { listMembers as listMembersBySpace } from '../../services/members';


import { api } from "../../services/api";
import {
  createSubscription,
  listSubscriptions,
  deleteSubscription,          // 👈 import
} from "../../services/subscriptions";

// même helper que plus tôt
async function bootstrapServiceAndMember() {
  let services = await api.get("/service/all").then(r => r.data).catch(() => []);
  let service = services?.[0];
  if (!service) {
    const { data } = await api.post("/service/create", {
      name: "Netflix",
      description: "SVOD",
      provider: "Netflix Inc.",
      logo: "netflix.png",
      website: "https://www.netflix.com",
      status: "active",
      currency: "EUR",
      category_id: null
    });
    service = data;
  }

  const spaces = await api.get("/space/all").then(r => r.data);
  const space = spaces?.[0];
  if (!space) throw new Error("Aucun espace n'existe. Crée un Space d'abord.");

  let members = await api.get("/member/all").then(r => r.data).catch(() => []);
  let member = members?.find(m => m?.space?.id === space.id) || members?.[0];

  if (!member) {
    const { data } = await api.post("/member/create", {
      space_id: space.id,
      role: "owner"
    });
    member = data;
  }

  return { serviceId: service.id, memberId: member.id };
}

const SubscriptionListScreen = ({ navigation }) => {
  const { loading: authLoading, isLogged } = useCookieHook();
  const [searchText, setSearchText] = useState("");
  const [subscriptions, setSubscriptions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const route = useRoute();
  const spaceId = route.params?.spaceId || null;

  const fetchData = async () => {
    setLoading(true);
    setError(null);
    try {
      const [subsAll, membersInSpace] = await Promise.all([
        listSubscriptions(),
        spaceId ? listMembersBySpace(spaceId) : Promise.resolve([]),
      ]);

      let data = Array.isArray(subsAll) ? subsAll : [];
      if (spaceId) {
        const ids = new Set((membersInSpace || []).map(m => String(m.id)));
        data = data.filter(s => ids.has(String(s.member_id)));
      }
      setSubscriptions(data);
    } catch (err) {
      setError("Impossible de charger les abonnements");
    } finally {
      setLoading(false);
    }
  };


  useEffect(() => {
    if (!authLoading && !isLogged) {
      navigation?.navigate?.("Login");
    }
  }, [authLoading, isLogged, navigation]);

  useEffect(() => {
    if (!authLoading && isLogged) fetchData();
    else if (!authLoading && !isLogged) setLoading(false);
  }, [authLoading, isLogged]);

  useFocusEffect(
    useCallback(() => {
      if (!authLoading && isLogged) fetchData();
    }, [authLoading, isLogged])
  );

  const filtered = subscriptions.filter((sub) =>
    sub.name?.toLowerCase().includes(searchText.toLowerCase())
  );

  const money = (amount, currency = "EUR") =>
    amount == null
      ? "—"
      : new Intl.NumberFormat("fr-FR", { style: "currency", currency }).format(Number(amount));

  // utilitaire confirm cross-platform
const confirm = async (title, message) => {
  if (Platform.OS === "web") {
    return window.confirm(`${title}\n\n${message}`);
  }
  return new Promise((resolve) => {
    Alert.alert(
      title,
      message,
      [
        { text: "Annuler", style: "cancel", onPress: () => resolve(false) },
        { text: "Supprimer", style: "destructive", onPress: () => resolve(true) },
      ],
      { cancelable: true }
    );
  });
};

// remplace ta confirmAndDelete par ça
const confirmAndDelete = async (item) => {
  console.log("🗑️ suppression directe", item?.id);

  const previous = subscriptions;
  setSubscriptions((arr) => arr.filter((s) => s.id !== item.id)); // optimiste
  try {
    await deleteSubscription(item.id);
  } catch (e) {
    setSubscriptions(previous); // rollback
    alert(e?.response?.data?.error || e.message);
  }
};


  const renderSubscriptionItem = ({ item }) => (
  <View style={[styles.row, { flexDirection: "row", alignItems: "center" }]}>
    {/* Zone navigation (titre + sous-titre) */}
    <TouchableOpacity
      onPress={() => navigation.navigate("SubscriptionDetails", { id: item.id, subscription: item })}
      style={{ flexGrow: 1, flexShrink: 1, flexBasis: 0, flexDirection: "row", alignItems: "center" }}
      hitSlop={{ top: 6, bottom: 6, left: 6, right: 6 }}
    >
      <View style={styles.leftIcon}>
        <Ionicons name="card" size={24} color="#72CE1D" />
      </View>
      <View style={{ flex: 1, minWidth: 0 }}>
        <Text style={styles.title} numberOfLines={1}>{item.name || "Sans nom"}</Text>
        <Text style={styles.subtitle}>
          {money(item.amount, item.currency)}
          {item.billing_frequency ? ` · ${item.billing_frequency}` : ""}
        </Text>
      </View>
    </TouchableOpacity>

    {/* Corbeille */}
    <TouchableOpacity
      onPress={() => confirmAndDelete(item)}
      style={{ paddingHorizontal: 8, paddingVertical: 6, marginRight: 2 }}
      hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
    >
      <Ionicons name="trash-outline" size={20} color="#C62828" />
    </TouchableOpacity>

    {/* Flèche → navigue aussi */}
    <TouchableOpacity
      onPress={() => navigation.navigate("SubscriptionDetails", { id: item.id, subscription: item })}
      style={{ paddingHorizontal: 8, paddingVertical: 6 }}
      hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
    >
      <Ionicons name="chevron-forward" size={20} color="#9aa0a6" />
    </TouchableOpacity>
  </View>
  );



  if (authLoading || loading) {
    return (
      <SafeAreaView style={styles.container}>
        <ActivityIndicator size="large" color="#72CE1D" />
        <Text style={{ color: "#72CE1D", marginTop: 8 }}>Chargement…</Text>
      </SafeAreaView>
    );
  }

  if (error) {
    return (
      <SafeAreaView style={styles.container}>
        <Text style={{ color: "red" }}>{error}</Text>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      {/* Recherche */}
      <View style={styles.searchContainer}>
        <TextInput
          style={styles.searchInput}
          placeholder="Rechercher..."
          placeholderTextColor="#72CE1D"
          value={searchText}
          onChangeText={setSearchText}
        />
        <TouchableOpacity style={styles.searchButton} onPress={() => {}}>
          <Ionicons name="search" size={24} color="#72CE1D" />
        </TouchableOpacity>
      </View>

      {/* Liste */}
      <FlatList
        data={filtered}
        keyExtractor={(item) => String(item.id)}
        renderItem={renderSubscriptionItem}
        contentContainerStyle={styles.listContentContainer}
        ListEmptyComponent={<Text style={{ color: "#aaa" }}>Aucun abonnement trouvé.</Text>}
      />
    </SafeAreaView>
  );
};

export default SubscriptionListScreen;

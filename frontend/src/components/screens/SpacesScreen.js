import React, { useCallback, useState } from "react";
import { View, Text, TouchableOpacity, FlatList, RefreshControl } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import styles from "../../styles/SpacesStyles";
import useCookieHook from "../../hooks/use-cookie";
import { useFocusEffect, useNavigation } from "@react-navigation/native";
import { getAllSpaces } from "../../services/api";
import { getSpaceById, listSpaces } from "../../services/spaces";

export default function SpacesScreen() {
  const navigation = useNavigation();
  const { isLogged, loading } = useCookieHook();
  const [spaces, setSpaces] = useState([]);
  const [pending, setPending] = useState(false);
  const [error, setError] = useState(null);

  const load = useCallback(async () => {
    if (!isLogged) return;
    setPending(true);
    setError(null);
    try {
      const data = await getAllSpaces();
      setSpaces(Array.isArray(data) ? data : data?.items || []);
    } catch (e) {
      setError(e.message || "Impossible de charger les espaces.");
    } finally {
      setPending(false);
    }
  }, [isLogged]);

  // refresh à chaque focus
  useFocusEffect(
    useCallback(() => {
      load();
    }, [load])
  );

  if (loading) {
    return (
      <View style={styles.center}>
        <Text style={styles.muted}>Chargement...</Text>
      </View>
    );
  }

  if (!isLogged) {
    return (
      <View style={styles.center}>
        <Text style={styles.muted}>Connecte-toi pour voir tes espaces.</Text>
        <TouchableOpacity style={styles.cta} onPress={() => navigation.navigate("Login")}>
          <Text style={styles.ctaText}>Se connecter</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <View style={styles.tabsContainer}>
      {/* "Vous" = inactif ici, renvoie vers le dashboard */}
      <TouchableOpacity
        style={styles.inactiveTab}
        onPress={() =>
          navigation.reset({
            index: 0,
            routes: [{ name: 'Dashboard' }],
          })
        }
      >
        <Text style={styles.inactiveTabText}>Vous</Text>
      </TouchableOpacity>


  {/* "Espaces" = actif (on est déjà sur cette page) */}
  <TouchableOpacity style={styles.activeTab} disabled>
    <Text style={styles.activeTabText}>Espaces</Text>
  </TouchableOpacity>
</View>

      {/* Header */}
      <View style={styles.headerRow}>
        <Text style={styles.title}>Vos espaces</Text>
        <TouchableOpacity style={styles.newBtn} onPress={() => navigation.navigate("SpaceCreate")}>
          <Ionicons name="add" size={18} />
          <Text style={styles.newBtnText}>Créer</Text>
        </TouchableOpacity>
      </View>

      {error ? <Text style={styles.error}>{error}</Text> : null}

      <FlatList
        data={spaces}
        keyExtractor={(it) => it.id}
        refreshControl={<RefreshControl refreshing={pending} onRefresh={load} />}
        ListEmptyComponent={
          !pending && (
            <View style={styles.emptyBox}>
              <Text style={styles.muted}>Aucun espace pour le moment.</Text>
            </View>
          )
        }
        renderItem={({ item }) => (
          <TouchableOpacity
            style={styles.card}
            onPress={() => {
              console.log('[NAV] SpaceDetails ->', item.id);
              navigation.navigate("SpaceDetails", { id: item.id });
            }}
          >
            <View style={styles.logoCircle}>
              <Text style={styles.logoLetter}>{(item.name || "?").charAt(0).toUpperCase()}</Text>
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.cardTitle}>{item.name}</Text>
              <Text style={styles.cardSub}>
                {item.members_count != null ? `${item.members_count} membre(s)` : item.description || "—"}
              </Text>
            </View>
            <Ionicons name="chevron-forward" size={18} color="#aaa" />
          </TouchableOpacity>
        )}
      />
    </View>
  );
}

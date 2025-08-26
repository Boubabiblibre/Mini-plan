// src/components/screens/AddSubscriptionScreen.js
import React, { useEffect, useMemo, useState } from "react";
import { View, Text, TouchableOpacity, TextInput, SafeAreaView, Alert, Switch, Platform, ScrollView, KeyboardAvoidingView } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import DateTimePicker from "@react-native-community/datetimepicker";
import { useRoute, useNavigation } from "@react-navigation/native";
import styles from "../../styles/CustomSubscriptionStyles";
import ModalSelect from "../shared/ModalSelect";
import { getServiceIconUrl } from "../../util/serviceIcon";
import { Image } from "react-native";
import { createSubscription } from "../../services/subscriptions";
import { listServices } from "../../services/services";
import { api } from "../../services/api";
import { listMembersBySpace } from "../../services/members";
import { useAccountStore } from "../../store/account";

const CURRENCIES = ["EUR", "USD", "GBP", "CAD", "AUD"];
const FREQUENCIES = ["monthly", "yearly"];
const BILLING_MODES = ["unknown", "credit_card", "sepa", "paypal", "cash", "other"];
const STATUSES = ["active", "inactive", "cancelled", "expired"];

const twoDecimals = (v) => {
  const n = Number(String(v).replace(",", "."));
  return Number.isNaN(n) ? "0.00" : n.toFixed(2);
};
const fmtDate = (d) => (d instanceof Date ? d.toISOString().slice(0, 10) : d || "");
const parseYMD = (s) => {
  if (!s || !/^\d{4}-\d{2}-\d{2}$/.test(s)) return new Date();
  const [y, m, d] = s.split("-").map((x) => parseInt(x, 10));
  return new Date(y, m - 1, d);
};

export default function AddSubscriptionScreen() {
  const route = useRoute();
  const navigation = useNavigation();
  const { account } = useAccountStore();

  // On accepte des params mais on peut s’en passer
  const [memberId, setMemberId] = useState(route.params?.memberId ?? null);
  const [spaceId, setSpaceId] = useState(route.params?.spaceId ?? null);
  const [resolving, setResolving] = useState(false);

  // form
  const [serviceId, setServiceId] = useState(null);
  const [services, setServices] = useState([]);
  const [showService, setShowService] = useState(false);

  const [name, setName] = useState("");
  const [notes, setNotes] = useState("");
  const [amount, setAmount] = useState("");
  const [currency, setCurrency] = useState("EUR");
  const [cycle, setCycle] = useState("monthly");
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");
  const [billingMode, setBillingMode] = useState("unknown");
  const [autoRenewal, setAutoRenewal] = useState(false);
  const [status, setStatus] = useState("active");
  const [showStartPicker, setShowStartPicker] = useState(false);
  const [showEndPicker, setShowEndPicker] = useState(false);
  const [showCurrency, setShowCurrency] = useState(false);
  const [showCycle, setShowCycle] = useState(false);
  const [showBilling, setShowBilling] = useState(false);
  const [showStatusModal, setShowStatusModal] = useState(false);

  const title = useMemo(() => "Nouvel abonnement", []);
  useEffect(() => { navigation.setOptions({ title }); }, [navigation, title]);

  // Charger services
  useEffect(() => {
    (async () => {
      try {
        const list = await listServices();
        setServices(list);
        if (!serviceId && list.length) setServiceId(list[0]?.id ?? null);
      } catch (e) {
        console.log("Services load error:", e?.response?.data || e.message);
      }
    })();
  }, []);

  // ⚠️ Plus de "goBack" si memberId est vide.
  // → On résout automatiquement le premier espace où je suis membre.
  useEffect(() => {
    if (memberId) return;
    setResolving(true);
    (async () => {
      try {
        // 1) Liste des espaces
        const { data } = await api.get("space/all"); // chemin relatif (pas de slash initial)
        const spaces = Array.isArray(data) ? data : (data?.items ?? []);

        // 2) Tester d'abord spaceId reçu en param s'il existe
        const ordered = spaceId
          ? [{ id: spaceId }, ...spaces.filter(s => s.id !== spaceId)]
          : spaces;

        // 3) Trouver mon Member (premier match) par indices inline...
        for (const sp of ordered) {
          const inline =
            sp.my_member_id ||
            sp.meMemberId ||
            sp.members?.find?.(m => m?.is_me)?.id ||
            sp.members?.find?.(m => m?.user?.id === account?.id)?.id ||
            (account?.email ? sp.members?.find?.(m => m?.user?.email === account.email)?.id : null);

          if (inline) { setMemberId(inline); setSpaceId(prev => prev ?? sp.id); return; }

          // ...puis fallback via API membres
          try {
            const list = await listMembersBySpace(sp.id); // doit renvoyer un array
            const mine =
              list.find(m => m?.is_me) ||
              list.find(m => m?.user?.id === account?.id) ||
              (account?.email ? list.find(m => m?.user?.email === account.email) : null);
            if (mine?.id) { setMemberId(mine.id); setSpaceId(prev => prev ?? sp.id); return; }
          } catch (_) { }
        }
        // Rien trouvé → on reste sur l’écran; la validation bloquera "Créer".
      } catch (e) {
        console.log("resolve memberId err:", e?.response?.data || e.message);
      } finally {
        setResolving(false);
      }
    })();
  }, []);

  const validate = () => {
    // if (!memberId) return "Membre introuvable pour cet espace.";
    if (!serviceId) return "Choisis un service.";
    if (!name || name.trim().length < 2) return "Le nom est requis (≥ 2 caractères).";
    if (!startDate || !/^\d{4}-\d{2}-\d{2}$/.test(startDate)) return "La date de début doit être au format YYYY-MM-DD.";
    if (!FREQUENCIES.includes(cycle)) return "Fréquence invalide.";
    if (!CURRENCIES.includes(currency)) return "Devise invalide.";
    if (!STATUSES.includes(status)) return "Statut invalide.";
    if (!BILLING_MODES.includes(billingMode)) return "Mode de facturation invalide.";
    return null;
  };

  const handleCreate = async () => {
    const err = validate();
    if (err) return Alert.alert("Validation", err);

    const payload = {
      service_id: serviceId,
      name: name.trim(),
      subscription_type: "custom",
      start_date: startDate,
      end_date: endDate || null,
      billing_mode: billingMode,
      billing_frequency: cycle,
      auto_renewal: !!autoRenewal,
      status,
      notes: notes?.trim() || null,
      amount: twoDecimals(amount || "0"),
      currency,
      ...(memberId ? { member_id: memberId } : {}), // ← seulement si présent
    };

    try {
      const { data } = await createSubscription(payload);
      Alert.alert("Succès", "Abonnement créé.");
      const newId = data?.id;
      if (newId) {
        navigation.navigate("SubscriptionDetails", { id: newId, refreshAt: Date.now() });
      } else if (navigation.canGoBack()) {
        navigation.goBack();
      }
    } catch (e) {
      console.log("❌ create err:", e?.response?.data || e.message);
      Alert.alert("Erreur", e?.response?.data?.error || e.message);
    }
  };

  const serviceItems = services.map((s) => ({
    label: s.name ?? s.title ?? s.id,
    value: s.id,
    icon: getServiceIconUrl(s),
  }));
  const selectedItem = serviceItems.find(o => o.value === serviceId);
  const textWhite = { color: "#fff" };
  const placeholderCol = "#8e8e8e";

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: "#0a0a0a" }]}>
      <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={{ paddingBottom: 24 }} keyboardShouldPersistTaps="handled">
          <View style={styles.formContainer}>
        {resolving ? (
          <Text style={{ color: "#9cdcfe", marginBottom: 8 }}>Préparation…</Text>
        ) : null}

        {/* Service */}
        <Text style={[styles.label, textWhite]}>Service</Text>
        <TouchableOpacity
          style={[styles.inputRow, { justifyContent: "flex-start", gap: 10 }]}
          onPress={() => setShowService(true)}
        >
          {!!selectedItem?.icon && (
            <Image source={{ uri: selectedItem.icon }} style={{ width: 20, height: 20, borderRadius: 4 }} />
          )}
          <Text style={styles.valueText}>{selectedItem?.label || "Sélectionner"}</Text>
        </TouchableOpacity>

        {/* Nom */}
        <Text style={[styles.label, textWhite]}>Nom</Text>
        <TextInput
          style={[styles.input, textWhite]}
          placeholder="Nom de l’abonnement"
          placeholderTextColor={placeholderCol}
          value={name}
          onChangeText={setName}
        />

        {/* Notes */}
        <Text style={[styles.label, textWhite]}>Notes</Text>
        <TextInput
          style={[styles.input, { height: 90, textAlignVertical: "top" }]}
          placeholder="Notes (optionnel)"
          placeholderTextColor={placeholderCol}
          value={notes}
          onChangeText={setNotes}
          multiline
        />

        {/* Montant + Devise */}
        <Text style={[styles.label, textWhite]}>Montant</Text>
        <TextInput
          style={[styles.input, textWhite]}
          placeholder="0.00"
          placeholderTextColor={placeholderCol}
                  keyboardType="decimal-pad"
          value={amount}
          onChangeText={setAmount}
        />

        <Text style={[styles.label, textWhite]}>Devise</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowCurrency(true)}>
          <Text style={[styles.valueText, textWhite]}>{currency}</Text>
          <Ionicons name="chevron-down" size={20} color="#72CE1D" />
        </TouchableOpacity>

        <Text style={[styles.label, textWhite]}>Cycle</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowCycle(true)}>
          <Text style={[styles.valueText, textWhite]}>{cycle}</Text>
          <Ionicons name="chevron-down" size={20} color="#72CE1D" />
        </TouchableOpacity>

        {/* Dates */}
        <Text style={[styles.label, textWhite]}>Début</Text>
        {Platform.OS === "web" ? (
          <TextInput
            style={[styles.input, textWhite]}
            placeholder="YYYY-MM-DD"
            placeholderTextColor={placeholderCol}
            value={startDate}
            onChangeText={setStartDate}
          />
        ) : (
          <TouchableOpacity style={styles.inputRow} onPress={() => setShowStartPicker(true)}>
            <Text style={[styles.valueText, textWhite]}>{startDate || "YYYY-MM-DD"}</Text>
            <Ionicons name="calendar" size={20} color="#72CE1D" />
          </TouchableOpacity>
        )}

        <Text style={[styles.label, textWhite]}>Fin (optionnel)</Text>
        {Platform.OS === "web" ? (
          <TextInput
            style={[styles.input, textWhite]}
            placeholder="YYYY-MM-DD"
            placeholderTextColor={placeholderCol}
            value={endDate}
            onChangeText={setEndDate}
          />
        ) : (
          <TouchableOpacity style={styles.inputRow} onPress={() => setShowEndPicker(true)}>
            <Text style={[styles.valueText, textWhite]}>{endDate || "—"}</Text>
            <Ionicons name="calendar" size={20} color="#72CE1D" />
          </TouchableOpacity>
        )}

        <Text style={[styles.label, textWhite]}>Mode de facturation</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowBilling(true)}>
          <Text style={[styles.valueText, textWhite]}>{billingMode}</Text>
          <Ionicons name="chevron-down" size={20} color="#72CE1D" />
        </TouchableOpacity>

        {/* Renouvellement auto */}
        <View style={[styles.inputRow, { justifyContent: "space-between" }]}>
          <Text style={[styles.label, textWhite]}>Renouvellement auto</Text>
          <Switch value={autoRenewal} onValueChange={setAutoRenewal} />
        </View>

        <Text style={[styles.label, textWhite]}>Statut</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowStatusModal(true)}>
          <Text style={[styles.valueText, textWhite]}>{status}</Text>
          <Ionicons name="chevron-down" size={20} color="#72CE1D" />
        </TouchableOpacity>

      {/* Modal service */}
      <ModalSelect
        visible={showService}
        title="Service"
        items={serviceItems}
        value={selectedItem?.label}
        onChangeItem={(item) => setServiceId(item.value)}
        onClose={() => setShowService(false)}
        maxHeight={360}     // ← limite la hauteur
        compact             // ← padding réduit
        columns={2}         // ← grille 2 colonnes
        searchable          // ← champ de recherche
      />
      {/* Devise */}
      <ModalSelect
        visible={showCurrency}
        title="Devise"
        options={CURRENCIES}
        value={currency}
        onChange={(v) => { setCurrency(v); setShowCurrency(false); }}
        onClose={() => setShowCurrency(false)}
        compact
        columns={3}
      />

      {/* Cycle */}
      <ModalSelect
        visible={showCycle}
        title="Cycle"
        options={FREQUENCIES}
        value={cycle}
        onChange={(v) => { setCycle(v); setShowCycle(false); }}
        onClose={() => setShowCycle(false)}
        compact
        columns={2}
      />

      {/* Mode de facturation */}
      <ModalSelect
        visible={showBilling}
        title="Mode de facturation"
        options={BILLING_MODES}
        value={billingMode}
        onChange={(v) => { setBillingMode(v); setShowBilling(false); }}
        onClose={() => setShowBilling(false)}
        compact
        columns={2}
      />

      {/* Statut */}
      <ModalSelect
        visible={showStatusModal}
        title="Statut"
        options={STATUSES}
        value={status}
        onChange={(v) => { setStatus(v); setShowStatusModal(false); }}
        onClose={() => setShowStatusModal(false)}
        compact
        columns={2}
      />


      {/* Date pickers */}
      {showStartPicker && (
        <DateTimePicker
          value={parseYMD(startDate || fmtDate(new Date()))}
          mode="date"
          display={Platform.OS === "ios" ? "spinner" : "calendar"}
          onChange={(_, d) => {
            setShowStartPicker(Platform.OS === "ios");
            if (d) setStartDate(fmtDate(d));
          }}
        />
      )}
      {showEndPicker && (
        <DateTimePicker
          value={parseYMD(endDate || fmtDate(new Date()))}
          mode="date"
          display={Platform.OS === "ios" ? "spinner" : "calendar"}
          onChange={(_, d) => {
            setShowEndPicker(Platform.OS === "ios");
            if (d) setEndDate(fmtDate(d));
          }}
        />
      )}
   </View>
        </ScrollView>
        <TouchableOpacity style={styles.button} onPress={handleCreate}>
          <Text style={styles.buttonText}>Créer</Text>
        </TouchableOpacity>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

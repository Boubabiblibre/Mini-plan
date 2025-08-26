// src/components/screens/CustomSubscriptionScreen.js
import React, { useEffect, useMemo, useState } from "react";
import { View, Text, TouchableOpacity, TextInput, SafeAreaView, Alert, Switch, Platform } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import DateTimePicker from "@react-native-community/datetimepicker";
import styles from "../../styles/CustomSubscriptionStyles";
import { useRoute, useNavigation } from "@react-navigation/native";
import { createSubscription, updateSubscription } from "../../services/subscriptions";
import { listServices } from "../../services/services";
import { listMembersBySpace } from "../../services/members";
import ModalSelect from "../shared/ModalSelect";
// si tu as un store utilisateur, décommente la ligne suivante et adapte le chemin :
// import { useAccountStore } from "../../store/account";

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

export default function CustomSubscriptionScreen() {
  const route = useRoute();
  const navigation = useNavigation();

  // params attendus
  const editingId = route.params?.id ?? route.params?.snapshot?.id ?? null;
  const editingSub = route.params?.snapshot ?? null;
  const isEdit = !!editingSub;

  const spaceId = route.params?.spaceId ?? null;
  const paramMemberId = route.params?.memberId ?? route.params?.myMemberId ?? null;

  // const me = useAccountStore.getState()?.me; // si tu as un store
  const me = null; // ← si pas de store, on déduit via members + route param

  // form
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

  // service
  const [services, setServices] = useState([]);
  const [serviceId, setServiceId] = useState(null);
  const [showService, setShowService] = useState(false);

  // member (interne seulement, pas d’UI)
  const [memberId, setMemberId] = useState(paramMemberId);

  // date pickers
  const [showStartPicker, setShowStartPicker] = useState(false);
  const [showEndPicker, setShowEndPicker] = useState(false);

  // Prefill (edit)
  useEffect(() => {
    if (!isEdit) return;
    setName(editingSub.name ?? "");
    setNotes(editingSub.notes ?? "");
    setAmount(
      editingSub.amount != null
        ? String(editingSub.amount)
        : editingSub.amountFloat != null
        ? String(editingSub.amountFloat)
        : ""
    );
    setCurrency(editingSub.currency ?? "EUR");
    setCycle(editingSub.billing_frequency ?? "monthly");
    setStartDate(editingSub.start_date ?? "");
    setEndDate(editingSub.end_date ?? "");
    setBillingMode(editingSub.billing_mode ?? "unknown");
    setAutoRenewal(Boolean(editingSub.auto_renewal));
    setStatus(editingSub.status ?? "active");

    setServiceId(editingSub.service?.id ?? null);
    setMemberId((prev) => prev ?? editingSub.member?.id ?? null);
  }, [isEdit, editingSub]);

  // Charger Services
  useEffect(() => {
    (async () => {
      try {
        const list = await listServices();
        setServices(list);
        if (!serviceId && !isEdit && list.length) setServiceId(list[0]?.id ?? null);
      } catch (e) {
        console.log("Services load error:", e?.response?.data || e.message);
      }
    })();
  }, []);

  // Déduire automatiquement MON memberId si non fourni
  useEffect(() => {
    if (memberId || !spaceId) return;
    (async () => {
      try {
        const list = await listMembersBySpace(spaceId);
        // si tu as `me`, prends le member où m.user.id === me.id ; sinon, s'il n'y a qu'un member lié au user courant, le backend pourrait avoir un endpoint /member/me
        // Sans store "me", on tente une heuristique: s'il n'y a qu'un seul membre avec un user flaggé "me" côté API, adapte-ci dessous.
        const mine = list.find((m) => m.is_me === true) // si ton API renvoie ce flag
                  || list.find((m) => m.user?.isCurrent === true) // autre possibilité
                  || list.find((m) => m.user?.id && m.user?.me === true);
        if (mine?.id) setMemberId(mine.id);
      } catch (e) {
        console.log("Members load error:", e?.response?.data || e.message);
      }
    })();
  }, [memberId, spaceId]);

  const title = useMemo(() => (isEdit ? "Éditer l’abonnement" : "Nouvel abonnement"), [isEdit]);
  useEffect(() => { navigation.setOptions({ title }); }, [navigation, title]);

  const validate = (isCreate) => {
    if (isCreate && !serviceId) return "Choisis un service.";
    if (isCreate && !memberId) return "Tu n’es pas membre de cet espace. Demande à l’admin de t’ajouter.";
    if (!name || name.trim().length < 2) return "Le nom est requis (≥ 2 caractères).";
    if (!startDate || !/^\d{4}-\d{2}-\d{2}$/.test(startDate)) return "La date de début doit être au format YYYY-MM-DD.";
    if (!FREQUENCIES.includes(cycle)) return "Fréquence invalide.";
    if (!CURRENCIES.includes(currency)) return "Devise invalide.";
    if (!STATUSES.includes(status)) return "Statut invalide.";
    if (!BILLING_MODES.includes(billingMode)) return "Mode de facturation invalide.";
    return null;
  };

  const handleSave = async () => {
    const isCreate = !isEdit;
    const err = validate(isCreate);
    if (err) return Alert.alert("Validation", err);

    const payloadCommon = {
      name: name.trim(),
      notes: notes?.trim() || null,
      amount: twoDecimals(amount || "0"),
      currency,
      subscription_type: editingSub?.subscription_type ?? "custom",
      start_date: startDate,
      end_date: endDate || null,
      billing_mode: billingMode,
      billing_frequency: cycle,
      auto_renewal: !!autoRenewal,
      status,
    };

    try {
      if (isEdit) {
        if (!editingId) throw new Error("ID d’abonnement manquant.");
        await updateSubscription(editingId, payloadCommon);
        Alert.alert("Succès", "Abonnement modifié.");
        if (navigation.canGoBack()) navigation.goBack();
        navigation.navigate("SubscriptionDetails", { id: editingId, refreshAt: Date.now() });
      } else {
        // CREATE → on envoie member_id (fixé automatiquement) + service_id
        if (!memberId) throw new Error("Tu n’es pas membre de cet espace.");
        const { data } = await createSubscription({
          ...payloadCommon,
          member_id: memberId,
          service_id: serviceId,
        });
        Alert.alert("Succès", "Abonnement créé.");
        const newId = data?.id;
        if (newId) {
          navigation.navigate("SubscriptionDetails", { id: newId, refreshAt: Date.now() });
        } else if (navigation.canGoBack()) {
          navigation.goBack();
        }
      }
    } catch (e) {
      console.log("❌ save err:", e?.response?.data || e.message);
      Alert.alert("Erreur", e?.response?.data?.error || e.message);
    }
  };

  const serviceOptions = services.map((s) => ({ label: s.name ?? s.title ?? s.id, value: s.id }));

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.formContainer}>
        {/* Service */}
        <Text style={styles.label}>Service</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowService(true)}>
          <Text style={styles.valueText}>
            {serviceOptions.find(o => o.value === serviceId)?.label || "Sélectionner"}
          </Text>
          <Ionicons name="chevron-down" size={20} color="#72CE1D" />
        </TouchableOpacity>

        {/* Nom */}
        <Text style={styles.label}>Nom</Text>
        <TextInput style={styles.input} placeholder="Nom de l’abonnement" placeholderTextColor="#72CE1D" value={name} onChangeText={setName} />

        {/* Notes */}
        <Text style={styles.label}>Notes</Text>
        <TextInput
          style={[styles.input, { height: 90, textAlignVertical: "top" }]}
          placeholder="Notes (optionnel)"
          placeholderTextColor="#72CE1D"
          value={notes}
          onChangeText={setNotes}
          multiline
        />

        {/* Montant + Devise */}
        <Text style={styles.label}>Montant</Text>
        <TextInput
          style={styles.input}
          placeholder="0.00"
          placeholderTextColor="#72CE1D"
          keyboardType="decimal-pad"
          value={amount}
          onChangeText={setAmount}
        />

        <Text style={styles.label}>Devise</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowService(false) /* just for symmetry */}>
          <Text style={styles.valueText}>{currency}</Text>
          <Ionicons name="chevron-down" size={20} color="#72CE1D" />
        </TouchableOpacity>

        {/* Cycle */}
        <Text style={styles.label}>Cycle</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowService(false)}>
          <Text style={styles.valueText}>{cycle}</Text>
          <Ionicons name="chevron-down" size={20} color="#72CE1D" />
        </TouchableOpacity>

        {/* Dates */}
        <Text style={styles.label}>Début</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowStartPicker(true)}>
          <Text style={styles.valueText}>{startDate || "YYYY-MM-DD"}</Text>
          <Ionicons name="calendar" size={20} color="#72CE1D" />
        </TouchableOpacity>

        <Text style={styles.label}>Fin (optionnel)</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowEndPicker(true)}>
          <Text style={styles.valueText}>{endDate || "—"}</Text>
          <Ionicons name="calendar" size={20} color="#72CE1D" />
        </TouchableOpacity>

        {/* Mode de facturation */}
        <Text style={styles.label}>Mode de facturation</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowService(false)}>
          <Text style={styles.valueText}>{billingMode}</Text>
          <Ionicons name="chevron-down" size={20} color="#72CE1D" />
        </TouchableOpacity>

        {/* Renouvellement auto */}
        <View style={[styles.inputRow, { justifyContent: "space-between" }]}>
          <Text style={styles.label}>Renouvellement auto</Text>
          <Switch value={autoRenewal} onValueChange={setAutoRenewal} />
        </View>

        {/* Statut */}
        <Text style={styles.label}>Statut</Text>
        <TouchableOpacity style={styles.inputRow} onPress={() => setShowService(false)}>
          <Text style={styles.valueText}>{status}</Text>
          <Ionicons name="chevron-down" size={20} color="#72CE1D" />
        </TouchableOpacity>
      </View>

      <TouchableOpacity style={styles.button} onPress={handleSave}>
        <Text style={styles.buttonText}>{isEdit ? "Enregistrer" : "Créer"}</Text>
      </TouchableOpacity>

      {/* Modal Service */}
      <ModalSelect
        visible={showService}
        title="Service"
        options={serviceOptions.map(o => o.label)}
        value={serviceOptions.find(o => o.value === serviceId)?.label}
        onChange={(label) => {
          const found = serviceOptions.find(o => o.label === label);
          if (found) setServiceId(found.value);
        }}
        onClose={() => setShowService(false)}
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
    </SafeAreaView>
  );
}

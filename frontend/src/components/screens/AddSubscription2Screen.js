// frontend/src/components/screens/AddSubscription2Screen.js
import React, { useState } from 'react';
import { View, Text, TouchableOpacity, SafeAreaView, Image, TextInput, Alert, ScrollView } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import styles from '../../styles/AddSubscription2Styles';
import { createSubscription } from '../../services/subscriptions.js';

export default function AddSubscription2Screen({ navigation }) {
  const [cycle, setCycle] = useState('monthly');         // monthly|yearly|weekly|daily
  const [startDate, setStartDate] = useState('');        // YYYY-MM-DD
  const [paymentMethod, setPaymentMethod] = useState('unknown'); // -> billing_mode
  const [remindMe, setRemindMe] = useState('Never');     // (placeholder)

  const [name, setName] = useState('YouTube Premium');
  const [amount, setAmount] = useState('11.99');
  const [currency, setCurrency] = useState('EUR');

  // provisoire : en attendant des pickers
  const [memberId, setMemberId] = useState('');
  const [serviceId, setServiceId] = useState('');

  const handleAddSubscription = async () => {
    if (!name || !cycle || !startDate || !memberId || !serviceId) {
      Alert.alert('Champs requis', 'Nom, cycle, date de début, member_id et service_id.');
      return;
    }
    try {
      await createSubscription({
        name,
        subscription_type: cycle,
        start_date: startDate,
        end_date: null,
        amount: Number(amount || 0),
        currency,
        billing_mode: paymentMethod,
        member_id: memberId,
        service_id: serviceId,
      });
      Alert.alert('Succès', 'Abonnement créé');
      navigation.goBack();
    } catch (e) {
      Alert.alert('Erreur', e?.message || 'Création impossible');
    }
  };

  const toggle = (v, a, b) => (v===a?b:a);

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView contentContainerStyle={{ paddingBottom: 24 }}>
        {/* Logo & Titre */}
        <View style={styles.logoContainer}>
          <View style={styles.logoBorder}><Image /* source={…} */ style={styles.youtubeLogo} /></View>
          <Text style={styles.title}>YOUTUBE</Text>
        </View>

        {/* Champs */}
        <View style={styles.formContainer}>
          {/* Name */}
          <View style={styles.inputRow}>
            <Text style={styles.label}>Name</Text>
            <TextInput style={styles.valueInput} value={name} onChangeText={setName} placeholder="Nom" placeholderTextColor="#72CE1D99" />
          </View>

          {/* Amount */}
          <View style={styles.inputRow}>
            <Text style={styles.label}>Amount</Text>
            <TextInput style={styles.valueInput} value={amount} onChangeText={setAmount} keyboardType="numeric" placeholder="0.00" placeholderTextColor="#72CE1D99" />
          </View>

          {/* Currency */}
          <View style={styles.inputRow}>
            <Text style={styles.label}>Currency</Text>
            <TextInput style={styles.valueInput} value={currency} onChangeText={setCurrency} placeholder="EUR" placeholderTextColor="#72CE1D99" />
          </View>

          {/* Cycle */}
          <TouchableOpacity style={styles.inputRow} onPress={()=>setCycle(toggle(cycle,'monthly','yearly'))}>
            <Text style={styles.label}>Cycle</Text>
            <View style={styles.rightSection}>
              <Text style={styles.valueText}>{cycle}</Text>
              <Ionicons name="chevron-down" size={20} color="#72CE1D" style={styles.iconRight} />
            </View>
          </TouchableOpacity>

          {/* Started on */}
          <View style={styles.inputRow}>
            <Text style={styles.label}>Started on</Text>
            <TextInput style={styles.valueInput} value={startDate} onChangeText={setStartDate} placeholder="YYYY-MM-DD" placeholderTextColor="#72CE1D99" />
          </View>

          {/* Payment Method */}
          <TouchableOpacity style={styles.inputRow} onPress={()=>setPaymentMethod(toggle(paymentMethod,'visa','paypal'))}>
            <Text style={styles.label}>Payment Method</Text>
            <View style={styles.rightSection}>
              <Text style={styles.valueText}>{paymentMethod}</Text>
              <Ionicons name="chevron-down" size={20} color="#72CE1D" style={styles.iconRight} />
            </View>
          </TouchableOpacity>

          {/* Remind me (placeholder) */}
          <TouchableOpacity style={styles.inputRow} onPress={()=>setRemindMe(toggle(remindMe,'Never','Every month'))}>
            <Text style={styles.label}>Remind me</Text>
            <View style={styles.rightSection}>
              <Text style={styles.valueText}>{remindMe}</Text>
              <Ionicons name="chevron-down" size={20} color="#72CE1D" style={styles.iconRight} />
            </View>
          </TouchableOpacity>

          {/* IDs nécessaires pour l’API actuelle */}
          <View style={styles.inputRow}>
            <Text style={styles.label}>member_id</Text>
            <TextInput style={styles.valueInput} value={memberId} onChangeText={setMemberId} placeholder="uuid" placeholderTextColor="#72CE1D99" />
          </View>
          <View style={styles.inputRow}>
            <Text style={styles.label}>service_id</Text>
            <TextInput style={styles.valueInput} value={serviceId} onChangeText={setServiceId} placeholder="uuid" placeholderTextColor="#72CE1D99" />
          </View>
        </View>

        {/* Action */}
        <TouchableOpacity style={styles.button} onPress={handleAddSubscription}>
          <Text style={styles.buttonText}>Add Subscription</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

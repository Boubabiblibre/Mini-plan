// src/components/screens/ActiveSubscriptionScreen.js
import React, { useCallback, useMemo, useState, useEffect } from 'react';
import { View, Text, FlatList, RefreshControl, Alert, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useFocusEffect, useNavigation } from '@react-navigation/native';
import SubscriptionItem from '../../components/screens/SubscriptionItem';
import { listServices } from '../../services/services';
import { listSubscriptions, deleteSubscription } from '../../services/subscriptions'; // adapte le chemin à ton service unifié
import { useAccountStore } from '../../store/account';

export default function ActiveSubscriptionScreen() {
  const navigation = useNavigation();
  const { account } = useAccountStore();
  const myUserId = account?.id ?? account?.user?.id;
  const isAdmin = Array.isArray(account?.roles) && account.roles.includes('ROLE_ADMIN');

  const [subs, setSubs] = useState([]);
  const [services, setServices] = useState([]);
  const [loading, setLoading] = useState(false);
  const [pendingId, setPendingId] = useState(null);
  const [scope, setScope] = useState('mine'); // 'mine' | 'all'


  // UI (en haut de la liste)
  {isAdmin && (
    <View style={{ flexDirection:'row', gap:8, marginBottom:12 }}>
      {['mine','all'].map(v => (
        <TouchableOpacity
          key={v}
          onPress={() => setScope(v)}
          style={{
            paddingVertical:8, paddingHorizontal:12, borderRadius:999,
            borderWidth:1, borderColor: scope===v ? '#A6FF00' : '#333',
            backgroundColor: scope===v ? 'rgba(166,255,0,0.12)' : '#121212'
          }}
        >
          <Text style={{ color: scope===v ? '#A6FF00' : '#bbb' }}>
            {v === 'mine' ? 'Mes abonnements' : 'Tous (admin)'}
          </Text>
        </TouchableOpacity>
      ))}
    </View>
  )}

  useEffect(() => { if (isAdmin) setScope('mine'); }, [isAdmin]);

  // Header back robuste (si pas d’historique → Dashboard)
  useFocusEffect(
    useCallback(() => {
      navigation.setOptions({
        title: 'Abonnements actifs',
        headerLeft: () => (
          <TouchableOpacity
            onPress={() => (navigation.canGoBack() ? navigation.goBack() : navigation.navigate('Dashboard'))}
            style={{ paddingLeft: 12 }}
          >
            <Ionicons name="arrow-back" size={24} color="#fff" />
          </TouchableOpacity>
        ),
      });
    }, [navigation])
  );

  const serviceMap = useMemo(() => {
    const m = new Map();
    (services || []).forEach(s => m.set(String(s.id), s));
    return m;
  }, [services]);

  const computeNextDue = (sub) => {
    if (!sub?.start_date) return null;
    // si inactif ou finie → pas d’échéance
    const end = sub?.end_date ? new Date(sub.end_date) : null;
    const today = new Date();
    if ((sub.status || '').toLowerCase() === 'inactive') return null;
    if (end && end < today) return null;

    let d = new Date(sub.start_date);
    const step = (sub.subscription_type || 'monthly').toLowerCase();

    const bump = () => {
      if (step === 'yearly') d.setFullYear(d.getFullYear() + 1);
      else if (step === 'weekly') d.setDate(d.getDate() + 7);
      else if (step === 'daily') d.setDate(d.getDate() + 1);
      else d.setMonth(d.getMonth() + 1);
    };

    while (d <= today) bump();
    if (end && d > end) return null;
    return d.toISOString().slice(0, 10);
  };

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const [svc, list] = await Promise.all([
        listServices(),
        listSubscriptions({ scope: isAdmin ? scope : 'mine' }),
      ]);
      setServices(Array.isArray(svc) ? svc : svc?.items || []);
      setSubs(Array.isArray(list) ? list : []);
    } finally {
      setLoading(false);
    }
  }, [scope, isAdmin]);

  useFocusEffect(useCallback(() => { load(); }, [load]));

  const Empty = () => (
    <View style={{ alignItems: 'center', paddingVertical: 40 }}>
      <Ionicons name="card-outline" size={28} color="#666" />
      <Text style={{ color: '#999', marginTop: 8 }}>Aucun abonnement actif.</Text>
      <TouchableOpacity
        onPress={() => navigation.navigate('AddSubscription')}
        style={{ marginTop: 12, backgroundColor: '#A6FF00', paddingHorizontal: 16, paddingVertical: 10, borderRadius: 999 }}
      >
        <Text style={{ fontWeight: '700' }}>Ajouter</Text>
      </TouchableOpacity>
    </View>
  );

  return (
    <View style={{ flex: 1, backgroundColor: '#000', padding: 16 }}>
      <FlatList
        data={subs}
        keyExtractor={(it) => it.id}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={load} />}
        ListEmptyComponent={!loading ? <Empty /> : null}
        renderItem={({ item }) => (
          <SubscriptionItem
            name={item.name}
            amount={item.amount}
            currency={item.currency}
            subscription_type={item.subscription_type}
            next_due={computeNextDue(item)}
            service={serviceMap.get(String(item.service_id))}
            // 1) tap sur la carte → détails
            onPress={() => navigation.navigate('SubscriptionDetails', { id: item.id })}
            // 2) bouton "Gérer" → détails
            onManage={() => navigation.navigate('SubscriptionDetails', { id: item.id })}
            // (si tu as besoin d’un état de chargement par item)
            busy={pendingId === item.id}
          />
        )}
      />
    </View>
  );
}

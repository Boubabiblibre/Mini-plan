import { View, Text, TouchableOpacity, ScrollView, Alert } from 'react-native';
import styles from '../../styles/DashboardStyles';
 import { Ionicons } from '@expo/vector-icons';
 import useCookieHook from '../../hooks/use-cookie';
 import { useAccountStore } from '../../store/account';
 import { fetchAccount } from '../../services/account';
 import { listSubscriptions } from '../../services/subscriptions.js';
 import { useFocusEffect } from '@react-navigation/native';
import { api } from '../../services/api';
import { listMembersBySpace } from '../../services/members';
import ModalSelect from '../shared/ModalSelect';
import React, { useEffect, useState, useMemo } from 'react';

const toFloat = (v) => {
  if (v === null || v === undefined) return 0;
  const f = parseFloat(String(v).replace(',', '.'));
  return Number.isFinite(f) ? f : 0;
};

const getInterval = (s) => {
  const t = (s?.subscription_type || '').toLowerCase();
  if (t.includes('year')) return 'yearly';
  if (t.includes('week')) return 'weekly';
  if (t.includes('day'))  return 'daily';
  return 'monthly';
};

const getMonthlyAmount = (s) => {
  const amount = toFloat(s?.amount);
  switch (getInterval(s)) {
    case 'yearly': return amount / 12;
    case 'weekly': return amount * (52 / 12);
    case 'daily':  return amount * 30;
    default:       return amount;
  }
};

const addMonthsSafe = (d, m) => { 
  const x = new Date(d); x.setMonth(x.getMonth()+m); return x; 
};

const nextDueFromStart = (startISO, interval, endISO) => {
  if (!startISO) return null;
  const start = new Date(startISO);
  const now = new Date();
  if (endISO && new Date(endISO) < now) return null;
  let next = new Date(start);
  for (let i=0;i<200 && next<now;i++){
    if (interval==='yearly') next.setFullYear(next.getFullYear()+1);
    else if (interval==='weekly') next.setDate(next.getDate()+7);
    else if (interval==='daily') next.setDate(next.getDate()+1);
    else next = addMonthsSafe(next,1);
  }
  return next;
};

const monthLabelFR = (d) => d.toLocaleDateString('fr-FR',{month:'long',year:'numeric'});
const formatEUR = (n) => `${(n??0).toFixed(2).replace('.',',')} €`;
const toYMD = (d) => d.toISOString().slice(0,10);

const addDays = (d, n) => { const x = new Date(d); x.setDate(x.getDate()+n); return x; };
const startOfISOWeek = (d) => {
  const x = new Date(d); const wd = x.getDay(); // 0=Dim...6=Sam
  const diff = (wd === 0 ? -6 : 1) - wd; // aller au lundi
  x.setDate(x.getDate() + diff); x.setHours(0,0,0,0); return x;
};

const formatWeekRangeFR = (weekStart) => {
  const weekEnd = addDays(weekStart, 6);
  const sameMonth = weekStart.getMonth() === weekEnd.getMonth();
  const left = weekStart.toLocaleDateString('fr-FR', sameMonth ? { day:'numeric' } : { day:'numeric', month:'long' });
  const right = weekEnd.toLocaleDateString('fr-FR', { day:'numeric', month:'long', year:'numeric' });
  return `${left}–${right}`;
};


export default function DashboardScreen({ navigation }) {
  const { isLogged, loading, refreshAuth } = useCookieHook();
  const { account, setAccount } = useAccountStore();

  const [subs, setSubs] = useState([]);
  const [subsLoading, setSubsLoading] = useState(true);
  const [subsError, setSubsError] = useState(null);
  const [spaceChoices, setSpaceChoices] = useState([]); // {id,name,myMemberId}
  const [showSpacePicker, setShowSpacePicker] = useState(false);

  const [mode, setMode] = useState('week');
  const [weekStart, setWeekStart] = useState(startOfISOWeek(new Date()));
  const [cursor, setCursor] = useState(new Date());
  const [selectedYMD, setSelectedYMD] = useState(null);


  useEffect(() => { if (!loading && !isLogged) navigation?.navigate?.('Login'); }, [loading, isLogged, navigation]);

  useEffect(() => {
    const run = async () => {
      if (loading || !isLogged) return;
      try { const me = await fetchAccount(); if (me) setAccount(me); else await refreshAuth(); }
      catch(e){ console.warn('fetchAccount failed:', e?.message||e); }
    };
    run();
  }, [loading, isLogged, refreshAuth, setAccount]);

  useEffect(() => {
    const run = async () => {
      if (loading || !isLogged) return;
      setSubsLoading(true); setSubsError(null);
      try {
        const data = await listSubscriptions();
        setSubs(Array.isArray(data) ? data : Array.isArray(data?.items) ? data.items : []);
      } catch(e){ setSubsError(e?.message||'Erreur chargement abonnements'); }
      finally{ setSubsLoading(false); }
    };
    run();
  }, [loading, isLogged]);

  useFocusEffect(
    React.useCallback(() => {
      // relance la charge des abonnements quand l'écran redevient visible
      (async () => {
        if (!isLogged) return;
        setSubsLoading(true); setSubsError(null);
        try {
          const data = await listSubscriptions();
          setSubs(Array.isArray(data) ? data : Array.isArray(data?.items) ? data.items : []);
        } catch(e){ setSubsError(e?.message||'Erreur chargement abonnements'); }
        finally{ setSubsLoading(false); }
      })();
    }, [isLogged])
  );

  const now = new Date();
  const displayName = account?.firstname || account?.email || 'Utilisateur';

  const { activeSubs, totalMonthly, upcomingMap } = useMemo(() => {
    const active = subs.filter(s => (s?.status || 'active') === 'active').map(s => {
      const inter = getInterval(s);
      const due = nextDueFromStart(s.start_date, inter, s.end_date);
      return { ...s, _interval: inter, _monthly: getMonthlyAmount(s), _nextDue: due, _nextKey: due ? toYMD(due) : null };
    });
    const tm = active.reduce((sum, s) => sum + (s._monthly||0), 0);
    const map = new Map();
    for (const s of active) { if (!s._nextKey) continue; const arr = map.get(s._nextKey)||[]; arr.push(s); map.set(s._nextKey, arr); }
    return { activeSubs: active, totalMonthly: tm, upcomingMap: map };
  }, [subs]);

  const days = useMemo(() => {
  if (mode === 'week') {
    const start = weekStart;
    return Array.from({ length: 7 }, (_, i) => addDays(start, i));
  }
  // mode "mois"
  const first = new Date(cursor.getFullYear(), cursor.getMonth(), 1);
  const nextMonth = new Date(cursor.getFullYear(), cursor.getMonth()+1, 1);
  const out = [];
  for (let d=new Date(first); d<nextMonth; d.setDate(d.getDate()+1)) out.push(new Date(d));
  return out;
  }, [mode, weekStart, cursor]);

  const filteredList = useMemo(() => {
    if (selectedYMD) {
      return (upcomingMap.get(selectedYMD) || []).slice().sort((a,b)=>a.name.localeCompare(b.name));
    }
    // pas de jour sélectionné → agréger toute la semaine (ou le mois, si besoin)
    if (mode === 'week') {
      const keys = Array.from({ length: 7 }, (_, i) => toYMD(addDays(weekStart, i)));
      return keys.flatMap(k => upcomingMap.get(k) || []);
    }
    // mode "mois" sans sélection : on peut décider d'agréger tout le mois ou rien.
    // Ici on n’agrège pas (garde comportement actuel).
    return [];
  }, [upcomingMap, selectedYMD, mode, weekStart]);


  const goVoirPlusDepenses = () => navigation?.navigate?.('ActiveSubscription');
  const goVoirPlusCalendar = () => navigation?.navigate?.('ActiveSubscription', { month: cursor.toISOString().slice(0,7) });
  const goAdd = () => {
  console.log('NAV → AddSubscription');
  navigation.navigate('AddSubscription');   // ⬅️ on y va sans params
};

  // Essaie de retrouver mon Member dans un espace
  async function resolveMyMemberId(space, accountId) {
    if (space?.my_member_id) return space.my_member_id;
    if (space?.meMemberId) return space.meMemberId;

    const fromInline =
      space?.members?.find?.(m => m?.user?.id === accountId)?.id ||
      space?.members?.find?.(m => m?.is_me)?.id;
    if (fromInline) return fromInline;

    try {
      const list = await listMembersBySpace(space.id);
      const mine =
        list.find(m => m?.user?.id === accountId)?.id ||
        list.find(m => m?.is_me)?.id ||
        // dernier filet: compare par email si présent
        list.find(m => m?.user?.email && m.user.email === account?.email)?.id;
      return mine || null;
    } catch {
      return null;
    }
  }

  async function fetchMySpacesWithMemberId(accountId) {
    // adapte l’URL si nécessaire (ex: /api/spaces/mine)
    const { data } = await api.get('/space/all');
    const items = Array.isArray(data) ? data : (data?.items ?? []);
    const out = [];
    for (const sp of items) {
      const myMemberId = await resolveMyMemberId(sp, accountId);
      out.push({ id: sp.id, name: sp.name || sp.title || 'Espace', myMemberId });
    }
    return out;
  }


  return (
    <View style={{ flex: 1 }}>
      <ScrollView style={styles.container}>
        <View style={styles.tabsContainer}>
          <TouchableOpacity style={styles.activeTab}><Text style={styles.activeTabText}>Vous</Text></TouchableOpacity>
          <TouchableOpacity style={styles.inactiveTab} onPress={() => navigation?.navigate?.('SpacesScreen')}>
            <Text style={styles.inactiveTabText}>Espaces</Text>
          </TouchableOpacity>
        </View>

        <Text style={styles.greeting}>Bonjour {displayName}</Text>

        {/* CTA Ajouter */}
        <View style={styles.topActions}>
          <TouchableOpacity style={styles.primaryBtnLg} onPress={goAdd}>
            <Ionicons name="add" size={18} color="#000" />
            <Text style={styles.primaryBtnLgText}>AJOUTER UN ABONNEMENT</Text>
          </TouchableOpacity>
        </View>


        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Dépenses</Text>
            <TouchableOpacity onPress={goVoirPlusDepenses}><Text style={styles.seeMore}>Voir plus</Text></TouchableOpacity>
          </View>
          <View style={styles.card}>
            <Text style={styles.cardText}><Ionicons name="calendar" size={16} /> {monthLabelFR(now)}</Text>
            <Text style={styles.cardText}><Ionicons name="layers" size={16} /> {activeSubs.length} abonnements</Text>
            {subsLoading ? <Text style={styles.cardText}>Calcul...</Text> :
             subsError ? <Text style={styles.cardText}>Erreur : {subsError}</Text> :
             <Text style={styles.amount}>{formatEUR(totalMonthly)} <Text style={styles.perMonth}>/mois</Text></Text>}
          </View>
        </View>

        <View style={styles.sectionHeader}>
  <Text style={styles.sectionTitle}>Calendrier</Text>
  <TouchableOpacity onPress={() => {
    // si un jour est sélectionné → on envoie le filtre jour
    if (selectedYMD) {
      navigation?.navigate?.('ActiveSubscription', { day: selectedYMD });
    } else {
      // sinon filtre semaine (YYYY-Wxx pas nécessaire, on passe juste la date de début)
      navigation?.navigate?.('ActiveSubscription', { weekStart: toYMD(weekStart) });
    }
  }}>
    <Text style={styles.seeMore}>Voir plus</Text>
  </TouchableOpacity>
</View>

{/* Switch + navigation semainier */}
<View style={{ flexDirection:'row', alignItems:'center', gap:10, marginBottom:10 }}>
  <TouchableOpacity
    style={mode==='week'?styles.activeFilter:styles.inactiveFilter}
    onPress={()=>{ setMode('week'); setWeekStart(startOfISOWeek(new Date())); setSelectedYMD(null); }}
  >
    <Text style={mode==='week'?styles.activeFilterText:styles.inactiveFilterText}>Semaine</Text>
  </TouchableOpacity>

  <TouchableOpacity
    style={mode==='month'?styles.activeFilter:styles.inactiveFilter}
    onPress={()=>{ setMode('month'); setCursor(new Date()); setSelectedYMD(null); }}
  >
    <Text style={mode==='month'?styles.activeFilterText:styles.inactiveFilterText}>Mois</Text>
  </TouchableOpacity>

  {mode==='week' && (
    <>
      <TouchableOpacity style={styles.smallPill} onPress={()=>setWeekStart(addDays(weekStart, -7))}>
        <Ionicons name="chevron-back" size={16} />
      </TouchableOpacity>
      <Text style={{ color:'#aaa' }}>Semaine du {formatWeekRangeFR(weekStart)}</Text>
      <TouchableOpacity style={styles.smallPill} onPress={()=>setWeekStart(addDays(weekStart, 7))}>
        <Ionicons name="chevron-forward" size={16} />
      </TouchableOpacity>
      <TouchableOpacity style={styles.smallPill} onPress={()=>{
        setWeekStart(startOfISOWeek(new Date()));
        setSelectedYMD(null);
      }}>
        <Text>Aujourd’hui</Text>
      </TouchableOpacity>
    </>
  )}

  {mode==='month' && (
    <>
      <TouchableOpacity style={styles.smallPill} onPress={()=>setCursor(new Date(cursor.getFullYear(),cursor.getMonth()-1,1))}>
        <Ionicons name="chevron-back" size={16} />
      </TouchableOpacity>
      <Text style={{ color:'#aaa' }}>{monthLabelFR(cursor)}</Text>
      <TouchableOpacity style={styles.smallPill} onPress={()=>setCursor(new Date(cursor.getFullYear(),cursor.getMonth()+1,1))}>
        <Ionicons name="chevron-forward" size={16} />
      </TouchableOpacity>
    </>
  )}
</View>

{/* JOURS (semaine/mois) */}
<View
  style={[
    styles.calendarContainer,
    // optionnel : en mode "mois" on passe en grille
    mode === 'month' ? { flexWrap: 'wrap', justifyContent: 'space-between' } : null,
  ]}
>
  {days.map((d) => {
    const key = toYMD(d);
    const isSel = key === selectedYMD;
    const hasDue = upcomingMap.has(key);
    return (
      <TouchableOpacity
        key={key}
        style={isSel ? styles.selectedDate : styles.date}
        onPress={() => setSelectedYMD(isSel ? null : key)}
      >
        <Text style={isSel ? styles.selectedDateText : styles.dateText}>
          {d.getDate()}
        </Text>
        {hasDue ? <View style={styles.dueDot} /> : null}
      </TouchableOpacity>
    );
  })}
</View>




        {filteredList.length > 0 ? filteredList.map((s)=>(
          <View key={s.id} style={styles.subscriptionCard}>
            <View style={styles.subscriptionLeft}>
              <View style={styles.logoBox}><Text style={styles.logoText}>{(s.name||'Abo').slice(0,10)}</Text></View>
              <View>
                <Text style={styles.subscriptionTitle}>{s.name||'—'}</Text>
                <Text style={styles.subscriptionDesc}>Échéance le {new Date(s._nextDue).toLocaleDateString('fr-FR')}</Text>
              </View>
            </View>
            <Text style={styles.subscriptionPrice}>
              {formatEUR(getMonthlyAmount(s))} <Text style={styles.perMonth}>/mois</Text>
            </Text>
          </View>
        )) : (
          <View style={styles.emptyState}>
            <Ionicons name="card-outline" size={22} color="#666" />
            <Text style={styles.emptyText}>Aucun abonnement pour le moment.</Text>
            <TouchableOpacity style={styles.primaryBtn} onPress={goAdd}>
              <Text style={styles.primaryBtnText}>AJOUTER</Text>
            </TouchableOpacity>
          </View>
        )}
      </ScrollView>

      <TouchableOpacity style={styles.fab} onPress={goAdd}>
        <Ionicons name="add" size={28} color="#000" />
      </TouchableOpacity>
      <ModalSelect
        visible={showSpacePicker}
        title="Choisir un espace"
        options={spaceChoices.map(s => s.name)}
        value={null}
        onChange={(label) => {
          const chosen = spaceChoices.find(s => s.name === label);
          if (chosen) {
            setShowSpacePicker(false);
            navigation.navigate('AddSubscription', {
              spaceId: chosen.id,
              memberId: chosen.myMemberId,
            });
          }
        }}
        onClose={() => setShowSpacePicker(false)}
      />
    </View>
  );
}

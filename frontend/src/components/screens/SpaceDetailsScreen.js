// src/components/screens/SpaceDetailsScreen.js
import React, { useCallback, useEffect, useLayoutEffect, useState } from 'react';
import { useRoute, useNavigation, useFocusEffect } from '@react-navigation/native';
import { Ionicons } from '@expo/vector-icons';
import { View, Text, ActivityIndicator, Alert, TouchableOpacity, ScrollView, Modal, TextInput } from 'react-native';
import { createMember, listMembers as listMembersBySpace } from '../../services/members';
import { useAccountStore } from '../../store/account';
import { getSpaceById } from '../../services/spaces';
import { listInvitesBySpace, cancelInvite, resendInvite } from '../../services/invitations';

export default function SpaceDetailsScreen() {
  const route = useRoute();
  const navigation = useNavigation();

  const spaceId = route.params?.id;
  const { account } = useAccountStore();
  const myUserId = account?.id ?? account?.user?.id;
  const isAdmin = Array.isArray(account?.roles) && account.roles.includes('ROLE_ADMIN');

  const [space, setSpace] = useState(null);
  const [members, setMembers] = useState([]);
  const [invites, setInvites] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showAdd, setShowAdd] = useState(false); // placeholder pour futur modal d’ajout
  const [emailInput, setEmailInput] = useState('');
  const [relInput, setRelInput] = useState('friend');

  // Fait apparaitre une flèche retour dans le header vert (avec fallback)
  useLayoutEffect(() => {
    navigation.setOptions({
      headerLeft: () => (
        <TouchableOpacity
          onPress={() => (navigation.canGoBack() ? navigation.goBack() : navigation.navigate('SpacesScreen'))}
          style={{ paddingLeft: 15 }}
        >
          <Ionicons name="arrow-back" size={24} color="white" />
        </TouchableOpacity>
      ),
      title: "Détails de l'espace",
    });
  }, [navigation]);

  const load = useCallback(async () => {
    if (!spaceId) return;
    setLoading(true);
    try {
      const sp = await getSpaceById(spaceId);
      setSpace(sp);

      // owner ?
      const isOwner = sp?.created_by?.id && String(sp.created_by.id) === String(myUserId);
      const canManage = isAdmin || isOwner;

      // membres
      const mlist = await listMembersBySpace(spaceId).catch(() => []);
      setMembers(Array.isArray(mlist) ? mlist : []);

      // invitations (seulement admin/owner)
      if (canManage) {
        const ilist = await listInvitesBySpace(spaceId).catch(() => []);
        setInvites(Array.isArray(ilist) ? ilist : []);
      } else {
        setInvites([]);
      }

      // titre contextuel avec le nom
      if (sp?.name) navigation.setOptions({ title: sp.name });
    } catch (e) {
      Alert.alert('Erreur', "Espace introuvable.");
      setSpace(null);
      setMembers([]);
      setInvites([]);
    } finally {
      setLoading(false);
    }
  }, [spaceId, myUserId, isAdmin, navigation]);

  // charge au focus + au mount
  useFocusEffect(useCallback(() => { load(); }, [load]));
  useEffect(() => { load(); }, [load]);

  if (loading) {
    return (
      <View style={{ flex:1, alignItems:'center', justifyContent:'center', backgroundColor:'#000' }}>
        <ActivityIndicator />
      </View>
    );
  }

  if (!space) {
    return (
      <View style={{ flex:1, padding:16, backgroundColor:'#000' }}>
        <Text style={{ color:'#fff' }}>Espace introuvable.</Text>
        <TouchableOpacity onPress={() => (navigation.canGoBack() ? navigation.goBack() : navigation.navigate('SpacesScreen'))} style={{ marginTop:12 }}>
          <Text style={{ color:'#A6FF00', fontWeight:'700' }}>Retour</Text>
        </TouchableOpacity>
      </View>
    );
  }

  // recompute droits pour le rendu
  const isOwner = space?.created_by?.id && String(space.created_by.id) === String(myUserId);
  const canManage = isAdmin || isOwner;

  const onAddMember = async () => {
    const email = String(emailInput).trim().toLowerCase();
    if (!email) { Alert.alert('Email requis'); return; }
    try {
      // MemberController /member/create : s’il existe -> ajoute le membre, sinon -> crée une invitation
      const res = await createMember(spaceId, { email, relationship: relInput || 'friend' });

      if (res?.status === 'added' && res?.member) {
        setMembers(prev => [res.member, ...prev]);
        Alert.alert('OK', 'Membre ajouté.');
      } else if (res?.status === 'invited' && res?.invite) {
        setInvites(prev => [res.invite, ...prev]);
        Alert.alert('OK', 'Invitation envoyée.');
      } else {
        Alert.alert('Info', 'Opération effectuée.');
      }

      setShowAdd(false);
      setEmailInput('');
      setRelInput('friend');
    } catch (e) {
      Alert.alert('Erreur', e?.response?.data?.error || e.message);
    }
  };


  return (
    <ScrollView style={{ flex:1, backgroundColor:'#000' }} contentContainerStyle={{ padding:16, paddingBottom:24 }}>
      {/* Carte infos espace */}
      <View style={{ backgroundColor:'#111', borderRadius:14, padding:14, borderWidth:1, borderColor:'#1f1f1f', marginBottom:12 }}>
        <Text style={{ color:'#9ca3af', marginBottom:8 }}>Visibilité</Text>
        <Text style={{ color:'#fff', marginBottom:12 }}>{space.visibility || '—'}</Text>

        <Text style={{ color:'#9ca3af', marginBottom:8 }}>Description</Text>
        <Text style={{ color:'#fff' }}>{space.description || '—'}</Text>
      </View>

      {/* Membres */}
      <View style={{ backgroundColor:"#0f0f0f", borderRadius:12, borderWidth:1, borderColor:"#1f1f1f", padding:12 }}>
        <View style={{ flexDirection:"row", alignItems:"center", marginBottom:8 }}>
          <Text style={{ color:"#B7FF27", fontWeight:"700", flex:1 }}>Membres ({members.length})</Text>

          {canManage && (
            <TouchableOpacity
              onPress={() => {
                setShowAdd(true);
                Alert.alert('À venir', "Formulaire d’ajout/invitation à implémenter ici.");
              }}
              style={{ borderWidth:1, borderColor:"#A6FF00", borderRadius:10, paddingVertical:6, paddingHorizontal:10 }}
            >
              <Text style={{ color:"#A6FF00", fontWeight:"700" }}>Ajouter</Text>
            </TouchableOpacity>
          )}
        </View>

        {members.length === 0 ? (
          <Text style={{ color:"#999" }}>Aucun membre pour le moment.</Text>
        ) : (
          members.map(m => (
            <View key={m.id} style={{ paddingVertical:8, borderTopWidth:1, borderTopColor:"#1f1f1f" }}>
              <Text style={{ color:"#ede" }}>{m.name || "Membre"}</Text>
              <Text style={{ color:"#999", fontSize:12 }}>{m.relationship || "—"}</Text>
            </View>
          ))
        )}
      </View>

      {/* Invitations visibles seulement si canManage */}
      {canManage && (
        <View style={{ backgroundColor:"#0f0f0f", borderRadius:12, borderWidth:1, borderColor:"#1f1f1f", padding:12, marginTop:12 }}>
          <Text style={{ color:"#B7FF27", fontWeight:"700", marginBottom:8 }}>Invitations en attente</Text>

          {invites.length === 0 ? (
            <Text style={{ color:"#999" }}>Aucune invitation en attente.</Text>
          ) : (
            invites.map(inv => (
              <View
                key={inv.id}
                style={{ paddingVertical:8, borderTopWidth:1, borderTopColor:"#1f1f1f", flexDirection:'row', alignItems:'center', gap:8 }}
              >
                <View style={{ flex:1 }}>
                  <Text style={{ color:'#ede' }}>{inv.email}</Text>
                  <Text style={{ color:'#999', fontSize:12 }}>
                    {inv.relationship || '—'} · expire le {inv.expires_at || '—'}
                  </Text>
                </View>

                <TouchableOpacity
                  onPress={async () => {
                    try { await resendInvite(inv.id); Alert.alert('OK','Invitation renvoyée'); load(); }
                    catch (e) { Alert.alert('Erreur', e?.response?.data?.error || e.message); }
                  }}
                >
                  <Ionicons name="reload" size={18} color="#A6FF00" />
                </TouchableOpacity>

                <TouchableOpacity
                  onPress={() => {
                    Alert.alert('Confirmer','Annuler cette invitation ?',[
                      { text:'Non', style:'cancel' },
                      { text:'Oui', style:'destructive', onPress: async () => {
                        try { await cancelInvite(inv.id); load(); }
                        catch (e) { Alert.alert('Erreur', e?.response?.data?.error || e.message); }
                      } }
                    ]);
                  }}
                >
                  <Ionicons name="trash" size={18} color="#ff6b6b" />
                </TouchableOpacity>
              </View>
            ))
          )}
        </View>
      )}

      {/* Actions */}
      <View style={{ marginTop:16, gap:10 }}>
        <TouchableOpacity
          style={{ backgroundColor:'#A6FF00', paddingVertical:14, borderRadius:12, alignItems:'center', flexDirection:'row', justifyContent:'center', gap:8 }}
          // 👉 envoie TOUT ce qu’il faut pour filtrer dans la liste
          onPress={() => navigation.navigate('SubscriptionList', {
            spaceId,
            spaceName: space?.name,
            createdById: space?.created_by?.id,
          })}
        >
          <Ionicons name="albums" size={18} color="#000" />
          <Text style={{ color:'#000', fontWeight:'700' }}>Voir les abonnements de cet espace</Text>
        </TouchableOpacity>
      </View>
      {/* ===== Modal Ajouter membre / Inviter ===== */}
      <Modal visible={showAdd} transparent animationType="fade" onRequestClose={() => setShowAdd(false)}>
        <View style={{ flex:1, backgroundColor:'rgba(0,0,0,0.6)', justifyContent:'center', padding:16 }}>
          <View style={{ backgroundColor:'#0f0f0f', borderRadius:14, padding:14, borderWidth:1, borderColor:'#1f1f1f' }}>
            <Text style={{ color:'#B7FF27', fontWeight:'700', marginBottom:10 }}>Ajouter un membre</Text>

            <Text style={{ color:'#9ca3af', marginBottom:6 }}>Email</Text>
            <TextInput
              placeholder="prenom@exemple.com"
              placeholderTextColor="#777"
              autoCapitalize="none"
              keyboardType="email-address"
              style={{ color:'#eee', borderWidth:1, borderColor:'#262626', borderRadius:10, paddingHorizontal:10, paddingVertical:8, marginBottom:10 }}
              value={emailInput}
              onChangeText={setEmailInput}
            />

            <Text style={{ color:'#9ca3af', marginBottom:6 }}>Lien (relationship)</Text>
            <TextInput
              placeholder="ami, famille, collègue…"
              placeholderTextColor="#777"
              style={{ color:'#eee', borderWidth:1, borderColor:'#262626', borderRadius:10, paddingHorizontal:10, paddingVertical:8, marginBottom:12 }}
              value={relInput}
              onChangeText={setRelInput}
            />

            <View style={{ flexDirection:'row', gap:8 }}>
              <TouchableOpacity
                onPress={() => setShowAdd(false)}
                style={{ flex:1, alignItems:'center', paddingVertical:12, borderRadius:10, borderWidth:1, borderColor:'#444' }}
              >
                <Text style={{ color:'#bbb' }}>Annuler</Text>
              </TouchableOpacity>

              <TouchableOpacity
                onPress={onAddMember}
                style={{ flex:1, alignItems:'center', paddingVertical:12, borderRadius:10, backgroundColor:'#A6FF00' }}
              >
                <Text style={{ color:'#000', fontWeight:'700' }}>Envoyer</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </ScrollView>
  );
}

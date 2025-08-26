// frontend/src/services/subscriptions.service.js
import { api } from "./api";

/** Liste (optionnellement avec params au besoin) */
export async function listSubscriptions({ scope = 'mine' } = {}) {
  const url = scope === 'all' ? '/subscription/all' : '/subscription/mine';
  const { data } = await api.get(url);
  return Array.isArray(data) ? data : [];
}

/** Lecture d’une souscription */
export async function getSubscription(id) {
  const { data } = await api.get(`/subscription/${id}`);
  return data;
}

/** Création */

export async function createSubscription(payload) {
  const res = await api.post('subscription/create', payload);
  return res.data;
}

export async function updateSubscription(id, payload) {
  return api.put(`/api/subscription/${id}`, payload);
}

/** Suppression */
export async function deleteSubscription(id) {
  const { data } = await api.delete(`/subscription/delete/${id}`);
  return data;
}

/** === Helpers métier === */

/** Résilier / rendre obsolète : status=inactive, stop auto_renewal, date de fin = aujourd’hui (ou fournie) */
export async function archiveSubscription(id, endDate) {
  const today = endDate || new Date().toISOString().slice(0, 10);
  return updateSubscription(id, {
    status: "inactive",
    auto_renewal: false,
    end_date: today,
  });
}

/** Réactiver : status=active, relance auto_renewal, retire la date de fin */
export async function restoreSubscription(id) {
  return updateSubscription(id, {
    status: "active",
    auto_renewal: true,
    end_date: null,
  });
}

/** Petit utilitaire pratique côté UI pour détecter les doublons (HTTP 409) */
export function isDuplicateError(err) {
  return err?.response?.status === 409;
}

export async function listSubscriptionsBySpace(spaceId, { all = false } = {}) {
  const params = new URLSearchParams({ space_id: String(spaceId) });
  if (all) params.append('all', '1');
  const { data } = await api.get(`/subscription/by-space?${params.toString()}`);
  return data;
}

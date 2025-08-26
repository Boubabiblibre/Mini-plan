// frontend/src/services/service.js
import { api } from "./api";

/** Liste des services (Netflix, Spotify, …) */
export async function listServices() {
  const { data } = await api.get("/service/all");
  return Array.isArray(data) ? data : data?.items || [];
}

/** (optionnel) Détail d’un service */
export async function getService(id) {
  const { data } = await api.get(`/service/${id}`);
  return data;
}

// frontend/src/services/service.js
export function serviceLogo(s) {
  // priorité: logo fourni > fallback via website > placeholder local
  if (s?.logo) return s.logo;
  if (s?.website) {
    try {
      const host = new URL(s.website).host;
      return `https://logo.clearbit.com/${host}`;
    } catch {}
  }
  return null;
}

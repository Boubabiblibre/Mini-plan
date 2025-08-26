// frontend/src/services/account.js
import { api } from "./api";

export async function fetchAccount() {
  try {
    const { data } = await api.get("/user/me"); // baseURL = .../api
    return data;
  } catch (err) {
    // si le token est invalide/expiré, l'intercepteur a mis _auth401 et vidé le storage
    if (err?._auth401 || err?.response?.status === 401) return null;
    throw err;
  }
}

// frontend/src/services/api.js
import axios from "axios";
import AsyncStorage from "@react-native-async-storage/async-storage";

// ⚠️ Choisis UN host et garde-le partout (et autorise-le côté CORS).
// Pour ton front web sur http://localhost:8081, prends localhost ici :
const API_URL = "http://localhost:8000/api";
// Si tu es sur Android Emulator: "http://10.0.2.2:8000/api"
// Si tu es sur un téléphone physique: "http://<IP-LAN-DE-TON-PC>:8000/api"

export const api = axios.create({
  baseURL: API_URL,
  headers: { "Content-Type": "application/json" },
});


// Intercepteur request
api.interceptors.request.use(
  async (config) => {
    const token = await AsyncStorage.getItem("token");
    console.log("🔑 Token intercepteur:", token);

    if (token) {
      // ⚠️ Toujours initialiser les headers si absents
      if (!config.headers) {
        config.headers = {};
      }
      config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
  },
  (error) => Promise.reject(error)
);

// ➜ Uniformise la gestion des 401 (token expiré/invalide)
api.interceptors.response.use(
  (res) => res,
  async (err) => {
    if (err?.response?.status === 401) {
      await AsyncStorage.removeItem("token"); // on nettoie
      err._auth401 = true;                   // flag pour que les écrans sachent quoi faire
    }
    return Promise.reject(err);
  }
);

/* ===== SPACES =====
   IMPORTANT: Comme baseURL finit par /api,
   NE PAS préfixer tes chemins par /api ici. */
export const getAllSpaces = async () => {
  const { data } = await api.get("/space/all");
  return data;
};

export const getSpaceDetails = async (id) => {
  const { data } = await api.get(`/space/${id}`);
  return data;
};

export const createSpace = async (payload) => {
  const { data } = await api.post("/space/create", payload);
  return data;
};

// (autres endpoints ici)

import api from "./api";

export async function searchUsers(q) {
  try {
    const { data } = await api.get("/user/all", { params: { q } }); // ou /user/search si tu l'as
    return Array.isArray(data) ? data : [];
  } catch {
    return [];
  }
}

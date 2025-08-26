// src/services/spaces.js
import { api } from "./api";

export async function createSpace(payload) {
  const { data } = await api.post("/space/create", payload);
  return data.space || data;
}

export async function listSpaces({ scope='mine' } = {}) {
  const { data } = await api.get('/space/list', { params: { scope }});
  return Array.isArray(data) ? data : [];
}

export async function getSpaceById(id) {
  const { data } = await api.get(`/space/${id}`);
  return data.space || data;
}
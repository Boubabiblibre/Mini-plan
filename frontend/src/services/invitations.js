// src/services/invitations.js
import { api } from "./api";

export async function listInvitations(spaceId) {
  const { data } = await api.get("/invite/all", { params: { space_id: spaceId } });
  return Array.isArray(data) ? data : [];
}

export async function listMyInvitations(spaceId) {
  // (utilisateur standard) — pour /api/invite/mine
  const { data } = await api.get("/invite/mine", {
    params: spaceId ? { space_id: spaceId } : {},
  });
  return Array.isArray(data) ? data : [];
}

export async function claimInvite(token) {
  const { data } = await api.post("/invite/claim", { token });
  return data;
}

export async function acceptInvitation(id) {
  console.log("Accepting invitation with id:", id);
  const { data } = await api.post(`/invite/accept/${id}`);
  return data;
}

export async function declineInvitation(id) {
  const { data } = await api.post(`/invite/decline/${id}`);
  return data;
}

export async function listInvitesBySpace(spaceId) {
  const { data } = await api.get(`/member/invitations?space_id=${spaceId}`);
  return data;
}

export async function cancelInvite(inviteId) {
  const { data } = await api.post(`/member/invitation/cancel/${inviteId}`);
  return data;
}

export async function resendInvite(inviteId) {
  const { data } = await api.post(`/member/invitation/resend/${inviteId}`);
  return data;
}

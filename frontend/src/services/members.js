import { api } from "./api";

export async function listMembers(spaceId) {
  const { data } = await api.get("/member/all", { params: { space_id: spaceId } });
  return Array.isArray(data) ? data : [];
}

export async function createMember(spaceId, body) {
  const payload = { ...body, space_id: spaceId }; // email | user_id | relationship | date_of_birth
  const { data } = await api.post("/member/create", payload);
  return data?.member || data; // {status:'added', member:{...}} ou {status:'invited', invite:{...}}
}

export async function deleteMember(memberId) {
  return api.delete(`/member/delete/${memberId}`);
}

export async function listMembersBySpace(spaceId) {
  const { data } = await api.get('/member/all', { params: { space_id: spaceId }});
  return Array.isArray(data) ? data : [];
}
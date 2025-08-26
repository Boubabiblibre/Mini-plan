// frontend/src/store/account.js
import { create } from "zustand";

export const useAccountStore = create((set) => ({
  account: null,
  setAccount: (data) => set({ account: data }),
  restoreAccount: () => set({ account: null }),
}));

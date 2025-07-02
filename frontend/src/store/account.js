import { create } from "zustand";

export const useAccountStore = create((set) => ({
  account: null,
  setAccount: (profile) => set({ account: profile }),
  resetAccount: () => set({ account: null }),
}));

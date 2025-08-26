// frontend/src/hooks/use-cookie.js
import { useCallback, useEffect, useState } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import decodeJwt from "../util/decodeJwt";
import { useAccountStore } from "../store/account";

const KEY = "token";

function isTokenValid(token) {
  if (!token) return false;
  try {
    const claims = decodeJwt(token);
    if (!claims) return false;
    if (typeof claims.exp === "number") {
      const now = Math.floor(Date.now() / 1000);
      return claims.exp > now;
    }
    return true;
  } catch {
    return false;
  }
}

export default function useCookieHook() {
  const [token, setToken] = useState(null);
  const [loading, setLoading] = useState(true);   // ✅ nouvel état
  const { setAccount, restoreAccount } = useAccountStore();

  const refreshAuth = useCallback(async () => {
    setLoading(true);
    const stored = await AsyncStorage.getItem(KEY);
    setToken(stored);
    if (isTokenValid(stored)) {
      const claims = decodeJwt(stored);
      setAccount(claims || null);
    } else {
      await AsyncStorage.removeItem(KEY);
      setToken(null);
      restoreAccount();
    }
    setLoading(false);
  }, [setAccount, restoreAccount]);

  useEffect(() => {
    refreshAuth();
  }, [refreshAuth]);

  const setCookie = useCallback(
    async (_name, value, _maxAge) => {
      await AsyncStorage.setItem(KEY, value);
      await refreshAuth();
    },
    [refreshAuth]
  );

  const logout = useCallback(async () => {
    await AsyncStorage.removeItem(KEY);
    setToken(null);
    restoreAccount();
  }, [restoreAccount]);

  return {
    isLogged: isTokenValid(token),
    loading,    // ✅ exposé
    token,
    setCookie,
    logout,
    refreshAuth,
  };
}

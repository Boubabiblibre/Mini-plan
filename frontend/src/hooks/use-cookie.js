import { useCallback, useEffect, useState } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import decodeJwt from "../util/decodeJwt";
import { useAccountStore } from "../store/account";

const KEY = "token";

export default function useCookieHook() {
  const [token, setToken]   = useState(null);
  const { setAccount, resetAccount } = useAccountStore();

  /* Chargement initial -------------------------------------------------- */
  useEffect(() => {
    (async () => {
      const stored = await AsyncStorage.getItem(KEY);
      setToken(stored);
      const claims = decodeJwt(stored);
      if (claims) setAccount(claims);
    })();
  }, []);

  /* Helpers ------------------------------------------------------------- */
  const setCookie = useCallback(async (_name, value, _maxAge) => {
    await AsyncStorage.setItem(KEY, value);
    setToken(value);
    const claims = decodeJwt(value);
    if (claims) setAccount(claims);
  }, []);

  const logout = useCallback(async () => {
    await AsyncStorage.removeItem(KEY);
    setToken(null);
    resetAccount();
  }, []);

  return {
    isLogged: !!token,
    token,
    setCookie,
    logout,
  };
}

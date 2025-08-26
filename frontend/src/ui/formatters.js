// src/ui/formatters.js
export const formatMoney = (v = 0, cur = 'EUR') =>
  new Intl.NumberFormat('fr-FR', { style: 'currency', currency: cur }).format(Number(v || 0));

export const cycleLabel = (t = 'monthly') =>
  ({ monthly: 'mois', yearly: 'an', weekly: 'semaine', daily: 'jour' }[t] || 'mois');

export const formatDateFR = (iso) => {
  if (!iso) return '';
  const d = new Date(iso);
  return d.toLocaleDateString('fr-FR');
};

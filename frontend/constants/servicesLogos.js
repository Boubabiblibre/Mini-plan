// Mets des fichiers PNG (32–64px) dans /assets/services/
// ⚠️ Seuls les require existants doivent rester (sinon Metro râle).
export const SERVICE_LOGOS = {
  'Amazon Prime Video': require('../../assets/services/prime-video.png'),
  'Disney+':             require('../../assets/services/disney-plus.png'),
  'Canal+':              require('../../assets/services/canal-plus.png'),
  'OCS':                 require('../../assets/services/ocs.png'),
  'Spotify':             require('../../assets/services/spotify.png'),
  'Apple Music':         require('../../assets/services/apple-music.png'),
  'YouTube Premium':     require('../../assets/services/youtube-premium.png'),
  'Deezer':              require('../../assets/services/deezer.png'),
  'Orange':              require('../../assets/services/orange.png'),
  'SFR':                 require('../../assets/services/sfr.png'),
  'Bouygues Telecom':    require('../../assets/services/bouygues.png'),
  'Free':                require('../../assets/services/free.png'),
  'Microsoft 365':       require('../../assets/services/microsoft-365.png'),
  'Google One':          require('../../assets/services/google-one.png'),
  'iCloud+':             require('../../assets/services/icloud-plus.png'),
  'Dropbox':             require('../../assets/services/dropbox.png'),
  'Adobe Creative Cloud':require('../../assets/services/adobe-cc.png'),
  'Xbox Game Pass':      require('../../assets/services/xbox-game-pass.png'),
  'PlayStation Plus':    require('../../assets/services/ps-plus.png'),
  'Nintendo Switch Online': require('../../assets/services/nintendo-switch-online.png'),
};

export function getServiceLogoSource(serviceOrName) {
  const name = (serviceOrName?.name || serviceOrName?.title || serviceOrName || '').trim();
  if (!name) return null;
  // si l’API finit par exposer un logo_url
  if (serviceOrName?.logo_url) return { uri: serviceOrName.logo_url };
  return SERVICE_LOGOS[name] ?? null;
}

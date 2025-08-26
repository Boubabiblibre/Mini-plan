// src/navigation/linking.js
const linking = {
  // pour web, on utilise l'origine courante (localhost:8081, etc.)
  prefixes: [typeof window !== 'undefined' ? window.location.origin : 'mini-plan://'],

  config: {
    screens: {
      // Auth / profil
      Home: '',
      Login: 'login',
      Register: 'register',
      Profile: 'profile',

      // App
      Dashboard: 'dashboard',

      // Subscriptions
      SubscriptionList: 'subscriptions',
      SubscriptionDetails: 'subscriptions/:id',
      AddSubscription: 'subscriptions/new',
      ActiveSubscription: 'subscriptions/active',
      CustomSubscription: 'subscriptions/:id/edit',

      // Spaces
      SpacesScreen: 'spaces',
      SpaceDetails: 'spaces/:id',
      SpaceCreate: 'spaces/new',
    },
  },
};

export default linking;

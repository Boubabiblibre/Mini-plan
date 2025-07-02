import { StyleSheet } from 'react-native';

const LoginStyles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    paddingHorizontal: 20,
    backgroundColor: '#000000',
  },
  input: {
    marginBottom: 20,
    borderRadius: 25,
    backgroundColor: 'white',
    height: 50,
    paddingHorizontal: 15,
    borderWidth: 1,
    borderColor: '#ccc',
    fontFamily: 'OutfitRegular', // Appliquer la police
  },
  label: {
    fontSize: 14,
    fontWeight: 'bold',
    marginBottom: 10,
    color: '#FFFFFF',
    fontFamily: 'OutfitBold', // Police plus marquée pour le label
  },
  button: {
    marginTop: 20,
    borderRadius: 25,
    height: 50,
    backgroundColor: '#A6FF00',
    justifyContent: 'center',
  },
  buttonText: {
    color: 'black',
    fontWeight: 'bold',
    fontFamily: 'OutfitBold',
    fontSize: 16,
  },
  link: {
    color: 'white',
    alignSelf: 'center',
    marginTop: '5%',
    textDecorationLine: 'underline',
    fontFamily: 'OutfitRegular',
  },
  loadingOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
});

export default LoginStyles;

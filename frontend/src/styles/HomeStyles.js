import { StyleSheet } from 'react-native';

const HomeStyles = StyleSheet.create({
    container: {
      flex: 1,
      backgroundColor: '#000',
      justifyContent: 'center',
      alignItems: 'center',
      paddingHorizontal: 20,
    },
    welcomeText: {
      color: 'white',     // texte en blanc
      fontSize: 20,       // un peu plus grand
      fontWeight: 'bold',
      marginBottom: 20,
      textAlign: 'center',
    },
    logo: {
      width: 100,
      height: 100,
      marginBottom: 30,
    },
    introContainer: {
      backgroundColor: '#7ED321',
      padding: 20,
      borderRadius: 20,
      alignItems: 'center',
      marginBottom: 40,
    },
    introTitle: {
      color: 'black',
      fontSize: 18,
      fontWeight: 'bold',
      textAlign: 'center',
    },
    introText: {
      color: 'black',
      textAlign: 'center',
      marginTop: 10,
    },
    button: {
      backgroundColor: '#8A2BE2',
      paddingVertical: 15,
      paddingHorizontal: 50,
      borderRadius: 30,
      marginVertical: 10,
      width: '80%',
      alignItems: 'center',
    },
    secondaryButton: {
      backgroundColor: '#7D3C98',
    },
    buttonText: {
      color: 'white',
      fontSize: 16,
      fontWeight: 'bold',
    },
  });
  
  export default HomeStyles;
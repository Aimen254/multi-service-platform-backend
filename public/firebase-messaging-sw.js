importScripts('https://www.gstatic.com/firebasejs/8.2.7/firebase-app.js')
importScripts('https://www.gstatic.com/firebasejs/8.2.7/firebase-messaging.js')

const firebaseConfig = {
  apiKey: "AIzaSyCONw2sJqLPSCSCZOSUW8vtuFir49LDZ-w",
  authDomain: "localverse-9d3d4.firebaseapp.com",
  databaseURL: "https://localverse-9d3d4-default-rtdb.firebaseio.com",
  projectId: "localverse-9d3d4",
  storageBucket: "localverse-9d3d4.appspot.com",
  messagingSenderId: "481054357290",
  appId: "1:481054357290:web:fa7bf34e9d43abf1f7d053",
  measurementId: "G-HPQ3DXR7FS"
};
const initializeApp = firebase.initializeApp(firebaseConfig)
const messaging = firebase.messaging()
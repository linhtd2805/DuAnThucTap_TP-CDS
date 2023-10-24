// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
      apiKey: "AIzaSyDxjjBJTK4afCNYAiUTAtOhGPr7IXIYRnA",
      authDomain: "ordereatslumen.firebaseapp.com",
      databaseURL: "https://ordereatslumen-default-rtdb.firebaseio.com",
      projectId: "ordereatslumen",
      storageBucket: "ordereatslumen.appspot.com",
      messagingSenderId: "823685759816",
      appId: "1:823685759816:web:1ecf2ac9d8f37d17d3697d",
      measurementId: "G-XWZ1CMRNXZ"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log("Message received.", payload);
    const title = "Hello world is awesome";
    const options = {
        body: "Your notificaiton message .",
        icon: "/firebase-logo.png",
    };
    return self.registration.showNotification(
        title,
        options,
    );
});
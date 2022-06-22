import { initializeApp } from "firebase/app";
import { getMessaging } from "firebase/messaging";
import { onBackgroundMessage } from "firebase/messaging/sw";

console.log('test, awdwad');
//
const app = initializeApp({
            apiKey: "AIzaSyCeqIKbxL7b-6a2OkLo4UZTZ8ya9w8qhGE",
            authDomain: "blockester-dev.firebaseapp.com",
            projectId: "blockester-dev",
            storageBucket: "blockester-dev.appspot.com",
            messagingSenderId: "61581966389",
            appId: "1:61581966389:web:b77034b133fbec6de2457c",
            measurementId: "G-RWZM28069W"
        });

onBackgroundMessage(getMessaging, (payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = 'Background Message Title';
    const notificationOptions = {
        body: 'Background Message body.',
        icon: '/firebase-logo.png'
    };

    self.registration.showNotification(notificationTitle,
        notificationOptions);
});

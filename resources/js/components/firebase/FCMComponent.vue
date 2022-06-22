<template>
    <h2>Firebase Cloud Message</h2>
    Current Token: <br><br> {{ currentToken }}
</template>

<script>
import { initializeApp } from "firebase/app";
import { getMessaging, onMessage, getToken } from "firebase/messaging";
import { onBackgroundMessage } from "firebase/messaging/sw";

export default {
    name: "fcmComponent.vue",
    data() {
        return {
            firebaseConfig: {
                apiKey: "AIzaSyCeqIKbxL7b-6a2OkLo4UZTZ8ya9w8qhGE",
                authDomain: "blockester-dev.firebaseapp.com",
                projectId: "blockester-dev",
                storageBucket: "blockester-dev.appspot.com",
                messagingSenderId: "61581966389",
                appId: "1:61581966389:web:b77034b133fbec6de2457c",
                measurementId: "G-RWZM28069W"
            },
            key: 'BLNPDISJGVAZUpbr6f1s-Vfm1VtpkdbYCQwXvCUsHRZl_a2i-RiTXqGi5XzPNJ2CqHYNgmD4gheJV_-yHnJC1nU',
            currentToken: null
        }
    },
    methods: {
        run() {
            const app = initializeApp(this.firebaseConfig);
            const messaging = getMessaging(app);

            getToken(messaging, {
                vapidKey: this.key,
            }).then(currentToken => {
                window.console.log('currentToken: ', this.currentToken = currentToken);
                window.axios.post('/v1/users/fcm_token', { token: currentToken });
            }).catch(err => {
                window.console.log('Err: ', err);
            });

            onMessage(messaging, (payload) => {
                console.log('Message received. ', payload);
                alert(payload);
            });

            // onBackgroundMessage(messaging, (payload) => {
            //     console.log('[firebase-messaging-sw.js] Received background message ', payload);
            //     // Customize notification here
            //     const notificationTitle = 'Background Message Title';
            //     const notificationOptions = {
            //         body: 'Background Message body.',
            //         icon: '/firebase-logo.png'
            //     };
            //
            //     self.registration.showNotification(notificationTitle, notificationOptions);
            // });
        }
    },
    mounted() {
        this.run();
    }
}
</script>

<style scoped>

</style>

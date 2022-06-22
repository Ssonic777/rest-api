<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.js" integrity="sha512-RT3IJsuoHZ2waemM8ccCUlPNdUuOn8dJCH46N3H2uZoY7swMn1Yn7s56SsE2UBMpjpndeZ91hm87TP1oU6ANjQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <title>FCM Web</title>
</head>
<body>
    <div id="app" class="center">
        <App></App>
        <h1>FCM Web Test</h1>
        <a href="#">Send Notification</a>
    </div>
</body>
<script src="{{  asset('js')  }}/app.js"></script>
{{--<script type="module">--}}
{{--    // Import the functions you need from the SDKs you need--}}
{{--    import { initializeApp } from "../node_modules/firebase/app";--}}
{{--    import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.4.1/firebase-analytics.js";--}}
{{--    // import { getMessaging, onMessage, getToken } from "firebase/messaging/sw";--}}
{{--    import { getMessaging } from "../node_modules/firebase/messaging/sw";--}}
{{--    // import { onBackgroundMessage } from "https://www.gstatic.com/firebasejs/9.4.1/firebase-messaging-sw.js";--}}
{{--    // TODO: Add SDKs for Firebase products that you want to use--}}
{{--    // https://firebase.google.com/docs/web/setup#available-libraries--}}

{{--    // Your web app's Firebase configuration--}}
{{--    // For Firebase JS SDK v7.20.0 and later, measurementId is optional--}}
{{--    const firebaseConfig = {--}}
{{--        apiKey: "AIzaSyCeqIKbxL7b-6a2OkLo4UZTZ8ya9w8qhGE",--}}
{{--        authDomain: "blockester-dev.firebaseapp.com",--}}
{{--        projectId: "blockester-dev",--}}
{{--        storageBucket: "blockester-dev.appspot.com",--}}
{{--        messagingSenderId: "61581966389",--}}
{{--        appId: "1:61581966389:web:b77034b133fbec6de2457c",--}}
{{--        measurementId: "G-RWZM28069W"--}}
{{--    };--}}

{{--    let key = 'BLNPDISJGVAZUpbr6f1s-Vfm1VtpkdbYCQwXvCUsHRZl_a2i-RiTXqGi5XzPNJ2CqHYNgmD4gheJV_-yHnJC1nU';--}}

{{--    // Initialize Firebase--}}
{{--    const app = initializeApp(firebaseConfig);--}}
{{--    // const analytics = getAnalytics(app);--}}
{{--    const messaging = getMessaging(app);--}}

{{--    getToken(messaging, {--}}
{{--        vapidKey: key,--}}
{{--    }).then(currentToken => {--}}
{{--        window.console.log('currentToken: ', currentToken);--}}
{{--        axios.post('/v1/users/fcm_token', { token: currentToken });--}}
{{--    }).catch(err => {--}}
{{--        window.console.log('Err: ', err);--}}
{{--    });--}}

{{--    // const messaging = getMessaging(firebaseConfig);--}}

{{--    onMessage(messaging, (payload) => {--}}
{{--        console.log('Message received. ', payload);--}}
{{--        // alert(payload);--}}
{{--        // ...--}}
{{--    });--}}

{{--    // function getModularInstance(service) {--}}
{{--    //     if (service && service._delegate) {--}}
{{--    //         return service._delegate;--}}
{{--    //     }--}}
{{--    //     else {--}}
{{--    //         return service;--}}
{{--    //     }--}}
{{--    // }--}}

{{--    // function onBackgroundMessage(messaging, nextOrObserver) {--}}
{{--    //     messaging = getModularInstance(messaging);--}}
{{--    //     return onBackgroundMessage(messaging, nextOrObserver);--}}
{{--    // }--}}

{{--    onBackgroundMessage(messaging, (payload) => {--}}
{{--        console.log('[firebase-messaging-sw.js] Received background message ', payload);--}}
{{--        // Customize notification here--}}
{{--        const notificationTitle = 'Background Message Title';--}}
{{--        const notificationOptions = {--}}
{{--            body: 'Background Message body.',--}}
{{--            icon: '/firebase-logo.png'--}}
{{--        };--}}

{{--        self.registration.showNotification(notificationTitle, notificationOptions);--}}
{{--    });--}}

{{--    console.log('onBackgroundMessage end');--}}

{{--</script>--}}
</html>

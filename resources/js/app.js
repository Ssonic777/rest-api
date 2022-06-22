require('./bootstrap');

import { createApp, h } from 'vue';
import App from './components/App';
import FCMComponent from './components/firebase/FCMComponent';

const app = createApp({
    // render(h) {
    //     return h(App);
    // }
});

// #region Register components
app.component('App', App);
app.component('FCMComponent', FCMComponent);
// #endregion

app.mount('#app');

<template>
    <div>
        <h1>Start Vue</h1>
        <fcm-component></fcm-component>
    </div>
</template>

<script>
import FCMComponent from './firebase/FCMComponent';

export default {
    name: 'App',
    components: {
        fcmComponent: FCMComponent
    }
}
</script>

<style scoped>

</style>

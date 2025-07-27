<template>
    <Navigation/>
    <main class="container mx-auto max-w-7xl">
        <router-view />
    </main>
</template>

<script>
import Navigation from "../pages/Navigation.vue";

export default {
    components: {Navigation}
}
</script>

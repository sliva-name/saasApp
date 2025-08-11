<template>
    <div class="min-h-screen bg-gradient-to-br from-secondary-50 to-primary-50">
        <Navigation />
        <main class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="animate-fade-in">
                <router-view />
            </div>
        </main>
    </div>
</template>

<script>
import Navigation from "../pages/Navigation.vue";

export default {
    components: { Navigation }
}
</script>

<style scoped>
/* Дополнительные стили для плавных переходов */
.router-view-enter-active,
.router-view-leave-active {
    transition: all 0.3s ease;
}

.router-view-enter-from {
    opacity: 0;
    transform: translateY(20px);
}

.router-view-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>

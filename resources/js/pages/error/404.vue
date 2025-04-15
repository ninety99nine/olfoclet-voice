<template>
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden">

        <!-- Blurred background image -->
        <div
            class="absolute inset-0 bg-cover bg-center filter blur-xs scale-110 z-0"
            :style="{ backgroundImage: 'url(/images/smiling-customer-service-agent.jpg)' }">
        </div>

        <!-- Overlay to dim background -->
        <div class="absolute inset-0 bg-black/50 z-0"></div>

        <!-- Foreground content -->
        <div class="relative z-50 text-center p-6">

            <h1 class="text-8xl font-bold text-white mb-2">404</h1>

            <h2 class="text-4xl font-bold text-white mb-4">Page not found</h2>

            <p class="text-white mb-6">
                Sorry, the page you're looking for doesn't exist or has been moved.
            </p>

            <div class="flex justify-center">

                <Button type="primary" size="lg" :action="goHome">
                    <span>Go Home</span>
                </Button>

            </div>

        </div>

    </div>

</template>

<script>

    import Button from '@Partials/Button.vue';

    export default {
        components: {
            Button
        },
        methods: {
            goHome() {
                this.$router.push({ name: 'login' });
            }
        }
    }

</script>

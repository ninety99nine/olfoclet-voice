<template>
    <header class="sticky top-2 z-50 mx-2 -mb-20 rounded-xl transition-all duration-1000 border" :class="{ 'bg-white/95 shadow-md border-gray-200': scrolled, 'bg-transparent shadow-none border-transparent': !scrolled }">
        <nav class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <router-link :to="{ name: 'landing' }" class="text-2xl font-bold text-indigo-600">
                        <img :src="`/images/${scrolled ? 'logo-swirl-text.png' : 'logo-swirl-text-white.png'}`" class="h-8" />
                    </router-link>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <router-link :to="{ name: 'landing' }" :class="[scrolled ? ($route.name === 'landing' ? 'text-indigo-600 hover:text-indigo-600 font-bold' : 'text-gray-700 hover:text-indigo-600') : ($route.name === 'landing' ? 'text-white hover:text-indigo-200 underline font-bold' : 'text-white hover:text-indigo-200 hover:underline'), 'px-3 py-2 rounded-md text-sm cursor-pointer transition-all duration-1000']">
                        Home
                    </router-link>

                    <router-link :to="{ name: 'privacy-policy' }" :class="[scrolled ? ($route.name === 'privacy-policy' ? 'text-indigo-600 hover:text-indigo-600 font-bold' : 'text-gray-700 hover:text-indigo-600') : ($route.name === 'privacy-policy' ? 'text-white hover:text-indigo-200 underline font-bold' : 'text-white hover:text-indigo-200 hover:underline'), 'px-3 py-2 rounded-md text-sm cursor-pointer transition-all duration-1000']">
                        Privacy Policy
                    </router-link>
                    <router-link :to="{ name: 'terms-of-service' }" :class="[scrolled ? ($route.name === 'terms-of-service' ? 'text-indigo-600 hover:text-indigo-600 font-bold' : 'text-gray-700 hover:text-indigo-600') : ($route.name === 'terms-of-service' ? 'text-white hover:text-indigo-200 underline font-bold' : 'text-white hover:text-indigo-200 hover:underline'), 'px-3 py-2 rounded-md text-sm cursor-pointer transition-all duration-1000']">
                        Terms of Service
                    </router-link>
                    <router-link :to="{ name: 'data-deletion-instructions' }" :class="[scrolled ? ($route.name === 'data-deletion-instructions' ? 'text-indigo-600 hover:text-indigo-600 font-bold' : 'text-gray-700 hover:text-indigo-600') : ($route.name === 'data-deletion-instructions' ? 'text-white hover:text-indigo-200 underline font-bold' : 'text-white hover:text-indigo-200 hover:underline'), 'px-3 py-2 rounded-md text-sm cursor-pointer transition-all duration-1000']">
                        Data Deletion
                    </router-link>
                    <router-link :to="{ name: 'login' }" class="inline-block px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 hover:scale-105 active:scale-100 transition-all">
                        Sign In
                    </router-link>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button @click="toggleMenu" class="text-gray-700 hover:text-indigo-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" v-if="!isMenuOpen"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" v-else></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div v-if="isMenuOpen" class="md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <router-link :to="{ name: 'landing' }" :class="[$route.name === 'landing' ? 'text-indigo-600 font-bold' : 'text-gray-700 hover:text-indigo-600', 'block px-3 py-2 rounded-md text-base font-medium']">
                        Home
                    </router-link>
                    <router-link :to="{ name: 'privacy-policy' }" :class="[$route.name === 'privacy-policy' ? 'text-indigo-600 font-bold' : 'text-gray-700 hover:text-indigo-600', 'block px-3 py-2 rounded-md text-base font-medium']">
                        Privacy Policy
                    </router-link>
                    <router-link :to="{ name: 'terms-of-service' }" :class="[$route.name === 'terms-of-service' ? 'text-indigo-600 font-bold' : 'text-gray-700 hover:text-indigo-600', 'block px-3 py-2 rounded-md text-base font-medium']">
                        Terms of Service
                    </router-link>
                    <router-link :to="{ name: 'data-deletion-instructions' }" :class="[$route.name === 'data-deletion-instructions' ? 'text-indigo-600 font-bold' : 'text-gray-700 hover:text-indigo-600', 'block px-3 py-2 rounded-md text-base font-medium']">
                        Data Deletion
                    </router-link>
                    <router-link :to="{ name: 'login' }" class="block px-3 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-all text-center">
                        Sign In
                    </router-link>
                </div>
            </div>
        </nav>
    </header>
</template>

<script>
export default {
    name: 'WebsiteHeader',
    data() {
        return {
            isMenuOpen: false,
            scrolled: false,
        };
    },
    methods: {
        toggleMenu() {
            this.isMenuOpen = !this.isMenuOpen;
        },
        handleScroll() {
            // Update scrolled state based on scroll position
            this.scrolled = window.scrollY > 0;
        },
    },
    mounted() {
        // Add scroll event listener
        window.addEventListener('scroll', this.handleScroll);
    },
    beforeUnmount() {
        // Clean up scroll event listener
        window.removeEventListener('scroll', this.handleScroll);
    },
};
</script>

<template>

    <footer class="relative bg-gradient-to-b from-indigo-700 to-indigo-900 text-white pt-16 pb-8 overflow-hidden">

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">Telcoflo</h3>
                    <p class="text-indigo-200">
                        Empowering businesses with seamless omnichannel communication solutions.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><router-link :to="{ name: 'privacy-policy' }" class="text-indigo-200 hover:text-white">Privacy Policy</router-link></li>
                        <li><router-link :to="{ name: 'terms-of-service' }" class="text-indigo-200 hover:text-white">Terms of Service</router-link></li>
                        <li><router-link :to="{ name: 'data-deletion-instructions' }" class="text-indigo-200 hover:text-white">Data Deletion</router-link></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4">Contact Us</h3>
                    <p class="text-indigo-200">
                        Email: <a href="mailto:support@telcoflo.com" class="text-indigo-200 hover:text-white">support@telcoflo.com</a><br>
                        Address: Telcoflo, Gaborone, Botswana
                    </p>
                </div>
            </div>
        </div>

        <div class="relative z-10 mt-8 text-center text-indigo-200">
            &copy; {{ currentYear }} Telcoflo. All rights reserved.
        </div>

        <div class="wave-container flex flex-col justify-center items-center bg-red-200">
            <div class="wave w-full h-full"></div>
            <div class="wave w-full h-full"></div>
            <div class="wave w-full h-full"></div>
            <div class="wave w-full h-full"></div>
        </div>

    </footer>

</template>

<script>

    export default {
        data() {
            return {
                currentYear: new Date().getFullYear()
            };
        }
    };

</script>

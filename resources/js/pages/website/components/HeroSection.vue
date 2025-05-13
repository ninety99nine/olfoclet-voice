<template>
    <section class="relative bg-gradient-to-b from-indigo-900 to-indigo-700 text-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:pt-40 md:pb-20">
            <div class="flex flex-col items-center relative z-20">
                <!-- Title and Last Updated -->
                <div class="max-w-4xl text-center mb-12">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight mb-6 leading-tight bg-gradient-to-t from-indigo-100 to-white inline-block text-transparent bg-clip-text">
                        {{ title }}
                    </h1>
                    <p class="text-lg md:text-xl lg:text-2xl text-indigo-100 mb-8">Last Updated: May 12, 2025</p>
                </div>
            </div>
        </div>
        <div class="wave-container flex flex-col justify-center items-center">
            <div class="wave w-full h-full"></div>
            <div class="wave w-full h-full"></div>
            <div class="wave w-full h-full"></div>
            <div class="wave w-full h-full"></div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'LegalPageHero',
    props: {
        title: {
            type: String,
            required: true,
        },
    },
};
</script>

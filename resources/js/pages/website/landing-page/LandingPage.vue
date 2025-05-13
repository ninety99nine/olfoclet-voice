<template>
    <div class="bg-gray-50">
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-b from-indigo-900 to-indigo-700 text-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:pt-64 md:pb-20">
                <div class="flex flex-col items-center relative z-20">
                    <!-- Text Content -->
                    <div class="max-w-4xl text-center mb-12">
                        <h1 class="font-extrabold mb-6 leading-tight bg-gradient-to-t from-indigo-100 to-white inline-block text-transparent bg-clip-text">
                            <span class="text-4xl md:text-5xl lg:text-6xl block mb-2">Never Miss a Message.</span>
                            <span class="text-3xl md:text-4xl lg:text-5xl block">Chats, Calls & Emails In One Spot</span>
                        </h1>
                        <p class="text-lg md:text-xl lg:text-2xl text-indigo-100 mb-8">
                            Bring all your customer touchpoints into one unified platform +
                            <span class="inline-block px-2 py-1 bg-white border-4 border-indigo-100 rounded-full shadow-lg bubble-effect">
                                <span class="font-bold bg-gradient-to-r from-red-600 via-violet-600 to-indigo-600 bg-clip-text text-transparent">AI</span>
                            </span>
                            to automate everything while you focus on building connections and driving growth.
                        </p>
                        <div class="flex justify-center space-x-4">
                            <router-link :to="{ name: 'login' }" class="flex items-center space-x-2 px-6 py-3 bg-white text-indigo-600 font-semibold rounded-lg shadow-md hover:bg-gray-200 hover:scale-105 active:scale-100 transition-all">
                                <span>Get Started</span>
                                <ArrowRight size="20"></ArrowRight>
                            </router-link>
                        </div>
                    </div>
                    <!-- Visual (Dashboard Preview) -->
                    <div class="w-full">
                        <div class="flex flex-col justify-center items-center perspective-[1000px]">
                            <div class="w-fit relative rounded-2xl shadow-2xl shadow-purple-100" :style="[{ transform: `rotateX(${tiltAngle}deg) scale(${scale})` }]">
                                <div class="w-fit bg-gradient-to-r from-gray-100 to-purple-400 rounded-xl p-1">
                                    <img :src="'/images/dashboard.png'" alt="Telcoflo Dashboard" class="tilt-effect w-full max-w-6xl mx-auto rounded-lg overflow-hidden" />
                                </div>
                            </div>
                        </div>
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

        <!-- What We Offer Section -->
        <section class="py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 text-center mb-12">
                    What Telcoflo Offers
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Omnichannel Inbox -->
                    <div class="relative p-8 bg-gray-50 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4 text-center">Omnichannel Inbox</h3>
                        <p class="max-w-lg mx-auto text-gray-600 text-center mb-4">
                            Centralise your channels, including customer favourites like Calls, WhatsApp, Telegram, Instagram and more.
                        </p>
                        <img class="h-96 mx-auto" :src="'/images/omnichannel-inbox.png'" />
                    </div>

                    <!-- No Code, Just Build -->
                    <div class="relative p-8 bg-gray-50 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4 text-center">No Code, Just Build</h3>
                        <p class="max-w-lg mx-auto text-gray-600 text-center mb-4">
                            Simplify your IVR call flows, WhatsApp bots, and more with an intuitive no-code builder, empowering your team to create powerful workflows without technical expertise.
                        </p>
                        <img class="h-96 mx-auto" :src="'/images/call-flow.png'" />
                    </div>

                    <!-- AI Agents -->
                    <div class="relative p-8 bg-gray-50 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4 text-center">AI Agents</h3>
                        <p class="max-w-lg mx-auto text-gray-600 text-center mb-4">
                            Empower your business with AI Agents that effortlessly handle calls, acting as virtual receptionists, customer support experts, or technical specialists—freeing your team to focus on what matters most.
                        </p>
                        <img class="h-80 mx-auto" :src="'/images/ai-agent.png'" />
                    </div>

                    <!-- Insights -->
                    <div class="relative p-8 bg-gray-50 rounded-2xl shadow-sm border border-gray-200 hover:shadow-lg transition-all">
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4 text-center">Insights</h3>
                        <p class="max-w-lg mx-auto text-gray-600 text-center mb-4">
                            Gain actionable insights with every channel for simple to advanced analytics and reporting, helping you understand customer interactions, optimize workflows, and drive better business decisions.
                        </p>
                        <img class="w-full mx-auto" :src="'/images/insights.png'" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Integrations Section -->
        <section class="pt-10 pb-20 bg-gradient-to-b from-gray-50 to-indigo-100">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 text-center mb-4">
                    One Dashboard, Every Channel
                </h2>
                <p class="text-gray-600 text-center mb-12">Connect once and always stay connected</p>
            </div>

            <div class="max-w-4xl mx-auto rounded-lg overflow-hidden mb-20">
                <img :src="'/images/dashboard-channels.png'" alt="Channels" class="object-contain" />
            </div>

            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 text-center mb-4">
                    We Support Most Loved Channels
                </h2>
                <p class="text-gray-600 text-center mb-12">Staying in touch with your customers has never been easier</p>
                <!-- Marquee Container -->
                <div class="overflow-hidden marquee-mask">
                    <div ref="marquee" class="marquee flex space-x-8">
                        <div v-for="(logo, index) in logos" :key="index" class="flex-shrink-0">
                            <img :src="logo.src" :alt="logo.alt" class="w-16 h-16 object-contain" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 text-center mb-4">
                    Effortless Integration
                </h2>
                <p class="text-gray-600 text-center mb-12">
                    Seamlessly connect with your favorite tools to boost productivity.
                </p>
            </div>

            <div class="max-w-4xl mx-auto rounded-lg overflow-hidden">
                <img :src="'/images/dashboard-integrations.png'" alt="Integrations" class="object-contain" />
            </div>

        </section>

        <!-- CTA Section -->
        <section class="pt-20 pb-10 bg-indigo-700 text-white border-b border-indigo-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">
                    Ready to Transform Your Customer Communication?
                </h2>
                <p class="text-lg md:text-xl mb-8">
                    Join thousands of businesses using Telcoflo to connect with customers across all channels.
                </p>
                <router-link :to="{ name: 'login' }" class="w-fit mx-auto flex items-center space-x-2 px-6 py-3 bg-white text-indigo-600 font-semibold rounded-lg shadow-md hover:bg-gray-200 hover:scale-105 active:scale-100 transition-all">
                    <span>Get Started Now</span>
                    <ArrowRight size="20"></ArrowRight>
                </router-link>
            </div>
        </section>
    </div>
</template>

<script>
import { ArrowRight } from 'lucide-vue-next';

export default {
    name: 'LandingPage',
    components: { ArrowRight },
    data() {
        return {
            scale: 1, // Initial scale (no scaling)
            tiltAngle: 35, // Initial tilt angle (degrees)
            logos: [
                { src: '/images/channels/facebook.png', alt: 'Facebook' },
                { src: '/images/channels/whatsapp.png', alt: 'WhatsApp' },
                { src: '/images/channels/instagram.png', alt: 'Instagram' },
                { src: '/images/channels/messenger.png', alt: 'Messenger' },
                { src: '/images/channels/wechat.png', alt: 'WeChat' },
                { src: '/images/channels/telegram.png', alt: 'Telegram' },
                { src: '/images/channels/x.png', alt: 'X' },
                { src: '/images/channels/line.png', alt: 'Line' },
                { src: '/images/channels/sms.png', alt: 'SMS' },
                { src: '/images/channels/email.png', alt: 'Email' },
                { src: '/images/channels/phone3.png', alt: 'Phone' },
            ],
            logoWidth: 96, // Width of each logo (w-16 = 64px) + space-x-8 (32px) = 96px
            speed: 1, // Pixels per frame (adjustable for speed)
            animationFrameId: null,
        };
    },
    mounted() {
        const imageWrapper = this.$el.querySelector('.tilt-effect');
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    const ratio = entry.intersectionRatio;

                    // Tilt effect: only adjust tilt when the image is entering from below (scrolling down)
                    // or exiting at the bottom (scrolling up)
                    if (entry.boundingClientRect.top > 0) {
                        this.tiltAngle = 35 * (1 - ratio);
                    }

                    // Scale effect: adjust scale when the top of the image starts exiting the viewport (scrolling down)
                    if (entry.boundingClientRect.top < 0) {
                        // Calculate scale based on how much of the image has exited the viewport
                        // When ratio is 1 (fully in view), scale is 1
                        // When ratio is 0 (fully out of view), scale is 1.1
                        this.scale = 1 + (0.1 * (1 - ratio)); // Scale from 1 to 1.1
                    } else {
                        // Reset scale to 1 when the image is fully in view or entering from below
                        this.scale = 1;
                    }
                });
            },
            {
                threshold: Array.from({ length: 101 }, (_, i) => i / 100), // Array of thresholds [0, 0.01, 0.02, ..., 1] for smooth updates
            }
        );

        if (imageWrapper) {
            observer.observe(imageWrapper);
        }

        // Start the marquee animation
        this.startMarqueeAnimation();
    },
    beforeUnmount() {
        // Cancel the animation frame when the component is destroyed
        if (this.animationFrameId) {
            cancelAnimationFrame(this.animationFrameId);
        }
    },
    methods: {
        startMarqueeAnimation() {
            const marquee = this.$refs.marquee;
            if (!marquee) return;

            let position = 0;

            const animate = () => {
                // Move the marquee to the left
                position -= this.speed;
                marquee.style.transform = `translateX(${position}px)`;

                // Get the width of the container and the marquee
                const containerWidth = marquee.parentElement.offsetWidth;
                const marqueeWidth = marquee.scrollWidth;

                // When the first logo moves completely off-screen to the left
                if (Math.abs(position) >= this.logoWidth) {
                    // Move the first logo to the end
                    const firstLogo = marquee.children[0];
                    marquee.appendChild(firstLogo);
                    // Reset position to account for the moved logo
                    position += this.logoWidth;
                    marquee.style.transform = `translateX(${position}px)`;
                }

                // Continue the animation
                this.animationFrameId = requestAnimationFrame(animate);
            };

            // Start the animation
            this.animationFrameId = requestAnimationFrame(animate);
        },
    },
};
</script>

<style scoped>
.bubble-effect {
    animation: bubble 2s infinite ease-in-out;
}

@keyframes bubble {
    0%, 100% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.05); opacity: 1; }
}

/* Wave animation styles */
.wave-container {
    top: 500px;
    left: 50%;
    width: 500px;
    height: 500px;
    display: flex;
    position: absolute;
    align-items: center;
    justify-content: center;
    transform: translateX(-50%);
    -moz-transform: translateX(-50%);
    -webkit-transform: translateX(-50%);
}

.wave {
    opacity: 0;
    position: absolute;
    border-radius: 50%;
    animation: wave-ripple 5s ease-out infinite;
    border: 4px solid rgba(255, 255, 255, 0.073);
}

.wave:nth-child(1) {
    animation-delay: 0s;
}

.wave:nth-child(2) {
    animation-delay: 1s;
}

.wave:nth-child(3) {
    animation-delay: 2s;
}

@keyframes wave-ripple {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    30% {
        opacity: 1;
    }
    100% {
        transform: scale(3);
        opacity: 0;
    }
}

/* Marquee styles */
.marquee {
    display: flex;
    white-space: nowrap;
}

/* Fading effect for marquee edges */
.marquee-mask {
    -webkit-mask-image: linear-gradient(
        to right,
        transparent 0%,
        black 20%,
        black 80%,
        transparent 100%
    );
    mask-image: linear-gradient(
        to right,
        transparent 0%,
        black 10%,
        black 90%,
        transparent 100%
    );
}
</style>

<template>

    <div
        @click.stop="loading ? copyToClipboardNotReady() : copyToClipboard()"
        :class="
            showText
                ? [
                    { 'hover:bg-blue-50' : !loading},
                    'flex justify-between items-center bg-gray-50 p-2 border border-gray-300 rounded-md cursor-pointer relative']
                : ['relative cursor-pointer']">

        <!-- Copied Hint -->
        <div v-if="copied" :class="[showText ? '-top-8 right-0' : '-top-8 -right-7', 'absolute bg-green-500 text-white text-xs px-4 py-1 rounded-md']">
            Copied!
            <div class="absolute left-1/2 -bottom-1 transform -translate-x-1/2 w-0 h-0 border-l-4 border-l-transparent border-r-4 border-r-transparent border-t-4 border-t-green-500"></div>
        </div>

        <!-- Not Ready Hint -->
        <div v-if="notReady" :class="[showText ? '-top-8 -right-2' : '-top-8 -right-9', 'whitespace-nowrap absolute bg-yellow-500 text-white text-xs px-4 py-1 rounded-md']">
            Not Ready!
            <div class="absolute left-1/2 -bottom-1 transform -translate-x-1/2 w-0 h-0 border-l-4 border-l-transparent border-r-4 border-r-transparent border-t-4 border-t-yellow-500"></div>
        </div>

        <template v-if="showText">

            <!-- Store Link -->
            <ShineEffect v-if="loading" class="w-full">
                <div :class="[{ 'text-gray-300' : loading}, 'text-xs w-4/5 truncate']">{{ placeholder }}</div>
            </ShineEffect>

            <div v-else :class="[{ 'text-gray-300' : loading}, 'text-xs w-4/5 truncate']">{{ text }}</div>

        </template>

        <!-- Copy Icon -->
        <Copy size="16" class="shrink-0"></Copy>

    </div>

</template>

<script>

    import { Copy } from 'lucide-vue-next';
    import ShineEffect from '@Partials/ShineEffect.vue';

    export default {
        components: {
            Copy, ShineEffect
        },
        props: {
            loading: {
                type: Boolean,
                default: false
            },
            placeholder: {
                type: [String, null],
                default: '...'
            },
            text: {
                type: [String, Number],
            },
            showText: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                copied: false,
                notReady: false,
                copyToClipboardTimeout: null,
                copyToClipboardNotReadyTimeout: null,
            };
        },
        methods: {
            async copyToClipboard() {
                try {

                    if (this.copyToClipboardTimeout) {
                        clearTimeout(this.copyToClipboardTimeout);
                    }

                    await navigator.clipboard.writeText(this.text);
                    this.copied = true;

                    this.copyToClipboardTimeout = setTimeout(() => {
                        this.copied = false;
                    }, 2000);

                } catch (err) {
                    console.error('Failed to copy:', err);
                }
            },
            async copyToClipboardNotReady() {
                if (this.copyToClipboardNotReadyTimeout) {
                    clearTimeout(this.copyToClipboardNotReadyTimeout);
                }

                this.notReady = true;

                this.copyToClipboardNotReadyTimeout = setTimeout(() => {
                    this.notReady = false;
                }, 2000);
            }
        }
    };

</script>

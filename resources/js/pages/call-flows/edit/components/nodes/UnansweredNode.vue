<template>

    <div class="flex items-center space-x-1">

        <div class="w-32 bg-red-100 border border-red-500 rounded-4xl px-4 py-2">

            <div class="flex items-center space-x-2">
                <PhoneOff size="16"></PhoneOff>
                <span class="text-xs">Unanswered</span>
            </div>

        </div>

        <CirclePlus
            size="20"
            @click="data.showDrawer"
            class="text-blue-500 cursor-pointer hover:scale-125 active:scale-100 transition-transform duration-300 mr-2">
        </CirclePlus>

        <Handle type="target" position="left" />
        <Handle type="source" position="right" />

    </div>

</template>

<script>

    import { Handle } from '@vue-flow/core';
    import { PhoneOff, Settings, CirclePlus } from 'lucide-vue-next';

    export default {
        components: { Handle, PhoneOff, Settings, CirclePlus },
        props: {
            data: {
                type: Object
            },
        },
    };

</script>

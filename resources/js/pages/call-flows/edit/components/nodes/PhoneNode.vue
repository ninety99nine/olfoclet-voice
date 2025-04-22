<template>

    <div class="flex items-center space-x-1">

        <div class="bg-white border-2 border-green-500 rounded-sm shadow-sm p-6 space-y-2">

            <div class="text-sm text-gray-400">When customer calls</div>

            <div class="flex items-center space-x-2">

                <img :src="`/svgs/country-flags/BW.svg`" alt="BW" class="w-6 h-5 rounded-sm object-cover">
                <span class="text-sm">{{ data.number }}</span>

                <Settings size="20" class="text-gray-500 cursor-pointer hover:scale-125 active:scale-100 transition-transform duration-300"></Settings>

            </div>

        </div>

        <CirclePlus
            size="20"
            @click="data.openDrawer"
            class="text-blue-500 cursor-pointer hover:scale-125 active:scale-100 transition-transform duration-300 mr-2">
        </CirclePlus>

        <Handle type="target" position="right" />

    </div>

</template>

<script>

    import { Handle } from '@vue-flow/core';
    import { Settings, CirclePlus } from 'lucide-vue-next';

    export default {
        components: { Handle, Settings, CirclePlus },
        props: {
            data: {
                type: Object
            },
        },
    };

</script>

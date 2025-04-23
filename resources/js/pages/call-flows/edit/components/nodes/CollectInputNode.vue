<template>

    <div class="flex items-center space-x-1">

        <div class="w-60 bg-white border-2 border-orange-500 rounded-sm py-4 space-y-2 hover:shadow hover:border-blue-500 transition-all">

            <div class="flex items-center justify-between border-b border-gray-300 pb-2 mb-2 px-4">
                <div class="flex items-center space-x-2">
                    <Keyboard size="20"></Keyboard>
                    <span class="text-sm">Collect Input</span>
                </div>
                <div class="flex items-center space-x-2">
                    <CirclePlay size="20" class="text-gray-500 cursor-pointer hover:scale-125 active:scale-100 transition-transform duration-300"></CirclePlay>
                    <Settings size="20" class="text-gray-500 cursor-pointer hover:scale-125 active:scale-100 transition-transform duration-300"></Settings>
                </div>
            </div>

            <div class="px-4">
                <div class="flex justify-between">
                    <span class="text-sm truncate">Recording v1</span>
                    <span class="text-sm">(20s)</span>
                </div>
                <p class="text-sm truncate">Pattern: /d/d/d/d</p>
                <p class="text-sm">Retry on invalid: No</p>
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
    import { Keyboard, Settings, CirclePlus, CirclePlay } from 'lucide-vue-next';

    export default {
        components: { Handle, Keyboard, Settings, CirclePlus, CirclePlay },
        props: {
            data: {
                type: Object
            },
        },
    };

</script>

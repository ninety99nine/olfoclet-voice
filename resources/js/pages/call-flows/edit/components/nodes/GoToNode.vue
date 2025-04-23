<template>

    <div class="flex items-center space-x-1">

        <div class="w-60 bg-white border border-gray-500 rounded-sm py-4 space-y-2 hover:shadow hover:border-blue-500 transition-all">

            <div class="flex items-center justify-between border-b border-gray-300 pb-2 mb-2 px-4">
                <div class="flex items-center space-x-2">
                    <ChevronRight size="20"></ChevronRight>
                    <span class="text-sm">Go To</span>
                </div>
                <Settings size="20" class="text-gray-500 cursor-pointer hover:scale-125 active:scale-100 transition-transform duration-300"></Settings>
            </div>

            <div class="px-4">
                <p class="text-sm text-blue-500">Sales</p>
                <p class="text-sm">Call To Group (ID: 645234)</p>
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
    import { ChevronRight, Settings, CirclePlus } from 'lucide-vue-next';

    export default {
        components: { Handle, ChevronRight, Settings, CirclePlus },
        props: {
            data: {
                type: Object
            },
        },
    };

</script>

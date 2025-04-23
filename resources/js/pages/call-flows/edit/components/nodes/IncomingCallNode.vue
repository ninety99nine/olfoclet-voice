<template>

    <div class="flex items-center space-x-1">

        <div class="w-48 bg-white border-2 border-green-500 rounded-sm py-4 space-y-2">

            <div class="flex items-center justify-between px-4">
                <div class="flex items-center space-x-2">
                    <div class="rounded-full bg-green-500 text-white p-2">
                        <PhoneCall size="16" class="animate-pulse"></PhoneCall>
                    </div>
                    <span class="text-sm">Incoming Call</span>
                </div>
                <Settings
                    size="20"
                    @click="() => data.showDrawer(data)"
                    class="text-gray-500 cursor-pointer hover:scale-125 active:scale-100 transition-transform duration-300">
                </Settings>
            </div>

        </div>

        <CirclePlus
            size="20"
            @click="data.showDrawer"
            class="text-blue-500 cursor-pointer hover:scale-125 active:scale-100 transition-transform duration-300 mr-2">
        </CirclePlus>

        <Handle type="source" position="right" />

    </div>

</template>

<script>

    import { Handle } from '@vue-flow/core';
    import { PhoneCall, Settings, CirclePlus } from 'lucide-vue-next';

    export default {
        components: { Handle, PhoneCall, Settings, CirclePlus },
        props: {
            data: {
                type: Object
            },
        },
    };

</script>

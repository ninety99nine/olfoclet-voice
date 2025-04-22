<template>

    <div class="flex items-center space-x-1">

        <div class="w-48 bg-white border border-gray-500 rounded-sm py-4 space-y-2 hover:shadow hover:border-blue-500 transition-all">

            <div class="flex items-center justify-between border-b border-gray-300 pb-2 mb-2 px-4">
                <div class="flex items-center space-x-2">
                    <UsersRound size="20"></UsersRound>
                    <span class="text-sm">Call Group</span>
                </div>
                <Settings size="20" class="text-gray-500 cursor-pointer hover:scale-125 active:scale-100 transition-transform duration-300"></Settings>
            </div>

            <div class="flex justify-between items-center px-4">
                <span class="text-sm truncate">Billing</span>
                <span class="text-sm">(20s)</span>
            </div>

        </div>

        <CirclePlus
            size="20"
            @click="data.openDrawer"
            class="text-blue-500 cursor-pointer hover:scale-125 active:scale-100 transition-transform duration-300 mr-2">
        </CirclePlus>

        <Handle type="target" position="left" />
        <Handle type="source" position="right" />

    </div>

</template>

<script>

    import { Handle } from '@vue-flow/core';
    import { UsersRound, Settings, CirclePlus } from 'lucide-vue-next';

    export default {
        components: { Handle, UsersRound, Settings, CirclePlus },
        props: {
            data: {
                type: Object
            },
        },
    };

</script>

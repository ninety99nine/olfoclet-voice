<template>

    <div class="flex items-center">

        <div class="bg-green-100 border border-green-500 rounded-4xl px-6 py-2">

            <div class="flex items-center space-x-2">
                <Phone size="20"></Phone>
                <span class="text-sm">Answered</span>
            </div>

        </div>

        <Handle type="target" position="left" />
        <Handle type="source" position="right" />

    </div>

</template>

<script>

    import { Handle } from '@vue-flow/core';
    import { Phone, Settings, CirclePlus } from 'lucide-vue-next';

    export default {
        components: { Handle, Phone, Settings, CirclePlus },
        props: {
            data: {
                type: Object
            },
        },
    };

</script>

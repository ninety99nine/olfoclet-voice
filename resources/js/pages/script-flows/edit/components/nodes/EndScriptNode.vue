<template>
    <div class="flex items-center space-x-1">
        <div class="w-32 bg-green-100 border border-green-500 rounded-4xl px-4 py-2">
            <div class="flex items-center space-x-2">
                <CheckCircle size="16" />
                <span class="text-xs">End Script</span>
            </div>
        </div>

        <Handle type="target" position="left" />
    </div>
</template>

<script>
import { Handle } from '@vue-flow/core';
import { CheckCircle } from 'lucide-vue-next';

export default {
    components: { Handle, CheckCircle },
    props: {
        data: {
            type: Object,
        },
    },
};
</script>

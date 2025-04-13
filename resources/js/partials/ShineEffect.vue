<template>
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="w-full h-full absolute left-0 top-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-75 animate-shine"></div>
        </div>
        <slot></slot>
    </div>
</template>

<style>
    @keyframes shine {
        0% {
            transform: translateX(-100%);
        }
        100% {
            transform: translateX(100%);
        }
    }

    .animate-shine {
        animation: shine 2s infinite;
    }
</style>

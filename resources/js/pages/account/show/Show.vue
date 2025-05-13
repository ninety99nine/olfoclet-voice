<template>

    <div class="min-h-screen bg-gray-50 p-6">

        <div class="max-w-2xl mx-auto">

            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">

                <h1 class="text-3xl font-bold text-gray-900 mb-8">My Account</h1>

                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <p class="text-gray-900">{{ authState.user?.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="text-gray-900">{{ authState.user?.email }}</p>
                    </div>

                </div>

                <div class="flex justify-end mt-6">

                    <Button type="light" size="md" :rightIcon="MoveRight" :action="goBack">
                        <span class="mr-2">Update Account</span>
                    </Button>

                </div>

            </div>

        </div>

    </div>

</template>

<script>

    import Button from '@Partials/Button.vue';
    import { MoveRight } from 'lucide-vue-next';

    export default {
        name: 'ShowAccount',
        inject: ['authState'],
        components: { Button },
        data() {
            return {
                MoveRight
            }
        },
        methods: {
            goBack() {
                this.$router.push({ name: 'update-account' });
            }
        }
    };

</script>

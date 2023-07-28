<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    total_vehicles: {
        type: Number,
    }
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex">
                    <div class="bg-white shadow-lg rounded-lg p-6 w-72">
                        <div class="text-4xl font-bold text-center mb-6">{{total_vehicles}}</div>
                        <p class="text-center text-gray-500">Registered Vehicles</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

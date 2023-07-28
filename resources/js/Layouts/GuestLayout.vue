<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    RP Autos
                                </Link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <div class="ml-6 relative">
                                <Link :href="route('login')">
                                    Login
                                </Link>
                            </div>
                            <div class="ml-6 relative">
                                <Link :href="route('register')">
                                    Register
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

            </nav>

            <main>
                <div class="flex flex-col sm:justify-center items-center bg-gray-100">
                    <div
                        class="w-full sm:max-w-md mt-16 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
                    >
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
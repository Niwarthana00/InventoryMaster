<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />

    <div class="relative flex flex-col items-center justify-center min-h-screen bg-gray-50 dark:bg-gray-900 selection:bg-blue-500 selection:text-white">
        
        <!-- Navbar -->
        <div v-if="canLogin" class="fixed top-0 right-0 px-6 py-4 sm:block z-10">
            <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                Dashboard
            </Link>

            <template v-else>
                <Link :href="route('login')" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                    Log in
                </Link>

                <Link v-if="canRegister" :href="route('register')" class="ml-6 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                    Register
                </Link>
            </template>
        </div>

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto p-6 lg:p-8 w-full">
            <div class="flex flex-col items-center text-center">
                
                <!-- Logo -->
                <div class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-lg ring-1 ring-gray-200 dark:ring-gray-700">
                    <svg viewBox="0 0 24 24" fill="none" class="h-16 w-16 text-blue-600" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 3L20 7.5V16.5L12 21L4 16.5V7.5L12 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 12L20 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 12V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 12L4 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <!-- Headline -->
                <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-6xl mb-6 tracking-tight">
                    Inventory Management <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Simplified</span>
                </h1>

                <!-- Description -->
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
                    Track your stock, manage items, and view transaction history effortlessly. 
                    The perfect solution for small businesses to keep their inventory organized.
                </p>

                <!-- CTA Buttons -->
                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center w-full sm:w-auto">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transition duration-300 transform hover:-translate-y-1">
                        Go to Dashboard &rarr;
                    </Link>

                    <template v-else>
                        <Link :href="route('login')" class="bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transition duration-300 transform hover:-translate-y-1">
                            Login
                        </Link>

                        <Link v-if="canRegister" :href="route('register')" class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 font-bold py-3 px-8 rounded-full shadow-lg transition duration-300 transform hover:-translate-y-1">
                            Register Now
                        </Link>
                    </template>
                </div>
            </div>

            <!-- Footer Stats / Info -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 text-center border-t border-gray-200 dark:border-gray-700 pt-8">
                <div>
                    <div class="text-3xl font-bold text-blue-600">Fast</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Real-time Updates</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-blue-600">Secure</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">Authenticated Access</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-blue-600">Simple</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">User Friendly Interface</div>
                </div>
            </div>
            
            <div class="mt-12 text-center text-xs text-gray-400">
                Inventory System v1.0 &copy; {{ new Date().getFullYear() }}
            </div>
        </div>
    </div>
</template>

<style scoped>
    .bg-gray-100 {
        background-color: #f7fafc;
        background-color: rgba(247, 250, 252, var(--tw-bg-opacity));
    }

    .border-gray-200 {
        border-color: #edf2f7;
        border-color: rgba(237, 242, 247, var(--tw-border-opacity));
    }

    .text-gray-400 {
        color: #cbd5e0;
        color: rgba(203, 213, 224, var(--tw-text-opacity));
    }

    .text-gray-500 {
        color: #a0aec0;
        color: rgba(160, 174, 192, var(--tw-text-opacity));
    }

    .text-gray-600 {
        color: #718096;
        color: rgba(113, 128, 150, var(--tw-text-opacity));
    }

    .text-gray-700 {
        color: #4a5568;
        color: rgba(74, 85, 104, var(--tw-text-opacity));
    }

    .text-gray-900 {
        color: #1a202c;
        color: rgba(26, 32, 44, var(--tw-text-opacity));
    }

    @media (prefers-color-scheme: dark) {
        .dark\:bg-gray-800 {
            background-color: #2d3748;
            background-color: rgba(45, 55, 72, var(--tw-bg-opacity));
        }

        .dark\:bg-gray-900 {
            background-color: #1a202c;
            background-color: rgba(26, 32, 44, var(--tw-bg-opacity));
        }

        .dark\:border-gray-700 {
            border-color: #4a5568;
            border-color: rgba(74, 85, 104, var(--tw-border-opacity));
        }

        .dark\:text-white {
            color: #fff;
            color: rgba(255, 255, 255, var(--tw-text-opacity));
        }

        .dark\:text-gray-400 {
            color: #cbd5e0;
            color: rgba(203, 213, 224, var(--tw-text-opacity));
        }
    }
</style>

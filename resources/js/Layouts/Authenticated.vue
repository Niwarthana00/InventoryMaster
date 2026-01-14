<script setup>
import { ref } from 'vue';
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import BreezeDropdown from '@/Components/Dropdown.vue';
import BreezeDropdownLink from '@/Components/DropdownLink.vue';
import BreezeNavLink from '@/Components/NavLink.vue';
import BreezeResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/inertia-vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-900 selection:bg-blue-500 selection:text-white">
            <nav class="bg-gray-800 border-b border-gray-700 h-20 flex items-center">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    <div class="flex justify-between items-center h-full">
                        <div class="flex items-center">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center gap-3">
                                <Link :href="route('dashboard')" class="flex items-center gap-3">
                                    <div class="bg-blue-600/20 p-2 rounded-lg">
                                        <BreezeApplicationLogo class="block h-8 w-auto text-blue-500" />
                                    </div>
                                    <span class="text-2xl font-bold text-white tracking-tight">StockMaster</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Search Slot (Middle) -->
                        <div class="flex-1 flex items-center justify-center max-w-2xl mx-auto px-6 hidden md:flex" v-if="$slots.search">
                            <slot name="search" />
                        </div>
                        <div class="flex-1" v-else></div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6 gap-6">
                            <!-- Actions Slot -->
                            <div v-if="$slots.actions">
                                <slot name="actions" />
                            </div>

                            <!-- User Profile -->
                            <div class="ml-3 relative border-l border-gray-700 pl-6">
                                <BreezeDropdown align="right" width="48">
                                    <template #trigger>
                                        <button type="button" class="flex items-center gap-3 text-sm font-medium text-gray-300 hover:text-white transition focus:outline-none group">
                                            <div class="text-right hidden md:block group-hover:text-blue-400 transition">
                                                <div class="text-white font-bold leading-tight">{{ $page.props.auth.user.name }}</div>
                                                <div class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">Admin</div>
                                            </div>
                                            <div class="bg-gray-700 p-1.5 rounded-lg border border-gray-600 group-hover:border-gray-500 transition">
                                                <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            </div>
                                        </button>
                                    </template>

                                    <template #content>
                                        <BreezeDropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </BreezeDropdownLink>
                                    </template>
                                </BreezeDropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden bg-gray-800 absolute top-20 left-0 w-full z-50 border-b border-gray-700 shadow-xl">
                    <div class="pt-2 pb-3 space-y-1">
                        <BreezeResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </BreezeResponsiveNavLink>
                        <!-- Mobile Search Placeholder -->
                         <div class="px-4 py-2" v-if="$slots.search">
                            <slot name="search" />
                         </div>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-700">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-200">{{ $page.props.auth.user.name }}</div>
                            <div class="font-medium text-sm text-gray-400">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <BreezeResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </BreezeResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                <div class="py-6">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                         <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

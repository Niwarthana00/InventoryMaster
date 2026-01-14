<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';

const props = defineProps({
    item: Object,
    transactions: Array,
});
</script>

<template>
    <Head title="Item Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                    Item Details: {{ item.name }}
                </h2>
                <Link :href="route('dashboard')" class="text-sm text-gray-400 hover:text-white underline transition-colors">
                    &larr; Back to Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Item Summary Card -->
                <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-6 border border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-4 bg-gray-900 rounded-lg border border-blue-900">
                            <div class="text-sm text-blue-400 font-medium uppercase">Current Stock</div>
                            <div class="mt-2 text-3xl font-bold text-white">
                                {{ parseFloat(item.quantity) }} <span class="text-lg font-normal text-gray-400">{{ item.unit }}</span>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-900 rounded-lg border border-gray-700">
                            <div class="text-sm text-gray-400 font-medium uppercase">Last Updated</div>
                            <div class="mt-2 text-lg font-semibold text-gray-200">
                                {{ new Date(item.updated_at).toLocaleString() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-700">
                    <div class="p-6 bg-gray-800 border-b border-gray-700">
                        <h3 class="text-lg font-medium text-white mb-4">Transaction History</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Change</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Balance</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Note</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-700">
                                    <tr v-for="t in transactions" :key="t.id" class="hover:bg-gray-700 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            {{ new Date(t.created_at).toLocaleString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="t.type === 'add' ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200'">
                                                {{ t.type.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ t.user ? t.user.name : 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium"
                                            :class="t.type === 'add' ? 'text-green-400' : 'text-red-400'">
                                            {{ t.type === 'add' ? '+' : '-' }}{{ parseFloat(t.quantity) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-white font-bold">
                                            {{ parseFloat(t.balance_after) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ t.note || '-' }}
                                        </td>
                                    </tr>
                                    <tr v-if="transactions.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No transactions found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

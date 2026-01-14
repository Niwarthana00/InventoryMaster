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
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Item Details: {{ item.name }}
                </h2>
                <Link :href="route('dashboard')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    &larr; Back to Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Item Summary Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="text-sm text-blue-600 font-medium uppercase">Current Stock</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900">
                                {{ parseFloat(item.quantity) }} <span class="text-lg font-normal text-gray-500">{{ item.unit }}</span>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="text-sm text-gray-600 font-medium uppercase">Last Updated</div>
                            <div class="mt-2 text-lg font-semibold text-gray-900">
                                {{ new Date(item.updated_at).toLocaleString() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction History</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="t in transactions" :key="t.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(t.created_at).toLocaleString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="t.type === 'add' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                {{ t.type.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ t.user ? t.user.name : 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium"
                                            :class="t.type === 'add' ? 'text-green-600' : 'text-red-600'">
                                            {{ t.type === 'add' ? '+' : '-' }}{{ parseFloat(t.quantity) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-bold">
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

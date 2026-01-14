<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, watch, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    items: Array,
    filters: Object,
});

// Computed Stats
const totalItems = computed(() => props.items.length);
const lowStockCurrent = computed(() => props.items.filter(item => parseFloat(item.quantity) < 10).length);
const outOfStock = computed(() => props.items.filter(item => parseFloat(item.quantity) <= 0).length);

// Search State
const search = ref(props.filters.search || '');
let searchTimer = null;

watch(search, (value) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        Inertia.get(route('dashboard'), { search: value }, { preserveState: true, replace: true });
    }, 300);
});

// Modal States
const showCreateModal = ref(false);
const showAddStockModal = ref(false);
const showDeductStockModal = ref(false);
const selectedItem = ref(null);

// Forms
const createForm = useForm({ name: '', unit: '' });
const stockForm = useForm({ quantity: '', note: '' });

// Actions
const openAddStock = (item) => {
    selectedItem.value = item;
    stockForm.reset();
    showAddStockModal.value = true;
};

const openDeductStock = (item) => {
    selectedItem.value = item;
    stockForm.reset();
    showDeductStockModal.value = true;
};

const submitCreate = () => {
    createForm.post(route('items.store'), {
        onSuccess: () => { showCreateModal.value = false; createForm.reset(); },
    });
};

const submitAddStock = () => {
    stockForm.post(route('items.add', selectedItem.value.id), {
        onSuccess: () => showAddStockModal.value = false,
    });
};

const submitDeductStock = () => {
    stockForm.post(route('items.deduct', selectedItem.value.id), {
        onSuccess: () => showDeductStockModal.value = false,
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Inventory Overview
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Items</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ totalItems }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-yellow-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Low Stock (< 10)</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ lowStockCurrent }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-red-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Out of Stock</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">{{ outOfStock }}</div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        
                        <!-- Toolbar -->
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                            <div class="relative w-full md:w-1/3">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input 
                                    v-model="search"
                                    type="text" 
                                    placeholder="Search inventory..." 
                                    class="w-full pl-10 pr-4 py-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                >
                            </div>
                            <button 
                                @click="showCreateModal = true"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:shadow-outline-blue transition ease-in-out duration-150 shadow-md"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add New Item
                            </button>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Item Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Unit</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Stock Level</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                                            <div class="text-xs text-gray-400">ID: #{{ item.id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ item.unit }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-bold" 
                                                :class="{
                                                    'text-green-600': Number(item.quantity) >= 10,
                                                    'text-yellow-600': Number(item.quantity) < 10 && Number(item.quantity) > 0,
                                                    'text-red-600': Number(item.quantity) <= 0
                                                }"
                                            >
                                                {{ parseFloat(item.quantity) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                            <button @click="openAddStock(item)" class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-md transition duration-150 border border-green-200" title="Add Stock">
                                                <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Add</span>
                                            </button>
                                            <button @click="openDeductStock(item)" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition duration-150 border border-red-200" title="Deduct Stock">
                                                <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg> Deduct</span>
                                            </button>
                                            <Link :href="route('items.show', item.id)" class="text-gray-500 hover:text-blue-600 inline-flex items-center ml-2" title="View History">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="items.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                                <p>No inventory items found.</p>
                                                <p class="text-sm mt-1">Get started by adding a new item.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Item Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 overflow-y-auto px-4 py-6 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900 opacity-50 transition-opacity" @click="showCreateModal = false"></div>
            <div class="bg-white rounded-lg shadow-xl transform transition-all sm:w-full sm:max-w-md z-10">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">New Item</h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Item Name</label>
                            <input type="text" v-model="createForm.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="e.g. Laptop Stand">
                            <div v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit</label>
                            <input type="text" v-model="createForm.unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="e.g. pcs, kg">
                            <div v-if="createForm.errors.unit" class="text-red-500 text-xs mt-1">{{ createForm.errors.unit }}</div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</button>
                        <button type="button" @click="submitCreate" :disabled="createForm.processing" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Create Item</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Stock Modal -->
        <div v-if="showAddStockModal" class="fixed inset-0 overflow-y-auto px-4 py-6 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900 opacity-50 transition-opacity" @click="showAddStockModal = false"></div>
            <div class="bg-white rounded-lg shadow-xl transform transition-all sm:w-full sm:max-w-md z-10">
                <div class="px-6 py-4 border-b border-gray-100 bg-green-50 rounded-t-lg">
                    <h3 class="text-lg font-medium text-green-800">Receive Stock: {{ selectedItem?.name }}</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quantity to Add</label>
                            <input type="number" step="0.001" v-model="stockForm.quantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="0.00">
                            <div v-if="stockForm.errors.quantity" class="text-red-500 text-xs mt-1">{{ stockForm.errors.quantity }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Note (Optional)</label>
                            <input type="text" v-model="stockForm.note" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm" placeholder="e.g. Purchase Order #123">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showAddStockModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="button" @click="submitAddStock" :disabled="stockForm.processing" class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Confirm Add</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deduct Stock Modal -->
        <div v-if="showDeductStockModal" class="fixed inset-0 overflow-y-auto px-4 py-6 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900 opacity-50 transition-opacity" @click="showDeductStockModal = false"></div>
            <div class="bg-white rounded-lg shadow-xl transform transition-all sm:w-full sm:max-w-md z-10">
                <div class="px-6 py-4 border-b border-gray-100 bg-red-50 rounded-t-lg">
                    <h3 class="text-lg font-medium text-red-800">Deduct Stock: {{ selectedItem?.name }}</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quantity to Deduct</label>
                            <input type="number" step="0.001" v-model="stockForm.quantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="0.00">
                            <div v-if="stockForm.errors.quantity" class="text-red-500 text-xs mt-1">{{ stockForm.errors.quantity }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reason (Optional)</label>
                            <input type="text" v-model="stockForm.note" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="e.g. Sales, Damaged">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showDeductStockModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="button" @click="submitDeductStock" :disabled="stockForm.processing" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Confirm Deduct</button>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

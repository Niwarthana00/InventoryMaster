<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    items: Array,
    filters: Object,
});

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
const createForm = useForm({
    name: '',
    unit: '',
});

const stockForm = useForm({
    quantity: '',
    note: '',
});

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
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
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
    <Head title="Inventory Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Inventory Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Action Bar -->
                <div class="flex justify-between mb-6">
                    <div class="w-1/3">
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Search items..." 
                            class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                        >
                    </div>
                    <button 
                        @click="showCreateModal = true"
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                    >
                        + Create New Item
                    </button>
                </div>

                <!-- Items Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in items" :key="item.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <Link :href="route('items.show', item.id)" class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                            {{ item.name }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.unit }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-800">
                                        {{ parseFloat(item.quantity) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button @click="openAddStock(item)" class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded border border-green-200 cursor-pointer">Add Stock</button>
                                        <button @click="openDeductStock(item)" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded border border-red-200 cursor-pointer">Deduct</button>
                                        <Link :href="route('items.show', item.id)" class="text-gray-600 hover:text-gray-900 ml-2">History &rarr;</Link>
                                    </td>
                                </tr>
                                <tr v-if="items.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No items found in inventory.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Item Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 transform transition-all" @click="showCreateModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">Create New Inventory Item</h2>
                    <div class="mt-6 space-y-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Item Name</label>
                            <input type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="createForm.name" />
                            <div v-if="createForm.errors.name" class="text-red-500 text-sm mt-1">{{ createForm.errors.name }}</div>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Unit of Measurement</label>
                            <input type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="createForm.unit" />
                            <div v-if="createForm.errors.unit" class="text-red-500 text-sm mt-1">{{ createForm.errors.unit }}</div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="button" class="bg-gray-200 text-gray-800 hover:bg-gray-300 mr-2 px-4 py-2 rounded-md uppercase text-xs font-semibold tracking-widest" @click="showCreateModal = false">Cancel</button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150" @click="submitCreate" :disabled="createForm.processing">Create Item</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Stock Modal -->
        <div v-if="showAddStockModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 transform transition-all" @click="showAddStockModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">Add Stock: {{ selectedItem?.name }}</h2>
                    <div class="mt-6 space-y-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Quantity to Add</label>
                            <input type="number" step="0.001" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="stockForm.quantity" />
                            <div v-if="stockForm.errors.quantity" class="text-red-500 text-sm mt-1">{{ stockForm.errors.quantity }}</div>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Note (Optional)</label>
                            <input type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="stockForm.note" placeholder="e.g. New Shipment" />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="button" class="bg-gray-200 text-gray-800 hover:bg-gray-300 mr-2 px-4 py-2 rounded-md uppercase text-xs font-semibold tracking-widest" @click="showAddStockModal = false">Cancel</button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-green transition ease-in-out duration-150" @click="submitAddStock" :disabled="stockForm.processing">Confirm Add</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deduct Stock Modal -->
        <div v-if="showDeductStockModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 transform transition-all" @click="showDeductStockModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-red-700">Deduct Stock: {{ selectedItem?.name }}</h2>
                    <div class="mt-6 space-y-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Quantity to Deduct</label>
                            <input type="number" step="0.001" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="stockForm.quantity" />
                            <div v-if="stockForm.errors.quantity" class="text-red-500 text-sm mt-1">{{ stockForm.errors.quantity }}</div>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Reason (Optional)</label>
                            <input type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="stockForm.note" placeholder="e.g. Sold" />
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="button" class="bg-gray-200 text-gray-800 hover:bg-gray-300 mr-2 px-4 py-2 rounded-md uppercase text-xs font-semibold tracking-widest" @click="showDeductStockModal = false">Cancel</button>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red transition ease-in-out duration-150" @click="submitDeductStock" :disabled="stockForm.processing">Confirm Deduct</button>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

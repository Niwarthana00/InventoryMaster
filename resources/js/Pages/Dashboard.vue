<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, watch, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    items: Object,
    filters: Object,
});

// Computed Stats
const totalItems = computed(() => props.items.total);
const lowStockCurrent = computed(() => props.items.data.filter(item => parseFloat(item.quantity) < 10).length); // Note: This only counts current page. Ideally pass stats from backend.
const outOfStock = computed(() => props.items.data.filter(item => parseFloat(item.quantity) <= 0).length); // Same note.

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
    items: [
        { name: '', unit: '', quantity: '' }
    ]
});
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

const addItemBlock = () => {
    createForm.items.push({ name: '', unit: '', quantity: '' });
};

const removeItemBlock = (index) => {
    if (createForm.items.length > 1) {
        createForm.items.splice(index, 1);
    }
};

const submitCreate = () => {
    createForm.post(route('items.store'), {
        onSuccess: () => {
            createForm.reset();
            showCreateModal.value = false;
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
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Search Bar in Navbar -->
        <template #search>
            <div class="relative w-full max-w-xl">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input 
                    v-model="search"
                    type="text" 
                    placeholder="Search inventory items..." 
                    class="w-full pl-10 pr-4 py-2 bg-gray-900 border border-gray-700 text-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-500 focus:bg-gray-800"
                >
            </div>
        </template>

        <!-- Actions in Navbar -->
        <template #actions>
            <button 
                @click="showCreateModal = true"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150 shadow-lg"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Items
            </button>
        </template>

        <div class="py-6 space-y-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Items -->
                <div class="bg-gray-900 overflow-hidden shadow-lg rounded-xl p-6 border border-gray-800 relative group hover:border-blue-500/30 transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-sm font-medium text-gray-400">Total Items</div>
                            <div class="mt-2 text-4xl font-bold text-white tracking-tight">{{ totalItems }}</div>
                        </div>
                        <div class="p-3 bg-blue-500/10 rounded-lg text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Low Stock -->
                <div class="bg-gray-900 overflow-hidden shadow-lg rounded-xl p-6 border border-gray-800 relative group hover:border-yellow-500/30 transition duration-300">
                     <div class="flex justify-between items-start">
                        <div>
                            <div class="text-sm font-medium text-gray-400">Low Stock Items</div>
                            <div class="mt-2 text-4xl font-bold text-white tracking-tight">{{ lowStockCurrent }}</div>
                            <div class="mt-2 text-sm text-yellow-500 flex items-center gap-1" v-if="lowStockCurrent > 0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Attention needed
                            </div>
                            <div class="mt-2 text-sm text-green-500 flex items-center gap-1" v-else>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                All good
                            </div>
                        </div>
                        <div class="p-3 bg-yellow-500/10 rounded-lg text-yellow-500 group-hover:bg-yellow-500 group-hover:text-white transition duration-300">
                           <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Out of Stock / Stock Volume (Custom stat for fun, or just Out of Stock) -->
                <!-- Let's stick to Out of Stock but style it like 'Total Stock Volume' in screenshot if needed, but Out of Stock is more useful -->
                <!-- Actually user screenshot has "Total Stock Volume". Let's calculate that sum of quantities! -->
                <div class="bg-gray-900 overflow-hidden shadow-lg rounded-xl p-6 border border-gray-800 relative group hover:border-green-500/30 transition duration-300">
                     <div class="flex justify-between items-start">
                        <div>
                             <!-- Summing up quantities for "Stock Volume" appearance -->
                            <div class="text-sm font-medium text-gray-400">Total Stock Volume</div>
                            <div class="mt-2 text-4xl font-bold text-white tracking-tight">
                                {{ items.data.reduce((sum, item) => sum + parseFloat(item.quantity), 0) }}
                            </div>
                        </div>
                        <div class="p-3 bg-green-500/10 rounded-lg text-green-500 group-hover:bg-green-500 group-hover:text-white transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory List Section -->
             <div>
                <div class="flex justify-between items-end mb-4">
                    <h3 class="text-lg font-bold text-white">Inventory Items</h3>
                    <div class="text-sm text-gray-400">Showing {{ items.from || 0 }} to {{ items.to || 0 }} of {{ items.total }} items</div>
                </div>

                <!-- Main Content Table -->
                <div class="bg-[#010e21] overflow-hidden shadow-xl sm:rounded-lg border border-gray-800">
                    <div class="bg-[#010e21]">
                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead class="bg-[#010e21]">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Item Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Unit</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Stock Level</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-[#010e21] divide-y divide-gray-700">
                                    <tr v-for="item in items.data" :key="item.id" class="hover:bg-gray-700 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link :href="route('items.show', item.id)" class="group flex flex-col">
                                                <div class="text-sm font-medium text-white group-hover:text-blue-400 transition">{{ item.name }}</div>
                                                <div class="text-xs text-gray-500">ID: #{{ item.id }}</div>
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-700 text-gray-300 border border-gray-600">
                                                {{ item.unit }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-bold" 
                                                :class="{
                                                    'text-green-400': Number(item.quantity) >= 10,
                                                    'text-yellow-400': Number(item.quantity) < 10 && Number(item.quantity) > 0,
                                                    'text-red-400': Number(item.quantity) <= 0
                                                }"
                                            >
                                                {{ parseFloat(item.quantity) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                            <button @click="openAddStock(item)" class="text-green-400 hover:text-green-300 bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded-md transition duration-150 border border-green-900" title="Add Stock">
                                                <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Add</span>
                                            </button>
                                            <button @click="openDeductStock(item)" class="text-red-400 hover:text-red-300 bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded-md transition duration-150 border border-red-900" title="Deduct Stock">
                                                <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg> Deduct</span>
                                            </button>
                                            <Link :href="route('items.show', item.id)" class="text-blue-400 hover:text-blue-300 inline-flex items-center ml-2 transition-colors" title="View History">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </Link>
                                        </td>
                                    </tr>
                                     <tr v-if="items.data.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                                <p class="text-gray-400">No inventory items found.</p>
                                                <p class="text-sm mt-1 text-gray-500">Get started by adding a new item.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex justify-between items-center" v-if="items.links && items.links.length > 3">
                     <div class="text-sm text-gray-400">
                        Showing page {{ items.current_page }} of {{ items.last_page }}
                     </div>
                     <div class="flex flex-wrap">
                        <template v-for="(link, key) in items.links" :key="key">
                            <div v-if="link.url === null" 
                                 class="mr-1 mb-1 px-3 py-2 text-sm leading-4 text-gray-600 border border-gray-800 rounded bg-[#010e21]" 
                                 v-html="link.label" 
                            />
                            <Link v-else 
                                  :class="{ 'bg-blue-600 border-blue-600 text-white': link.active, 'text-gray-400 bg-[#010e21] hover:bg-gray-800 border-gray-700': !link.active }" 
                                  class="mr-1 mb-1 px-3 py-2 text-sm leading-4 border rounded focus:border-blue-500 focus:text-blue-500 transition duration-150" 
                                  :href="link.url" 
                                  v-html="link.label" 
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Item Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 overflow-y-auto px-4 py-6 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black opacity-80 transition-opacity" @click="showCreateModal = false"></div>
            <div class="bg-gray-800 rounded-lg shadow-2xl transform transition-all sm:w-full sm:max-w-md z-10 border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center bg-gray-900 rounded-t-lg">
                    <h3 class="text-lg font-medium text-white">Add Inventory Items</h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-white focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="px-6 py-4 space-y-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div v-for="(item, index) in createForm.items" :key="index" class="relative bg-gray-900/50 p-4 rounded-lg border border-gray-700 hover:border-blue-500/50 transition duration-200">
                        <!-- Remove Button -->
                        <button v-if="createForm.items.length > 1" @click="removeItemBlock(index)" class="absolute top-2 right-2 text-gray-500 hover:text-red-400 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Item Name</label>
                                <input type="text" v-model="item.name" class="block w-full rounded-md border-gray-600 bg-gray-900 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3" placeholder="e.g. Steel Rods">
                                <div v-if="createForm.errors[`items.${index}.name`]" class="text-red-400 text-xs mt-1">{{ createForm.errors[`items.${index}.name`] }}</div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Quantity</label>
                                    <input type="number" step="0.001" v-model="item.quantity" class="block w-full rounded-md border-gray-600 bg-gray-900 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3" placeholder="0">
                                    <div v-if="createForm.errors[`items.${index}.quantity`]" class="text-red-400 text-xs mt-1">{{ createForm.errors[`items.${index}.quantity`] }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Unit</label>
                                    <select v-model="item.unit" class="block w-full rounded-md border-gray-600 bg-gray-900 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 px-3">
                                        <option value="" disabled selected>Select Unit</option>
                                        <option value="pcs">pcs (Pieces)</option>
                                        <option value="kg">kg (Kilograms)</option>
                                        <option value="g">g (Grams)</option>
                                        <option value="l">l (Liters)</option>
                                        <option value="ml">ml (Milliliters)</option>
                                        <option value="m">m (Meters)</option>
                                        <option value="cm">cm (Centimeters)</option>
                                        <option value="box">box</option>
                                        <option value="roll">roll</option>
                                        <option value="pack">pack</option>
                                    </select>
                                    <div v-if="createForm.errors[`items.${index}.unit`]" class="text-red-400 text-xs mt-1">{{ createForm.errors[`items.${index}.unit`] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" @click="addItemBlock" class="w-full py-2 border-2 border-dashed border-gray-600 rounded-lg text-gray-400 hover:text-white hover:border-gray-500 hover:bg-gray-800 transition duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Add Another Item Block</span>
                    </button>
                </div>
                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-900 rounded-b-lg border-t border-gray-700 flex justify-end items-center space-x-3">
                    <button @click="showCreateModal = false" class="px-4 py-2 text-gray-400 hover:text-white transition">Cancel</button>
                    <button type="button" @click="submitCreate" :disabled="createForm.processing" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150 shadow-lg">
                        <svg v-if="createForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Save All Items
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Stock Modal -->
        <div v-if="showAddStockModal" class="fixed inset-0 overflow-y-auto px-4 py-6 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black opacity-70 transition-opacity" @click="showAddStockModal = false"></div>
            <div class="bg-gray-800 rounded-lg shadow-2xl transform transition-all sm:w-full sm:max-w-md z-10 border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700 bg-green-900 bg-opacity-20 rounded-t-lg">
                    <h3 class="text-lg font-medium text-green-400">Receive Stock: {{ selectedItem?.name }}</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Quantity to Add ({{ selectedItem?.unit }})</label>
                            <input type="number" step="0.001" v-model="stockForm.quantity" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-900 text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm placeholder-gray-500" placeholder="0.00">
                            <div v-if="stockForm.errors.quantity" class="text-red-400 text-xs mt-1">{{ stockForm.errors.quantity }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Note (Optional)</label>
                            <input type="text" v-model="stockForm.note" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-900 text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm placeholder-gray-500" placeholder="e.g. Purchase Order #123">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showAddStockModal = false" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-md text-sm font-medium text-gray-300 hover:bg-gray-600">Cancel</button>
                        <button type="button" @click="submitAddStock" :disabled="stockForm.processing" class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Confirm Add</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deduct Stock Modal -->
        <div v-if="showDeductStockModal" class="fixed inset-0 overflow-y-auto px-4 py-6 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black opacity-70 transition-opacity" @click="showDeductStockModal = false"></div>
            <div class="bg-gray-800 rounded-lg shadow-2xl transform transition-all sm:w-full sm:max-w-md z-10 border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700 bg-gray-900 rounded-t-lg flex justify-between">
                    <h3 class="text-lg font-medium text-white">Deduct Stock: <span class="text-gray-300">{{ selectedItem?.name }}</span></h3>
                    <button @click="showDeductStockModal = false" class="text-gray-400 hover:text-white focus:outline-none"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Highlited Current Stock Box -->
                    <div class="bg-yellow-900/20 border border-yellow-700/50 rounded-md p-4 flex items-center shadow-inner">
                        <span class="text-yellow-500 font-medium">Current Stock:</span>
                        <span class="ml-2 text-yellow-400 font-bold text-lg">{{ parseFloat(selectedItem?.quantity) }} {{ selectedItem?.unit }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Quantity to Deduct</label>
                        <input type="number" step="0.001" v-model="stockForm.quantity" class="block w-full rounded-md border-gray-600 bg-gray-900 text-white shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm py-2.5 px-3" placeholder="0.00">
                        <div v-if="stockForm.errors.quantity" class="text-red-400 text-xs mt-1">{{ stockForm.errors.quantity }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Reason / Note (Optional)</label>
                        <input type="text" v-model="stockForm.note" class="block w-full rounded-md border-gray-600 bg-gray-900 text-white shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm py-2.5 px-3" placeholder="e.g. Sales, Damaged">
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-900 rounded-b-lg border-t border-gray-700 flex justify-end space-x-3">
                    <button type="button" @click="showDeductStockModal = false" class="text-gray-400 hover:text-white text-sm font-medium">Cancel</button>
                    <button type="button" @click="submitDeductStock" :disabled="stockForm.processing" class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Deduct Stock</button>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

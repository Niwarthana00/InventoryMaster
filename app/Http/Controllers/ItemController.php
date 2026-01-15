<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    protected $inventoryService;

    // Dependency Injection of Service
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of the items.
     */
    public function index(Request $request)
    {
        $query = Item::query();

        // Search Functionality
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Dashboard', [
            'items' => $items,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|distinct|unique:items,name|max:255',
            'items.*.unit' => 'required|string|max:10',
            'items.*.quantity' => 'nullable|numeric|min:0',
        ]);

        foreach ($request->items as $itemData) {
            $this->inventoryService->createItem($itemData, auth()->id());
        }

        return redirect()->back()->with('success', count($request->items) . ' items created successfully.');
    }

    /**
     * Add stock to an item.
     */
    public function addStock(Request $request, Item $item)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.001',
            'note' => 'nullable|string|max:255',
        ]);

        $this->inventoryService->addStock(
            $item,
            $request->quantity,
            $request->note,
            auth()->id()
        );

        return redirect()->back()->with('success', 'Stock added successfully.');
    }

    /**
     * Deduct stock from an item.
     */
    public function deductStock(Request $request, Item $item)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.001',
            'note' => 'nullable|string|max:255',
        ]);

        try {
            $this->inventoryService->deductStock(
                $item,
                $request->quantity,
                $request->note,
                auth()->id()
            );
            return redirect()->back()->with('success', 'Stock deducted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    /**
     * Show item history.
     */
    public function show(Item $item)
    {
        return Inertia::render('Inventory/Show', [
            'item' => $item,
            'transactions' => $item->transactions()->with('user')->get(),
        ]);
    }
}

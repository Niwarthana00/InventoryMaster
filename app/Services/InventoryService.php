<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Create a new item in the inventory.
     */
    public function createItem(array $data)
    {
        return Item::create([
            'name' => $data['name'],
            'unit' => $data['unit'],
            'quantity' => 0, // Initial stock is 0, add via transaction ideally, or set custom
        ]);
    }

    /**
     * Add stock to an item.
     */
    public function addStock(Item $item, float $quantity, ?string $note = null, $userId = null)
    {
        return DB::transaction(function () use ($item, $quantity, $note, $userId) {

            // 1. Update Item Quantity
            $item->quantity += $quantity;
            $item->save();

            // 2. Log Transaction
            return ItemTransaction::create([
                'item_id' => $item->id,
                'user_id' => $userId,
                'type' => 'add',
                'quantity' => $quantity,
                'balance_after' => $item->quantity,
                'note' => $note,
            ]);
        });
    }

    /**
     * Deduct stock from an item.
     */
    public function deductStock(Item $item, float $quantity, ?string $note = null, $userId = null)
    {
        if ($item->quantity < $quantity) {
            throw new Exception("Insufficient stock. Current balance: {$item->quantity} {$item->unit}");
        }

        return DB::transaction(function () use ($item, $quantity, $note, $userId) {

            // 1. Update Item Quantity
            $item->quantity -= $quantity;
            $item->save();

            // 2. Log Transaction
            return ItemTransaction::create([
                'item_id' => $item->id,
                'user_id' => $userId,
                'type' => 'deduct',
                'quantity' => $quantity,
                'balance_after' => $item->quantity,
                'note' => $note,
            ]);
        });
    }
}

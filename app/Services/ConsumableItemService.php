<?php

namespace App\Services;

use App\Models\ConsumableItem;
use Illuminate\Support\Facades\Log;

class ConsumableItemService
{
    /**
     * Get all consumable items
     */
    public function getAllConsumableItems()
    {
        return ConsumableItem::all();
    }

    /**
     * Create a new consumable item
     */
    public function createConsumableItem(array $data)
    {
        try {
            $newItem = ConsumableItem::create([
                'name' => $data['name'],
                'unit' => $data['unit'],
                'quantity' => $data['quantity'],
                'major_id' => $data['major_id'],
            ]);

            return $newItem;
        } catch (\Throwable $th) {
            Log::error('Failed to create consumable item: ' . $th->getMessage());
            throw $th;
        }
    }

    /**
     * Get consumable item by id
     */
    public function getConsumableItemById(ConsumableItem $consumableItem)
    {
        return $consumableItem;
    }

    /**
     * Update consumable item data
     */
    public function updateConsumableItem(ConsumableItem $consumableItem, array $data)
    {
        try {
            $consumableItem->update([
                'name' => $data['name'],
                'unit' => $data['unit'],
                'major_id' => $data['major_id'],
                'quantity' => $data['quantity'],
            ]);

            return $consumableItem;
        } catch (\Throwable $th) {
            Log::error('Failed to update consumable item: ' . $th->getMessage());
            throw $th;
        }
    }

    /**
     * Delete a consumable item
     */
    public function deleteConsumableItem(ConsumableItem $consumableItem)
    {
        try {
            $consumableItem->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error('Failed to delete consumable item: ' . $th->getMessage());
            throw $th;
        }
    }
}


?>
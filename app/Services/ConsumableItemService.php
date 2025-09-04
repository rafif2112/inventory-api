<?php

namespace App\Services;

use App\Models\ConsumableItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConsumableItemService
{
    /**
     * Get all consumable items
     */
    public function getAllConsumableItems($user, $search = '', $sortType = '', $sortQuantity = '', $sortMajor = '')
    {
        $query = ConsumableItem::query()
            ->select('consumable_items.*')
            ->with(['major'])
            ->join('majors', 'consumable_items.major_id', '=', 'majors.id')
            ->when($search, function ($q) use ($search) {
                $q->where('consumable_items.name', 'ilike', "%{$search}%");
            })
            ->when(in_array($sortType, ['asc', 'desc']), function ($q) use ($sortType) {
                $q->orderBy('consumable_items.name', $sortType);
            })
            ->when(in_array($sortQuantity, ['asc', 'desc']), function ($q) use ($sortQuantity) {
                $q->orderBy('consumable_items.quantity', $sortQuantity);
            })
            ->when($user->role === 'superadmin' && in_array($sortMajor, ['asc', 'desc']), function ($q) use ($sortMajor) {
                $q->orderBy('majors.name', $sortMajor);
            })
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('majors.id', $user->major_id);
            });

        return $query->get();
    }

    public function getDataConsumableItems($user, $search = '', $sortType = '', $sortQuantity = '', $sortMajor = '', $perPage = 10)
    {
        $query = ConsumableItem::query()
            ->select('consumable_items.*')
            ->with(['major'])
            ->join('majors', 'consumable_items.major_id', '=', 'majors.id')
            ->when($search, function ($q) use ($search) {
                $q->where('consumable_items.name', 'ilike', "%{$search}%");
            })
            ->when(in_array($sortType, ['asc', 'desc']), function ($q) use ($sortType) {
                $q->orderBy('consumable_items.name', $sortType);
            })
            ->when(in_array($sortQuantity, ['asc', 'desc']), function ($q) use ($sortQuantity) {
                $q->orderBy('consumable_items.quantity', $sortQuantity);
            })
            ->when($user->role === 'superadmin' && in_array($sortMajor, ['asc', 'desc']), function ($q) use ($sortMajor) {
                $q->orderBy('majors.name', $sortMajor);
            })
            ->when($user->role !== 'superadmin', function ($q) use ($user) {
                $q->where('majors.id', $user->major_id);
            });

        return $query->paginate($perPage);
    }

    /**
     * Create a new consumable item
     */
    public function createConsumableItem(array $data)
    {
        try {
            $user = Auth::user();
            $newItem = ConsumableItem::updateOrCreate(
                ['name' => $data['name']],
                [
                    'unit' => $data['unit'],
                    'quantity' => $data['quantity'],
                    'major_id' => $data['major_id'] ?? $user->major_id,
                ]
            );

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
                'name' => $data['name'] ?? $consumableItem->name,
                'unit' => $data['unit'] ?? $consumableItem->unit,
                'major_id' => $data['major_id'] ?? $consumableItem->major_id,
                'quantity' => $data['quantity'] ?? $consumableItem->quantity,
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
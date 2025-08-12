<?php

namespace App\Services;

use App\Models\ConsumableItem;
use App\Models\ConsumableLoan;
use Illuminate\Support\Facades\Log;

class ConsumableLoanService
{
    public function getAllConsumableLoans()
    {
        return ConsumableLoan::all();
    }

    public function createConsumableLoan(array $data)
    {
        try {
            $newLoan = ConsumableLoan::create([
                'student_id' => $data['student_id'],
                'teacher_id' => $data['teacher_id'],
                'consumable_item_id' => $data['consumable_item_id'],
                'quantity' => $data['quantity'],
                'purpose' => $data['purpose'],
                'borrowed_by' => $data['borrowed_by'],
                'borrowed_at' => $data['borrowed_at'],
            ]);

            $consumableItem = ConsumableItem::findOrFail($data['consumable_item_id']);
            $consumableItem->decrement('quantity', $data['quantity']);

            return $newLoan;
        } catch (\Throwable $th) {
            Log::error('Failed to create consumable loan: ' . $th->getMessage());
            throw $th;
        }
    }
    public function getConsumableLoanById(ConsumableLoan $consumableLoan)
    {
        return $consumableLoan;
    }

    public function updateConsumableLoan(ConsumableLoan $consumableLoan, array $data)
    {
        try {
            $consumableLoan->update([
                'student_id' => $data['student_id'] ?? $consumableLoan->student_id,
                'teacher_id' => $data['teacher_id'] ?? $consumableLoan->teacher_id,
                'consumable_item_id' => $data['consumable_item_id'] ?? $consumableLoan->consumable_item_id,
                'quantity' => $data['quantity'] ?? $consumableLoan->quantity,
                'purpose' => $data['purpose'] ?? $consumableLoan->purpose,
                'borrowed_by' => $data['borrowed_by'] ?? $consumableLoan->borrowed_by,
                'borrowed_at' => $data['borrowed_at'] ?? $consumableLoan->borrowed_at,
            ]);

            return $consumableLoan;
        } catch (\Throwable $th) {
            Log::error('Failed to update consumable loan: ' . $th->getMessage());
            throw $th;
        }
    }

    public function deleteConsumableLoan(ConsumableLoan $consumableLoan)
    {
        try {
            $consumableLoan->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error('Failed to delete consumable loan: ' . $th->getMessage());
            throw $th;
        }
    }
}
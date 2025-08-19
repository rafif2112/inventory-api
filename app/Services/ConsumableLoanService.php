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
            $consumableItem = ConsumableItem::findOrFail($data['consumable_item_id']);

            if ($consumableItem->quantity < $data['quantity']) {
                throw new \Exception('Insufficient quantity available');
            }

            $newLoan = ConsumableLoan::create([
                'student_id' => $data['student_id'] ?? null,
                'teacher_id' => $data['teacher_id'] ?? null,
                'consumable_item_id' => $data['consumable_item_id'] ?? null,
                'quantity' => $data['quantity'] ?? null,
                'purpose' => $data['purpose'] ?? null,
                'borrowed_by' => $data['borrowed_by'] ?? null,
                'borrowed_at' => $data['borrowed_at'] ?? null,
            ]);

            $consumableItem->decrement('quantity', $data['quantity']);

            return $newLoan;
        } catch (\Throwable $th) {
            Log::error('Failed to create consumable loan: ' . $th->getMessage());
            throw $th;
        }
    }

    public function getConsumableLoanHistory($search, $sortQuantity, $sortType, $perPage = 10)
    {
        $query = ConsumableLoan::query()
            ->select('consumable_loans.*', 'consumable_items.name')
            ->with(['student', 'student.major', 'teacher', 'consumableItem'])
            ->join('consumable_items', 'consumable_loans.consumable_item_id', '=', 'consumable_items.id')
            ->leftJoin('students', 'consumable_loans.student_id', '=', 'students.id')
            ->leftJoin('teachers', 'consumable_loans.teacher_id', '=', 'teachers.id')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('students.name', 'ilike', "%{$search}%")
                        ->orWhere('teachers.name', 'ilike', "%{$search}%")
                        ->orWhere('consumable_items.name', 'ilike', "%{$search}%");
                });
            })
            ->when($sortType, function ($query, $sortType) {
                $query->orderBy('consumable_items.name', $sortType);
            })
            ->when($sortQuantity, function ($query, $sortQuantity) {
                $query->orderBy('quantity', $sortQuantity);
            });

        return $query->paginate($perPage);
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

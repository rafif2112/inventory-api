<?php

namespace App\Services;

use App\Models\UnitItem;
use App\Models\UnitLoan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnitLoanService
{
    /**
     * Get all unit loans with relationships
     */
    public function getAllUnitLoans()
    {
        return UnitLoan::with(['student', 'teacher', 'unitItem', 'unitItem.subItem', 'unitItem.subItem.item'])
            ->orderBy('borrowed_at', 'desc')
            ->get();
    }

    /**
     * Get unit loan by unit item code
     */
    public function getLoanByUnitCode(string $codeUnit)
    {
        $unitItem = UnitItem::with(['subItem', 'subItem.item'])->where('code_unit', $codeUnit)->first();
        if (!$unitItem) {
            return [
                'found' => false,
                'data' => null,
                'message' => 'Unit item not found'
            ];
        }

        $unitLoan = UnitLoan::where('unit_item_id', $unitItem->id)
            ->where('status', true)
            ->whereNull('returned_at')
            ->with(['student', 'teacher', 'unitItem'])
            ->latest('borrowed_at')
            ->first();

        return [
            'found' => true,
            'data' => $unitLoan ?: $unitItem,
            'is_borrowed' => $unitLoan !== null
        ];
    }

    /**
     * Create a new unit loan
     */
    public function createUnitLoan(array $data, Request $request)
    {
        try {
            $unitItem = UnitItem::findOrFail($data['unit_item_id']);

            if ($unitItem->status === false) {
                throw new \Exception('Unit item is not available for loan');
            }

            if ($unitItem->condition === false) {
                throw new \Exception('Unit item is damaged and cannot be loaned');
            }

            return DB::transaction(function () use ($data, $unitItem, $request) {
                $loanData = [
                    'student_id' => $data['student_id'] ?? null,
                    'teacher_id' => $data['teacher_id'] ?? null,
                    'unit_item_id' => $data['unit_item_id'],
                    'borrowed_by' => $data['borrowed_by'],
                    'borrowed_at' => $data['borrowed_at'],
                    'purpose' => $data['purpose'],
                    'room' => $data['room'],
                    'status' => true,
                    'guarantee' => $data['guarantee'],
                ];

                $unitLoan = UnitLoan::create($loanData);

                if ($request && $request->image) {
                    if ($unitLoan->image) {
                        Storage::disk('local')->delete($unitLoan->image);
                    }

                    $file = $request->image;
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = 'unit-loan';

                    Storage::disk('public')->makeDirectory($path);
                    $relativePath = $path . '/' . $filename;
                    Storage::disk('public')->put($relativePath, file_get_contents($file));

                    $unitLoan->image = $relativePath;
                    $unitLoan->save();
                }

                $unitItem->status = false;
                $unitItem->save();

                return $unitLoan;
            });
        } catch (\Throwable $th) {
            Log::error('Failed to create unit loan: ' . $th->getMessage());
            throw $th;
        }
    }

    /**
     * Get unit loan by ID
     */
    public function getUnitLoanById($id)
    {
        return UnitLoan::with(['student', 'student.major', 'teacher', 'unitItem', 'unitItem.subItem', 'unitItem.subItem.item'])->find($id);
    }

    /**
     * Update unit loan
     */
    public function updateUnitLoan(UnitLoan $unitLoan, array $data, Request $request)
    {
        try {

            $unitItem = UnitItem::findOrFail($unitLoan->unit_item_id);

            $updateData = [
                'student_id' => $data['student_id'] ?? $unitLoan->student_id,
                'teacher_id' => $data['teacher_id'] ?? $unitLoan->teacher_id,   
                'borrowed_by' => $data['borrowed_by'] ?? $unitLoan->borrowed_by,
                'borrowed_at' => $data['borrowed_at'] ?? $unitLoan->borrowed_at,
                'returned_at' => $data['returned_at'] ?? $unitLoan->returned_at,
                'purpose' => $data['purpose'] ?? $unitLoan->purpose,
                'room' => $data['room'] ?? $unitLoan->room,
                'status' => $data['returned_at'] ? false : $data['status'] ?? $unitLoan->status,
                'guarantee' => $data['guarantee'] ?? $unitLoan->guarantee,
            ];

            $unitLoan->update($updateData);

            if ($request && $request->image) {
                if ($unitLoan->image) {
                    Storage::disk('local')->delete($unitLoan->image);
                }

                $file = $request->image;
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = 'unit-loan';

                Storage::disk('public')->makeDirectory($path);
                $relativePath = $path . '/' . $filename;
                Storage::disk('public')->put($relativePath, file_get_contents($file));

                $unitLoan->image = $relativePath;
                $unitLoan->save();
            }

            if ($data['returned_at']) {
                $unitItem->status = true;
                $unitItem->save();
            }

            return $unitLoan;
        } catch (\Throwable $th) {
            Log::error('Failed to update unit loan: ' . $th->getMessage());
            throw $th;
        }
    }

    /**
     * Delete unit loan
     */
    public function deleteUnitLoan(UnitLoan $unitLoan)
    {
        try {
            $unitLoan->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error('Failed to delete unit loan: ' . $th->getMessage());
            throw $th;
        }
    }

    public function getLoanHistory($sortTime, $sortType, $search, $data)
    {
        $query = UnitLoan::with(['unitItem', 'student', 'student.major', 'teacher', 'unitItem.subItem', 'unitItem.subItem.item'])
            ->orderBy('borrowed_at', $sortTime === 'asc' ? 'asc' : 'desc')
            ->when($data === 'returning', function ($query) {
                return $query->where('status', false);
            }, function ($query) {
                return $query->where('status', true);
            });

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('unitItem.subItem.item', function ($subQuery) use ($search) {
                    $subQuery->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                    ->orWhereHas('student', function ($subQuery) use ($search) {
                        $subQuery->whereRaw('LOWER(name) LIKE ? OR nis::text LIKE ?', [
                            '%' . strtolower($search) . '%',
                            '%' . $search . '%'
                        ]);
                    })
                    ->orWhereHas('teacher', function ($subQuery) use ($search) {
                        $subQuery->whereRaw('nip::text LIKE ? OR LOWER(name) LIKE ?', [
                            '%' . $search . '%',
                            '%' . strtolower($search) . '%'
                        ]);
                    })
                    ->orWhereHas('unitItem', function ($subQuery) use ($search) {
                        $subQuery->whereRaw('LOWER(code_unit) LIKE ?', ['%' . strtolower($search) . '%']);
                    })
                    ->orWhereHas('unitItem.subItem', function ($subQuery) use ($search) {
                        $subQuery->whereRaw('LOWER(merk) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
            });
        }

        $loans = $query->get();

        if (!empty($sortType)) {
            $loans = $loans->sortBy(function ($loan) {
                return strtolower($loan->unitItem->subItem->item->name ?? '');
            });
            if (strtolower($sortType) === 'desc') {
                $loans = $loans->reverse();
            }
            $loans = $loans->values();
        }

        return $loans;
    }
}

<?php

namespace App\Http\Resources\Superadmin;

use App\Http\Resources\ConsumableItemResource;
use App\Http\Resources\MajorResource;
use App\Http\Resources\UnitLoanResource;
use App\Services\ConsumableLoanService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountTotalLoansResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fromDate = $request->query('from', now()->startOfYear()->toDateString());
        $toDate   = $request->query('to', now()->endOfYear()->toDateString());

        if ($fromDate > $toDate) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $consumableCount = $this->consumableLoans
            ->whereBetween('borrowed_at', [$fromDate, $toDate])
            ->count();

        $unitCount = $this->subItems
            ->sum(
                fn($sub) => $sub->unitLoans
                    ->whereBetween('borrowed_at', [$fromDate, $toDate])
                    ->count()
            );

        $grandTotal = $consumableCount + $unitCount;

        return [
            'id' => $this->id,
            'major' => $this->name,
            'count' => $grandTotal,
        ];
    }
}

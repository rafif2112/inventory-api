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
        $arr = [
            'id' => $this->id,
            'major' => $this->name,
            'count' => $this->consumableLoans->count()
                + $this->subItems->sum(fn($sub) => $sub->unitLoans->count()),
        ];

        return $arr;
    }
}

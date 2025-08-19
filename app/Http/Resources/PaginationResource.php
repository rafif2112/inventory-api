<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        if (is_object($this->resource) && method_exists($this->resource, 'currentPage')) {
            return [
                'current_page' => $this->currentPage(),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ];
        }
        
        return [
            'current_page' => $this->resource['current_page'] ?? 1,
            'from' => $this->resource['from'] ?? 0,
            'last_page' => $this->resource['last_page'] ?? 1,
            'per_page' => $this->resource['per_page'] ?? 10,
            'to' => $this->resource['to'] ?? 0,
            'total' => $this->resource['total'] ?? 0,
        ];

        return $arr;
    }
}

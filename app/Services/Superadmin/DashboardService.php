<?php

namespace App\Services\Superadmin;

use App\Models\Item;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    // GET Items Summary
    public function getItemsSummary()
    {
        return Item::select('items.name', DB::raw('COUNT(unit_loans.id) as total_borrowed'))
            ->join('sub_items', 'sub_items.item_id', '=', 'items.id')
            ->join('unit_items', 'unit_items.sub_item_id', '=', 'sub_items.id')
            ->join('unit_loans', 'unit_loans.unit_item_id', '=', 'unit_items.id')
            ->where('unit_loans.status', 1)
            ->groupBy('items.id', 'items.name')
            ->limit(5)
            ->orderByDesc('total_borrowed')
            ->get();
    }

    public function getAverageBorrowing()
    {
        // Ambil total peminjaman per kategori
        $borrowings = Item::select(
                'items.id',
                'items.name',
                DB::raw('COUNT(unit_loans.id) as borrowed')
            )
            ->join('sub_items', 'sub_items.item_id', '=', 'items.id')
            ->join('unit_items', 'unit_items.sub_item_id', '=', 'sub_items.id')
            ->join('unit_loans', 'unit_loans.unit_item_id', '=', 'unit_items.id')
            ->where('unit_loans.status', 1)
            ->groupBy('items.id', 'items.name')
            ->get();

        // Hitung total semua peminjaman
        $totalAll = $borrowings->sum('borrowed');

        // Hitung rata-rata peminjaman
        $average = $borrowings->count() > 0 ? $totalAll / $borrowings->count() : 0;

        // Tambahin persentase ke setiap kategori
        $result = $borrowings->map(function ($item) use ($average) {
            $item->persen = $average > 0 ? round(($item->total_borrowed / $average) * 100, 2) : 0;
            return $item;
        });

        return $result;
    }

}
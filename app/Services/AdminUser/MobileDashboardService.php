<?php

namespace App\Services\AdminUser;

use App\Models\ConsumableItem;
use App\Models\ConsumableLoan;
use App\Models\UnitItem;
use App\Models\UnitLoan;

class MobileDashboardService
{
    public function getCardData($user)
    {
        $unitItemQuery = UnitItem::whereHas('subItem', function ($query) use ($user) {
            $query->where('major_id', $user->major_id);
        });

        $consumableItemQuery = ConsumableItem::where('major_id', $user->major_id);

        $unitItem = $unitItemQuery->count();
        $consumableItem = $consumableItemQuery->count();

        $goodItem = $unitItemQuery->where('condition', true)->count();
        $badItem = $unitItemQuery->where('condition', false)->count();

        $monthlyData = UnitLoan::whereHas('unitItem.subItem', function ($query) use ($user) {
            $query->where('major_id', $user->major_id);
        })->whereMonth('borrowed_at', now()->month)->count();

        $monthlyDataConsumable = ConsumableLoan::whereHas('consumableItem', function ($query) use ($user) {
            $query->where('major_id', $user->major_id);
        })->whereMonth('borrowed_at', now()->month)->count();

        $dailyData = UnitLoan::whereHas('unitItem.subItem', function ($query) use ($user) {
            $query->where('major_id', $user->major_id);
        })->whereDay('created_at', now()->day)->count();

        $dailyDataConsumable = ConsumableLoan::whereHas('consumableItem', function ($query) use ($user) {
            $query->where('major_id', $user->major_id);
        })->whereDay('created_at', now()->day)->count();

        return [
            'reuse' => $unitItem,
            'used' => $consumableItem,
            'good' => $goodItem,
            'down' => $badItem,
            'daily_data' => $dailyData + $dailyDataConsumable,
            'monthly_data' => $monthlyData + $monthlyDataConsumable
        ];
    }

    public function getLatestActivity($user)
    {
        $latestLoans = UnitLoan::with('unitItem.subItem', 'student', 'teacher', 'unitItem.subItem.item')
            ->whereHas('unitItem.subItem', function ($query) use ($user) {
                $query->where('major_id', $user->major_id);
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'item' => $loan->unitItem->subItem->item->name,
                    'sub_item' => $loan->unitItem->subItem->merk,
                    'borrowed_at' => $loan->borrowed_at,
                    'borrower_name' => $loan->student->name ? $loan->student->name : ($loan->teacher->name ? $loan->teacher->name : ''),
                ];
            });

        return $latestLoans;
    }
}

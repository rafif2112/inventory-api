<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'sub_item_id',
        'code_unit',
        'description',
        'procurement_date',
        'status',
        'condition',
        'barcode',
    ];

    public function subItem()
    {
        return $this->belongsTo(SubItem::class, 'sub_item_id');
    }
}

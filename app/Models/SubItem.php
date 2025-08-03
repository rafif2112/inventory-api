<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'item_id',
        'merk',
        'stock',
        'unit',
        'major_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function unitItems()
    {
        return $this->hasMany(UnitItem::class, 'sub_item_id');
    }
}

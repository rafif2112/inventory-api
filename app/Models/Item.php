<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
    ];

    public function subItems()
    {
        return $this->hasMany(SubItem::class, 'item_id');
    }

    public function unitItems()
    {
        return $this->hasOne(UnitItem::class, 'item_id');
    }
}

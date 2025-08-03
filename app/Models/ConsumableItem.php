<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'quantity',
        'unit',
        'major_id',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }
    
    public function consumableLoans()
    {
        return $this->hasMany(ConsumableLoan::class, 'consumable_item_id');
    }
}

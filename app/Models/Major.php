<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'color',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'major_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'major_id');
    }

    public function subItems()
    {
        return $this->hasMany(SubItem::class, 'major_id');
    }

    public function unitItems()
    {
        return $this->hasManyThrough(UnitItem::class, SubItem::class, 'major_id', 'sub_item_id');
    }

    public function unitLoans()
    {
        return $this->hasManyThrough(UnitLoan::class, SubItem::class, 'major_id', 'unit_item_id');
    }

    public function consumableLoans()
    {
        return $this->hasManyThrough(ConsumableLoan::class, ConsumableItem::class, 'major_id', 'consumable_item_id');
    }
}

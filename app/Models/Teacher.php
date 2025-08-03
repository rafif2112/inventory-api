<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
    ];

    public function consumableLoans()
    {
        return $this->hasMany(ConsumableLoan::class, 'teacher_id');
    }

    public function unitLoans()
    {
        return $this->hasMany(UnitLoan::class, 'teacher_id');
    }
}

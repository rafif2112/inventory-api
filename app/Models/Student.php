<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'nis',
        // 'rombel',
        'rayon',
        'major_id',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function consumableLoans()
    {
        return $this->hasMany(ConsumableLoan::class, 'student_id');
    }

    public function unitLoans()
    {
        return $this->hasMany(UnitLoan::class, 'student_id');
    }
}

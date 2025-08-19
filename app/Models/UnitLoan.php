<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitLoan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'unit_item_id',
        'borrowed_by',
        'borrowed_at',
        'returned_at',
        'quantity',
        'room',
        'purpose',
        'status',
        'image',
        'guarantee',
    ];

    public function unitItem()
    {
        return $this->belongsTo(UnitItem::class, 'unit_item_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }   
}

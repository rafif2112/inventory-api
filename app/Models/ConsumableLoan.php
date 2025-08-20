<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableLoan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'consumable_item_id',
        'quantity',
        'purpose',
        'borrowed_by',
        'borrowed_at',
    ];

    public function consumableItem()
    {
        return $this->belongsTo(ConsumableItem::class, 'consumable_item_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function major()
    {
        return $this->hasOneThrough(Major::class, ConsumableItem::class, 'consumable_item_id', 'major_id');
    }
}

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
}

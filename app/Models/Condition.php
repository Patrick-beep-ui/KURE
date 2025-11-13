<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = [
        'condition_name',
        'condition_type',
        'condition_description'
    ];

    public function studentConditions(): HasMany
    {
        return $this->hasMany(StudentCondition::class);
    }
}

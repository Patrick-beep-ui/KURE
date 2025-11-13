<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'condition_id',
        'start_date',
        'end_date',
        'notes',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    public function controlledMedications(): HasMany
    {
        return $this->hasMany(ControlledMedication::class);
    }
}

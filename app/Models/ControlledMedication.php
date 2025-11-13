<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlledMedication extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_condition_id',
        'schedule',
        'duration',
        'medication_id',
    ];

    public function studentCondition(): BelongsTo
    {
        return $this->belongsTo(StudentCondition::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}

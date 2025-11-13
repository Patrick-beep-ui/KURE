<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'start_time',
        'end_time',
        'reason',
        'diagnosis',
        'notes',
        'provided_by',
        'type',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provided_by');
    }

    public function consultationMedications(): HasMany
    {
        return $this->hasMany(ConsultationMedication::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}

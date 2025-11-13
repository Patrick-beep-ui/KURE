<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'ku_id',
        'first_name',
        'last_name',
        'ku_email',
        'dob',
        'department',
        'address',
        'program_id',
        'residence',
        'profile_pic_url',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function contactNumbers(): HasMany
    {
        return $this->hasMany(ContactNumber::class);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(StudentCondition::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}

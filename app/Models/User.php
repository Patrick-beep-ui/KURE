<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'ku_id',
        'email',
        'role',
    ];

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'provided_by');
    }

    public function scheduledAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'scheduled_by');
    }
}

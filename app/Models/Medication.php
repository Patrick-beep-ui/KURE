<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dosage',
        'description',
        'unit',
        'notes',
        'stock',
        'controlled',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function consultationMedications(): HasMany
    {
        return $this->hasMany(ConsultationMedication::class);
    }

    public function controlledMedications(): HasMany
    {
        return $this->hasMany(ControlledMedication::class);
    }
}

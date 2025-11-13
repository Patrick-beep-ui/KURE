<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id',
        'quantity',
        'location',
        'batch_number',
        'expiration_date',
    ];

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}

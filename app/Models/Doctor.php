<?php

namespace App\Models;

use App\Model\User;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{
    protected $fillable = [
        'specialty_id',
        'name',
        'crm',
        'phone',
        'work_start_time',
        'work_end_time',
        'slot_duration',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'work_start_time' => 'datetime:H:i',
            'work_end_time' => 'datetime:H:i',
            'slot_duration' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}

<?php

namespace App\Models;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
}

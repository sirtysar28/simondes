<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    protected $fillable = ['code', 'name', 'district', 'year', 'budget'];

    protected $casts = [
        'year' => 'integer',
        'budget' => 'integer',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(FundTransaction::class);
    }

    public function getSpentAttribute(): int
    {
        return (int) $this->transactions()->sum('amount');
    }

    public function getRemainingAttribute(): int
    {
        return $this->budget - $this->spent;
    }

    public function getAbsorptionAttribute(): int
    {
        return $this->budget > 0 ? (int) round(100 * $this->spent / $this->budget) : 0;
    }

    public function getStatusAttribute(): array
    {
        if ($this->remaining < 0) {
            return ['Melebihi pagu', 'low'];
        }
        if ($this->absorption >= 75) {
            return ['Tinggi', 'ok'];
        }
        if ($this->absorption >= 40) {
            return ['Sedang', 'mid'];
        }

        return ['Rendah', 'low'];
    }
}

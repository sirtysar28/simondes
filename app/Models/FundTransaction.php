<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundTransaction extends Model
{
    protected $fillable = [
        'village_id', 'date', 'category', 'spending_type', 'funding_source',
        'description', 'amount', 'vendor', 'receipt_number', 'evidence_url', 'user_id',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'amount' => 'integer',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

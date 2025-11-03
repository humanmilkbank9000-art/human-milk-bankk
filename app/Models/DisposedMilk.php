<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DisposedMilk extends Model
{
    protected $table = 'disposed_milk';
    protected $primaryKey = 'disposed_id';

    protected $fillable = [
        'source_donation_id',
        'source_batch_id',
        'volume_disposed',
        'date_disposed',
        'time_disposed',
        'admin_id',
        'notes',
        'bag_indices',
    ];

    protected $casts = [
        'date_disposed' => 'date',
        'volume_disposed' => 'decimal:2',
        'bag_indices' => 'array',
    ];

    public function sourceDonation(): BelongsTo
    {
        return $this->belongsTo(Donation::class, 'source_donation_id', 'breastmilk_donation_id');
    }

    public function sourceBatch(): BelongsTo
    {
        return $this->belongsTo(PasteurizationBatch::class, 'source_batch_id', 'batch_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function getFormattedDateAttribute(): string
    {
        if (!$this->date_disposed) return '-';
        return Carbon::parse($this->date_disposed)->format('M d, Y');
    }

    public function getFormattedTimeAttribute(): string
    {
        if (!$this->time_disposed) return '-';
        try {
            return Carbon::parse($this->time_disposed)->format('g:i A');
        } catch (\Exception $e) {
            return (string)$this->time_disposed;
        }
    }

    public function getFormattedVolumeDisposedAttribute()
    {
        $vol = (float) $this->volume_disposed;
        return $vol == (int)$vol ? (int)$vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
    }
}

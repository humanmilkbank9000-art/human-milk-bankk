<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PasteurizationBatch extends Model
{
    protected $table = 'pasteurization_batches';
    protected $primaryKey = 'batch_id';
    
    protected $fillable = [
        'batch_number',
        'total_volume',
        'available_volume',
        'date_pasteurized',
        'time_pasteurized',
        'admin_id',
        'status',
        'notes'
    ];

    protected $casts = [
        'date_pasteurized' => 'date',
        'time_pasteurized' => 'datetime:H:i',
        'total_volume' => 'decimal:2',
        'available_volume' => 'decimal:2'
    ];

    // Relationships
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'pasteurization_batch_id', 'batch_id');
    }

    public function dispensedMilk(): BelongsToMany
    {
        return $this->belongsToMany(DispensedMilk::class, 'dispensed_milk_sources', 'source_id', 'dispensed_id')
            ->where('source_type', 'pasteurized')
            ->withPivot('volume_used')
            ->withTimestamps();
    }

    // Utility methods
    public function getFormattedDateAttribute(): string
    {
        return $this->date_pasteurized->format('M d, Y');
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->time_pasteurized->format('g:i A');
    }

    // Accessor to ensure batch_number is always present
    public function getBatchNumberAttribute($value): string
    {
        // If batch_number is null or empty, generate one based on batch_id
        if (empty($value)) {
            return 'BATCH-' . str_pad($this->batch_id, 3, '0', STR_PAD_LEFT);
        }
        return $value;
    }

    // Accessor for simple batch name display
    public function getSimpleBatchNameAttribute(): string
    {
        return $this->batch_number;
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->available_volume > 0;
    }

    public function isDepleted(): bool
    {
        return $this->status === 'depleted' || $this->available_volume <= 0;
    }

    public function getFormattedTotalVolumeAttribute()
    {
        $vol = (float) $this->total_volume;
        return $vol == (int)$vol ? (int)$vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
    }

    public function getFormattedAvailableVolumeAttribute()
    {
        $vol = (float) $this->available_volume;
        return $vol == (int)$vol ? (int)$vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
    }

    // Generate next batch number
    public static function generateBatchNumber(): string
    {
        $lastBatch = self::orderBy('batch_id', 'desc')->first();
        $nextNumber = $lastBatch ? intval(substr($lastBatch->batch_number, -3)) + 1 : 1;
        return 'BATCH-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Reduce available volume when dispensing
    public function reduceVolume(float $amount): bool
    {
        if ($this->available_volume >= $amount) {
            $newVolume = $this->available_volume - $amount;
            
            // Mark as depleted if no volume left
            if ($newVolume <= 0) {
                $this->status = 'depleted';
                $this->attributes['available_volume'] = 0;
            } else {
                // Remove .00 from whole numbers
                $this->attributes['available_volume'] = (float)$newVolume == (int)$newVolume ? (int)$newVolume : rtrim(rtrim(number_format($newVolume, 2, '.', ''), '0'), '.');
            }
            
            return $this->save();
        }
        
        return false;
    }

    /*
     |--------------------------------------------------------------------------
     | Scopes for inventory filtering (expiry-aware)
     |--------------------------------------------------------------------------
     | Pasteurized breastmilk expires 1 year after the date_pasteurized. The
     | requirement is: ON the day it expires it should disappear from the active
     | pasteurized tab and appear in the expired tab. That means equality is
     | considered expired (<= today after adding one year).
     */

    /**
     * Active (non-expired) batches with available volume.
     * Logic: status = active, available_volume > 0, date_pasteurized > (today - 1 year)
     */
    public function scopeNonExpiredActive($query)
    {
        // A batch is expired if date_pasteurized <= today - 1 year
        $oneYearAgo = now()->subYear()->toDateString();
        return $query->where('status', 'active')
            ->where('available_volume', '>', 0)
            ->whereDate('date_pasteurized', '>', $oneYearAgo);
    }

    /**
     * Expired batches (still having available volume) that should move to the expired tab.
     * Expired when date_pasteurized <= today - 1 year.
     */
    public function scopeExpiredActive($query)
    {
        $oneYearAgo = now()->subYear()->toDateString();
        return $query->where('status', 'active')
            ->where('available_volume', '>', 0)
            ->whereDate('date_pasteurized', '<=', $oneYearAgo);
    }
}

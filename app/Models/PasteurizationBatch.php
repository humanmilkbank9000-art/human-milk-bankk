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

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->available_volume > 0;
    }

    public function isDepleted(): bool
    {
        return $this->status === 'depleted' || $this->available_volume <= 0;
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
            $this->available_volume -= $amount;
            
            // Mark as depleted if no volume left
            if ($this->available_volume <= 0) {
                $this->status = 'depleted';
                $this->available_volume = 0;
            }
            
            return $this->save();
        }
        
        return false;
    }
}

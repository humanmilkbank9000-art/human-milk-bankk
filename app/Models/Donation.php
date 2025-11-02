<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Donation extends Model
{
    use SoftDeletes;
    protected $table = 'breastmilk_donation';
    protected $primaryKey = 'breastmilk_donation_id';

    protected $fillable = [
        'health_screening_id',
        'admin_id',
        'user_id',
        'donation_method',
        'status',
        'number_of_bags',
        'individual_bag_volumes',
        'total_volume',
        'dispensed_volume',
        'available_volume',
        'donation_date',
        'donation_time',
        'scheduled_pickup_date',
        'scheduled_pickup_time',
        'availability_id',
        // Inventory tracking fields
        'pasteurization_status',
        'pasteurization_batch_id',
        'added_to_inventory_at',
        'expiration_date',
        // Home collection fields
        'first_expression_date',
        'last_expression_date',
        'latitude',
        'longitude',
        'bag_details',
        // Lifestyle/health screening questions
        'good_health',
        'no_smoking',
        'no_medication',
        'no_alcohol',
        'no_fever',
        'no_cough_colds',
        'no_breast_infection',
        'followed_hygiene',
        'followed_labeling',
        'followed_storage',
        // Decline tracking
        'decline_reason',
        'declined_at',
    ];

    protected $casts = [
        'donation_date' => 'date',
        'scheduled_pickup_date' => 'date',
        'first_expression_date' => 'date',
        'last_expression_date' => 'date',
        'individual_bag_volumes' => 'array',
        'bag_details' => 'array',
        'total_volume' => 'decimal:2',
        'dispensed_volume' => 'decimal:2',
        'available_volume' => 'decimal:2',
        'added_to_inventory_at' => 'datetime',
        'expiration_date' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'declined_at' => 'datetime',
    ];

    // Relationships
    public function availability()
    {
        return $this->belongsTo(\App\Models\Availability::class, 'availability_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'user_id');
    }

    public function healthScreening()
    {
        return $this->belongsTo(\App\Models\HealthScreening::class, 'health_screening_id', 'health_screening_id');
    }

    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id', 'admin_id');
    }

    public function pasteurizationBatch()
    {
        return $this->belongsTo(\App\Models\PasteurizationBatch::class, 'pasteurization_batch_id', 'batch_id');
    }

    public function dispensedMilk(): BelongsToMany
    {
        return $this->belongsToMany(DispensedMilk::class, 'dispensed_milk_sources', 'source_id', 'dispensed_id')
            ->where('source_type', 'unpasteurized')
            ->withPivot('volume_used')
            ->withTimestamps();
    }

    // Status check methods
    public function isPendingWalkIn()
    {
        return $this->status === 'pending_walk_in';
    }

    public function isPendingHomeCollection()
    {
        return $this->status === 'pending_home_collection';
    }

    public function isScheduledHomeCollection()
    {
        return $this->status === 'scheduled_home_collection';
    }

    // Scopes for admin filtering
    public function scopePendingWalkIn($query)
    {
        return $query->where('status', 'pending_walk_in')->where('donation_method', 'walk_in');
    }

    public function scopeSuccessWalkIn($query)
    {
        return $query->where('status', 'success_walk_in')->where('donation_method', 'walk_in');
    }

    public function scopePendingHomeCollection($query)
    {
        return $query->where('status', 'pending_home_collection')->where('donation_method', 'home_collection');
    }

    public function scopeScheduledHomeCollection($query)
    {
        return $query->where('status', 'scheduled_home_collection')->where('donation_method', 'home_collection');
    }

    public function scopeSuccessHomeCollection($query)
    {
        return $query->where('status', 'success_home_collection')->where('donation_method', 'home_collection');
    }

    // Helper methods for individual bag volumes
    public function getBagVolumesAttribute()
    {
        return $this->individual_bag_volumes ?? [];
    }

    public function setBagVolumes(array $volumes)
    {
        $this->individual_bag_volumes = $volumes;
        $this->number_of_bags = count($volumes);
        $total = array_sum($volumes);
        // Set total_volume only (original donation amount - IMMUTABLE)
        // available_volume will be initialized from total_volume when added to inventory
        // Remove .00 from whole numbers
        $this->attributes['total_volume'] = (float)$total == (int)$total ? (int)$total : rtrim(rtrim(number_format($total, 2, '.', ''), '0'), '.');
    }

    public function getFormattedBagVolumesAttribute()
    {
        if (empty($this->individual_bag_volumes)) {
            return '-';
        }
        return implode(', ', array_map(function($vol) {
            // Remove .00 from whole numbers
            $formatted = (float)$vol == (int)$vol ? (int)$vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
            return $formatted . 'ml';
        }, $this->individual_bag_volumes));
    }

    public function getAverageVolumePerBagAttribute()
    {
        if (empty($this->individual_bag_volumes)) {
            return 0;
        }
        return round(array_sum($this->individual_bag_volumes) / count($this->individual_bag_volumes), 2);
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

    public function getFormattedDispensedVolumeAttribute()
    {
        $vol = (float) ($this->dispensed_volume ?? 0);
        return $vol == (int)$vol ? (int)$vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
    }

    // Inventory management methods
    public function isInInventory(): bool
    {
        // A donation is "in inventory" if it's a successful donation and unpasteurized
        // It shows in inventory regardless of whether it has available volume or not
        return in_array($this->status, ['success_walk_in', 'success_home_collection']) && 
            $this->pasteurization_status === 'unpasteurized';
    }

    public function isUnpasteurized(): bool
    {
        return $this->pasteurization_status === 'unpasteurized';
    }

    public function isPasteurized(): bool
    {
        return $this->pasteurization_status === 'pasteurized';
    }

    public function isDepleted(): bool
    {
        // A donation is depleted if available_volume is 0 or less
        return $this->available_volume <= 0;
    }

    public function addToInventory(): bool
    {
        // If already in inventory with positive available_volume, do nothing
        if ($this->pasteurization_status === 'unpasteurized' && !is_null($this->available_volume) && $this->available_volume > 0) {
            return false; // Already in inventory with usable volume
        }

        // Initialize available_volume from total_volume if missing or zero
        if (is_null($this->available_volume) || $this->available_volume == 0) {
            $this->available_volume = $this->total_volume ?? 0.0;
        }

        // Initialize dispensed_volume if not set
        if (is_null($this->dispensed_volume)) {
            $this->dispensed_volume = 0;
        }

        // Ensure pasteurization status is correct for a newly added donation
        $this->pasteurization_status = 'unpasteurized';

        if (is_null($this->added_to_inventory_at)) {
            $this->added_to_inventory_at = now();
        }

        return $this->save();
    }

    public function reduceVolume(float $amount): bool
    {
        $available = (float) $this->available_volume;
        $dispensed = (float) ($this->dispensed_volume ?? 0);
        $amount = (float) $amount;

        if ($available >= $amount) {
            // Update available volume
            $available -= $amount;
            
            // Update dispensed volume (cumulative)
            $dispensed += $amount;

            // Set available to 0 if fully depleted
            if ($available <= 0.0) {
                $available = 0.0;
            }

            // Update the model properties directly
            $this->available_volume = $available;
            $this->dispensed_volume = $dispensed;
            
            return $this->save();
        }
        
        return false;
    }

    /**
     * Consume volume from a specific bag index (0-based).
     * Adjust individual_bag_volumes, available_volume and dispensed_volume accordingly.
     */
    public function consumeFromBag(int $bagIndex, float $amount): bool
    {
        $amount = (float)$amount;
        if (empty($this->individual_bag_volumes) || !isset($this->individual_bag_volumes[$bagIndex])) {
            return false;
        }

        $bagVol = (float)$this->individual_bag_volumes[$bagIndex];
        if ($bagVol < $amount) {
            return false;
        }

        // Subtract from that bag
        $this->individual_bag_volumes[$bagIndex] = $bagVol - $amount;

        // Update aggregated fields
        $this->available_volume = max(0, (float)$this->available_volume - $amount);
        $this->dispensed_volume = (float)($this->dispensed_volume ?? 0) + $amount;

        // If bag becomes zero, decrement number_of_bags accordingly (keep integer)
        $nonEmpty = array_filter($this->individual_bag_volumes, function ($v) { return (float)$v > 0; });
        $this->number_of_bags = count($nonEmpty);

        return $this->save();
    }

    public function moveToBatch(int $batchId): bool
    {
        $this->pasteurization_status = 'pasteurized';
        $this->pasteurization_batch_id = $batchId;
        // When moved to batch, set available_volume to 0 (no longer available as individual donation)
        $this->attributes['available_volume'] = 0;
        
        return $this->save();
    }

    // Scopes for inventory management
    public function scopeUnpasteurizedInventory($query)
    {
        // Show ALL success donations that are unpasteurized
        // This includes donations with available_volume = 0 (fully dispensed)
        // The "Available" column will show the remaining volume
        return $query->whereIn('status', ['success_walk_in', 'success_home_collection'])
                    ->where('pasteurization_status', 'unpasteurized')
                    ->orderBy('added_to_inventory_at', 'asc');
    }

    public function scopeReadyForPasteurization($query)
    {
        return $query->whereIn('status', ['success_walk_in', 'success_home_collection'])
                    ->where('pasteurization_status', 'unpasteurized');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Donation extends Model
{
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
        'donation_date',
        'donation_time',
        'scheduled_pickup_date',
        'scheduled_pickup_time',
        'availability_id',
        // Inventory tracking fields
        'pasteurization_status',
        'pasteurization_batch_id',
        'available_volume',
        'inventory_status',
        'added_to_inventory_at',
    ];

    protected $casts = [
        'donation_date' => 'date',
        'scheduled_pickup_date' => 'date',
        'individual_bag_volumes' => 'array',
        'total_volume' => 'decimal:2',
        'available_volume' => 'decimal:2',
        'added_to_inventory_at' => 'datetime',
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
    $this->attributes['total_volume'] = number_format(array_sum($volumes), 2, '.', '');
    }

    public function getFormattedBagVolumesAttribute()
    {
        if (empty($this->individual_bag_volumes)) {
            return '-';
        }
        return implode(', ', array_map(function($vol) {
            return $vol . 'ml';
        }, $this->individual_bag_volumes));
    }

    public function getAverageVolumePerBagAttribute()
    {
        if (empty($this->individual_bag_volumes)) {
            return 0;
        }
        return round(array_sum($this->individual_bag_volumes) / count($this->individual_bag_volumes), 2);
    }

    // Inventory management methods
    public function isInInventory(): bool
    {
     // Consider a donation "in inventory" only if it's marked available and has usable volume
     return in_array($this->status, ['success_walk_in', 'success_home_collection']) && 
         $this->inventory_status === 'available' &&
         $this->pasteurization_status === 'unpasteurized' &&
         ($this->available_volume !== null && $this->available_volume > 0);
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
        return $this->inventory_status === 'depleted' || $this->available_volume <= 0;
    }

    public function addToInventory(): bool
    {
        // If record already appears to be in inventory and has a positive available_volume,
        // treat it as already added and do nothing.
        if ($this->inventory_status === 'available' && $this->available_volume > 0 && $this->pasteurization_status === 'unpasteurized') {
            return false; // Already in inventory with usable volume
        }

        // Ensure available_volume is set (some existing records may have inventory_status='available'
        // but missing available_volume or added_to_inventory_at). Fill using total_volume.
        if (is_null($this->available_volume) || $this->available_volume <= 0) {
            $this->attributes['available_volume'] = number_format($this->total_volume ?? 0.0, 2, '.', '');
        }

        // Ensure pasteurization and inventory status are correct for a newly added donation
        $this->pasteurization_status = 'unpasteurized';
        $this->inventory_status = 'available';

        if (is_null($this->added_to_inventory_at)) {
            $this->added_to_inventory_at = now();
        }

        return $this->save();
    }

    public function reduceVolume(float $amount): bool
    {
        $available = (float) $this->available_volume;
        $amount = (float) $amount;

        if ($available >= $amount) {
            $available -= $amount;

            // Mark as depleted if no volume left
            if ($available <= 0.0) {
                $this->inventory_status = 'depleted';
                $available = 0.0;
            }

            $this->attributes['available_volume'] = number_format($available, 2, '.', '');
            return $this->save();
        }
        
        return false;
    }

    public function moveToBatch(int $batchId): bool
    {
        $this->pasteurization_status = 'pasteurized';
        $this->pasteurization_batch_id = $batchId;
        $this->inventory_status = 'depleted'; // No longer available as individual donation
    $this->attributes['available_volume'] = number_format(0, 2, '.', '');
        
        return $this->save();
    }

    // Scopes for inventory management
    public function scopeUnpasteurizedInventory($query)
    {
        return $query->whereIn('status', ['success_walk_in', 'success_home_collection'])
                    ->where('pasteurization_status', 'unpasteurized')
                    ->where('inventory_status', 'available')
                    ->where('available_volume', '>', 0);
    }

    public function scopeReadyForPasteurization($query)
    {
        return $query->whereIn('status', ['success_walk_in', 'success_home_collection'])
                    ->where('pasteurization_status', 'unpasteurized');
    }
}

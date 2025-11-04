<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class DispensedMilk extends Model
{
    protected $table = 'dispensed_milk';
    protected $primaryKey = 'dispensed_id';
    
    protected $fillable = [
        'breastmilk_request_id',
        'guardian_user_id',
        'recipient_infant_id',
        'volume_dispensed',
        'source_donation_id',
        'source_batch_id',
        'date_dispensed',
        'time_dispensed',
        'admin_id',
        'dispensing_notes'
    ];

    protected $casts = [
        'date_dispensed' => 'date',
        // time_dispensed is stored as time string (H:i:s); format defensively in accessor
        'volume_dispensed' => 'decimal:2'
    ];

    // Relationships
    public function breastmilkRequest(): BelongsTo
    {
        return $this->belongsTo(BreastmilkRequest::class, 'breastmilk_request_id', 'breastmilk_request_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_user_id', 'user_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Infant::class, 'recipient_infant_id', 'infant_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function sourceDonations(): BelongsToMany
    {
        return $this->belongsToMany(Donation::class, 'dispensed_milk_sources', 'dispensed_id', 'source_id')
            ->where('source_type', 'unpasteurized')
            ->withPivot('volume_used')
            ->withTimestamps();
    }

    public function sourceBatches(): BelongsToMany
    {
        return $this->belongsToMany(PasteurizationBatch::class, 'dispensed_milk_sources', 'dispensed_id', 'source_id')
            ->where('source_type', 'pasteurized')
            ->withPivot('volume_used')
            ->withTimestamps();
    }

    // Utility methods
    public function getFormattedDateAttribute(): string
    {
        if (!$this->date_dispensed) return '-';
        return Carbon::parse($this->date_dispensed)->format('M d, Y');
    }

    public function getFormattedTimeAttribute(): string
    {
        if (!$this->time_dispensed) return '-';
        return Carbon::parse($this->time_dispensed)->format('g:i A');
    }

    public function getFormattedVolumeDispensedAttribute()
    {
        $vol = (float) $this->volume_dispensed;
        return $vol == (int)$vol ? (int)$vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
    }

    public function isFromUnpasteurized(): bool
    {
        // If the relation is already loaded, avoid an extra DB query by checking the collection
        if ($this->relationLoaded('sourceDonations')) {
            return ($this->sourceDonations instanceof \Illuminate\Database\Eloquent\Collection)
                ? $this->sourceDonations->isNotEmpty()
                : !empty($this->sourceDonations);
        }

        // Otherwise fall back to the efficient exists() check
        return $this->sourceDonations()->exists();
    }

    public function isFromPasteurized(): bool
    {
        if ($this->relationLoaded('sourceBatches')) {
            return ($this->sourceBatches instanceof \Illuminate\Database\Eloquent\Collection)
                ? $this->sourceBatches->isNotEmpty()
                : !empty($this->sourceBatches);
        }

        return $this->sourceBatches()->exists();
    }

    // Derive milk_type based on attached sources
    public function getMilkTypeAttribute(): ?string
    {
        // Prefer using loaded relations to avoid extra exists() queries
        if ($this->relationLoaded('sourceDonations')) {
            if (($this->sourceDonations instanceof \Illuminate\Database\Eloquent\Collection) ? $this->sourceDonations->isNotEmpty() : !empty($this->sourceDonations)) {
                return 'unpasteurized';
            }
        } else {
            if ($this->sourceDonations()->exists()) return 'unpasteurized';
        }

        if ($this->relationLoaded('sourceBatches')) {
            if (($this->sourceBatches instanceof \Illuminate\Database\Eloquent\Collection) ? $this->sourceBatches->isNotEmpty() : !empty($this->sourceBatches)) {
                return 'pasteurized';
            }
        } else {
            if ($this->sourceBatches()->exists()) return 'pasteurized';
        }

        return null;
    }

    public function getSourceDisplayAttribute(): string
    {
        $sources = [];
        
        // Get unpasteurized sources
        foreach ($this->sourceDonations as $donation) {
            // prefer the donor's user full name when available
            $label = null;
            if ($donation->relationLoaded('user') || $donation->user) {
                $user = $donation->user;
                $first = $user->first_name ?? '';
                $last = $user->last_name ?? '';
                $name = trim(($first . ' ' . $last));
                if ($name !== '') $label = $name;
            }

            // fallback to any donor_name attribute (some endpoints populate this on the fly)
            if (!$label && !empty($donation->donor_name)) {
                $label = $donation->donor_name;
            }

            if ($label) {
                $sources[] = "{$label} ({$donation->pivot->volume_used}ml)";
            } else {
                $sources[] = "Donation #{$donation->breastmilk_donation_id} ({$donation->pivot->volume_used}ml)";
            }
        }
        
        // Get pasteurized sources
        foreach ($this->sourceBatches as $batch) {
            $sources[] = "Batch {$batch->batch_number} ({$batch->pivot->volume_used}ml)";
        }
        
        return $sources ? implode(', ', $sources) : 'Unknown Source';
    }

    /**
     * Simplified source name (no pivot volumes) for table displays.
     * Returns donor full name(s) or batch number(s) or '-' when unknown.
     */
    public function getSourceNameAttribute(): string
    {
        $labels = [];

        if ($this->sourceDonations && $this->sourceDonations->count() > 0) {
            foreach ($this->sourceDonations as $donation) {
                $name = null;
                if ($donation->relationLoaded('user') || $donation->user) {
                    $user = $donation->user;
                    $first = $user->first_name ?? '';
                    $last = $user->last_name ?? '';
                    $name = trim(($first . ' ' . $last));
                }
                if (!$name && !empty($donation->donor_name)) $name = $donation->donor_name;
                if (!$name) $name = 'Donation #' . ($donation->breastmilk_donation_id ?? '-');
                $labels[] = $name;
            }
        } elseif ($this->sourceBatches && $this->sourceBatches->count() > 0) {
            $batchNumbers = $this->sourceBatches->pluck('batch_number')->filter()->all();
            if (!empty($batchNumbers)) {
                $labels = $batchNumbers;
            } else {
                $labels[] = 'Pasteurized batch';
            }
        }

        return $labels ? implode(', ', array_unique($labels)) : '-';
    }
}

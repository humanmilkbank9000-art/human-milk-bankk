<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BreastmilkRequest extends Model
{
    use SoftDeletes;
    protected $table = 'breastmilk_request';
    protected $primaryKey = 'breastmilk_request_id';

    protected $fillable = [
        'user_id',
        'infant_id',
        'availability_id',
        'admin_id',
        'prescription_path',
        'prescription_filename',
        'prescription_mime_type',
        'volume_requested',
        'request_date',
        'request_time',
        'status',
        'approved_at',
        'declined_at',
        'admin_notes',
        // Dispensing fields
        'volume_dispensed',
        'dispensed_at',
        'dispensing_notes',
        'dispensed_milk_id'
    ];

    protected $casts = [
        'request_date' => 'date',
        'request_time' => 'datetime:H:i',
        'volume_requested' => 'decimal:2',
        'volume_dispensed' => 'decimal:2',
        'approved_at' => 'datetime',
        'declined_at' => 'datetime',
        'dispensed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function infant()
    {
        return $this->belongsTo(Infant::class, 'infant_id', 'infant_id');
    }

    public function availability()
    {
        return $this->belongsTo(Availability::class, 'availability_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'admin_id');
    }

    // Status helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isDeclined()
    {
        return $this->status === 'declined';
    }

    // Status badge color helper
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'dispensed' => 'info',
            'declined' => 'danger',
            default => 'secondary'
        };
    }

    // Get formatted appointment date and time
    public function getFormattedAppointmentAttribute()
    {
        if ($this->availability) {
            return $this->availability->formatted_date;
        }
        return Carbon::parse($this->request_date)->format('M d, Y');
    }

    public function getFormattedVolumeRequestedAttribute()
    {
        $vol = (float) $this->volume_requested;
        return $vol == (int)$vol ? (int)$vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
    }

    public function getFormattedVolumeDispensedAttribute()
    {
        $vol = (float) $this->volume_dispensed;
        return $vol == (int)$vol ? (int)$vol : rtrim(rtrim(number_format($vol, 2, '.', ''), '0'), '.');
    }

    // Mark as approved
    public function approve($adminId, $volumeRequested = null, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'admin_id' => $adminId,
            'volume_requested' => $volumeRequested,
            'admin_notes' => $notes,
            'approved_at' => now(),
        ]);
    }

    // Mark as declined
    public function decline($adminId, $notes = null)
    {
        $this->update([
            'status' => 'declined',
            'admin_id' => $adminId,
            'admin_notes' => $notes,
            'declined_at' => now(),
        ]);
    }

    // Get prescription file content as base64 for display
    public function getPrescriptionAsBase64()
    {
        if (!$this->prescription_path) {
            return null;
        }

        // Try public disk first (where new files are stored)
        if (Storage::disk('public')->exists($this->prescription_path)) {
            return base64_encode(Storage::disk('public')->get($this->prescription_path));
        }

        // Fall back to default disk (legacy files)
        if (Storage::exists($this->prescription_path)) {
            return base64_encode(Storage::get($this->prescription_path));
        }

        return null;
    }

    // Store prescription file from uploaded file
    public function storePrescriptionFile($file)
    {
        // Delete old file if exists
        if ($this->prescription_path) {
            if (Storage::disk('public')->exists($this->prescription_path)) {
                Storage::disk('public')->delete($this->prescription_path);
            } elseif (Storage::exists($this->prescription_path)) {
                Storage::delete($this->prescription_path);
            }
        }
        
        // Store new file
        $fileName = 'prescriptions/' . uniqid() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('prescriptions', basename($fileName), 'public');

        $this->prescription_path = $path; // e.g. prescriptions/uniq_file.png
        $this->prescription_filename = $file->getClientOriginalName();
        $this->prescription_mime_type = $file->getMimeType();
        $this->save();
    }

    // Check if prescription exists
    public function hasPrescription()
    {
        if (empty($this->prescription_path)) {
            return false;
        }

        // Check public disk for newer files
        if (Storage::disk('public')->exists($this->prescription_path)) {
            return true;
        }

        // Fallback: check default disk for legacy files
        return Storage::exists($this->prescription_path);
    }

    // Get prescription file URL for download
    public function getPrescriptionUrl()
    {
        if (!$this->hasPrescription()) {
            return null;
        }
        // Prefer public disk URL when available
        if (Storage::disk('public')->exists($this->prescription_path)) {
            // Storage::url may not exist on some drivers via static analysis, but at runtime public disk will provide a URL
            return '/storage/' . ltrim($this->prescription_path, '/');
        }

        // Fall back to Storage::url for default disk if supported
        try {
            return Storage::url($this->prescription_path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Delete prescription file when model is deleted
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($request) {
            if ($request->prescription_path && Storage::exists($request->prescription_path)) {
                Storage::delete($request->prescription_path);
            }
        });
    }

    // New relationship for dispensing
    public function dispensedMilk()
    {
        return $this->belongsTo(DispensedMilk::class, 'dispensed_milk_id', 'dispensed_id');
    }

    // New dispensing status method
    public function isDispensed(): bool
    {
        return $this->status === 'dispensed';
    }

    // Update status badge color to include 'dispensed'
    public function getStatusBadgeColorUpdated(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success', 
            'dispensed' => 'info',
            'declined' => 'danger',
            default => 'secondary'
        };
    }

    // New scopes for dispensing workflow
    public function scopeReadyForDispensing($query)
    {
        return $query->where('status', 'approved')
                    ->whereNotNull('volume_requested')
                    ->whereNull('dispensed_at');
    }

    public function scopeDispensed($query)
    {
        return $query->where('status', 'dispensed');
    }
}
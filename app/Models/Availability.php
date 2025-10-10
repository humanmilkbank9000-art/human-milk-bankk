<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Availability extends Model
{
    protected $table = 'admin_availability';

    protected $fillable = [
        'available_date',
        'start_time',
        'end_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'available_date' => 'date',
    ];

    // Scopes for easy querying in appointment system
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeBooked($query)
    {
        return $query->where('status', 'booked');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('available_date', $date);
    }

    public function scopeFuture($query)
    {
        $now = Carbon::now();
        return $query->where(function ($q) use ($now) {
            $q->where('available_date', '>', $now->toDateString())
              ->orWhere(function ($subQ) use ($now) {
                  $subQ->where('available_date', '=', $now->toDateString())
                       ->where('start_time', '>', $now->format('H:i'));
              });
        });
    }

    public function scopeOrderByTime($query)
    {
        return $query->orderBy('available_date')->orderBy('start_time');
    }

    // Status checking methods
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isBooked(): bool
    {
        return $this->status === 'booked';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    // Helper methods for appointment system
    public function getDayOfWeekAttribute()
    {
        return Carbon::parse($this->available_date)->format('l');
    }

    public function getTimeSlotAttribute()
    {
        return $this->start_time . ' - ' . $this->end_time;
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->available_date)->format('M d, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->start_time)->format('g:i A') . ' - ' . 
               Carbon::parse($this->end_time)->format('g:i A');
    }

    public function isBookable()
    {
        return $this->status === 'available' && Carbon::parse($this->available_date)->isFuture();
    }

    public function markAsBooked()
    {
        $this->update(['status' => 'booked']);
    }

    public function markAsAvailable()
    {
        $this->update(['status' => 'available']);
    }

    public function markAsBlocked()
    {
        $this->update(['status' => 'blocked']);
    }

    // Future: Relationship to appointments
    // public function appointment()
    // {
    //     return $this->hasOne(Appointment::class, 'availability_id');
    // }
}

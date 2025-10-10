<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Admin extends Model
{
    use Notifiable;
    protected $table = 'admin';
    protected $primaryKey = 'admin_id';
    
    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
        'email',
        'contact_number'
    ];

    protected $hidden = [
        'password'
    ];

    // Relationships
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'admin_id', 'admin_id');
    }

    public function breastmilkRequests(): HasMany
    {
        return $this->hasMany(BreastmilkRequest::class, 'admin_id', 'admin_id');
    }

    public function pasteurizationBatches(): HasMany
    {
        return $this->hasMany(PasteurizationBatch::class, 'admin_id', 'admin_id');
    }

    public function dispensedMilk(): HasMany
    {
        return $this->hasMany(DispensedMilk::class, 'admin_id', 'admin_id');
    }

    // Utility methods
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}

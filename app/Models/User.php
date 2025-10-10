<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Explicitly define the table
    protected $table = 'user';  

    // Explicitly define the PK
    protected $primaryKey = 'user_id';  

    protected $fillable = [
        'contact_number',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'address',
        'latitude',
        'longitude',
        'date_of_birth',
        'age',
        'sex',
        'user_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'password' => 'hashed',
    ];

    // Laravel auth expects these methods for authentication
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    public function getAuthIdentifier()
    {
        return $this->user_id;
    }

    // For password reset functionality using contact_number instead of email
    public function getEmailForPasswordReset()
    {
        return $this->contact_number;
    }

    // Create a virtual email attribute for Laravel auth compatibility
    public function getEmailAttribute()
    {
        return $this->contact_number;
    }

    // Full name accessor for display purposes
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    // Relationship with Infant
    public function infants()
    {
        return $this->hasMany(Infant::class, 'user_id', 'user_id');
    }

    // Relationship with HealthScreening
    public function healthScreenings()
    {
        return $this->hasMany(HealthScreening::class, 'user_id', 'user_id');
    }
}

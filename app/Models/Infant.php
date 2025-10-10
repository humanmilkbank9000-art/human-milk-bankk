<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Infant extends Model
{
    use HasFactory;

    protected $table = 'infant';
    protected $primaryKey = 'infant_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'date_of_birth',
        'age',
        'birth_weight',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'birth_weight' => 'decimal:2',
    ];

    // Calculate current age in months
    public function getCurrentAgeInMonths()
    {
        if (!$this->date_of_birth) {
            return null;
        }

        // Calculate total months safely using DateInterval and avoid negative values
        $dob = Carbon::parse($this->date_of_birth)->startOfDay();
        $now = Carbon::now()->startOfDay();

        // If DOB is in the future, treat as 0 months (invalid DOB)
        if ($dob->gt($now)) {
            return 0;
        }

        $diff = $dob->diff($now); // DateInterval with y, m, d
        $totalMonths = ($diff->y * 12) + $diff->m;

        return (int) $totalMonths;
    }

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relationship to HealthScreenings
    public function healthScreenings()
    {
        return $this->hasMany(HealthScreening::class, 'infant_id', 'infant_id');
    }
}

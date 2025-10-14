<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthScreening extends Model
{
    protected $table = 'health_screening';
    protected $primaryKey = 'health_screening_id';
    protected $fillable = [
        'user_id','infant_id','civil_status','occupation','type_of_donor','status','date_accepted','date_declined','admin_notes',
        'medical_history_01','medical_history_02','medical_history_02_details','medical_history_03','medical_history_04','medical_history_04_details','medical_history_05','medical_history_05_details','medical_history_06','medical_history_07','medical_history_08','medical_history_08_details','medical_history_09','medical_history_10','medical_history_10_details','medical_history_11','medical_history_11_details','medical_history_12','medical_history_13','medical_history_13_details','medical_history_14','medical_history_15',
        'sexual_history_01','sexual_history_02','sexual_history_03','sexual_history_03_details','sexual_history_04','sexual_history_04_details',
        'donor_infant_01','donor_infant_02','donor_infant_03','donor_infant_04','donor_infant_04_details','donor_infant_05','donor_infant_05_details',
    ];

    protected $casts = [
        'date_accepted' => 'datetime',
        'date_declined' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function infant() {
        return $this->belongsTo(Infant::class, 'infant_id', 'infant_id');
    }
}

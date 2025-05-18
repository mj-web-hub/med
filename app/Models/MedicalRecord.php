<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'picture_path',
        'date_of_birth',
        'gender',
        'address',
        'contact_number',
        'marital_status',
        'occupation',
        'nationality',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_number',
        'emergency_contact_email',
        'chronic_conditions',
        'previous_illnesses',
        'surgeries_hospitalizations',
        'allergies',
        'immunization_history',
        'childhood_illnesses'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPictureUrlAttribute()
    {
        return $this->picture_path ? asset('storage/'.$this->picture_path) : null;
    }
}
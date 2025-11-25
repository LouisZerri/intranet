<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'city',
        'department',
        'position_applied',
        'desired_location',
        'available_from',
        'cv_path',
        'cover_letter_path',
        'rating_motivation',
        'rating_seriousness',
        'rating_experience',
        'rating_commercial_skills',
        'notes',
        'interview_notes',
        'status',
        'source',
        'created_by',
        'assigned_to',
        'converted_to_user_id',
        'interview_date',
        'decision_date',
    ];

    protected $casts = [
        'available_from' => 'date',
        'interview_date' => 'date',
        'decision_date' => 'date',
        'rating_motivation' => 'integer',
        'rating_seriousness' => 'integer',
        'rating_experience' => 'integer',
        'rating_commercial_skills' => 'integer',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    public function getAverageRatingAttribute(): ?float
    {
        $ratings = array_filter([
            $this->rating_motivation,
            $this->rating_seriousness,
            $this->rating_experience,
            $this->rating_commercial_skills,
        ]);

        if (empty($ratings)) {
            return null;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'new' => 'Nouvelle candidature',
            'in_review' => 'En cours d\'examen',
            'interview' => 'Entretien programmé',
            'recruited' => 'Recruté',
            'integrated' => 'Intégré',
            'refused' => 'Refusé',
            default => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new' => 'blue',
            'in_review' => 'yellow',
            'interview' => 'purple',
            'recruited' => 'green',
            'integrated' => 'emerald',
            'refused' => 'red',
            default => 'gray',
        };
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recruiter()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedUser()
    {
        return $this->belongsTo(User::class, 'converted_to_user_id');
    }
}
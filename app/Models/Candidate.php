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
        // Documents existants
        'cv_path',
        'cover_letter_path',
        // Google Drive
        'google_drive_folder_id',
        'cv_drive_link',
        'cover_letter_drive_link',
        // Nouveaux documents
        'identity_card_path',
        'identity_card_drive_link',
        'proof_of_address_path',
        'proof_of_address_drive_link',
        'legal_status_path',
        'legal_status_drive_link',
        'rcp_insurance_path',
        'rcp_insurance_drive_link',
        'signed_contract_path',
        'signed_contract_drive_link',
        'criminal_record_path',
        'criminal_record_drive_link',
        'rib_path',
        'rib_drive_link',
        'training_certificate_path',
        'training_certificate_drive_link',
        // Évaluations
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

    /**
     * Liste des types de documents avec leurs labels
     */
    public static function getDocumentTypes(): array
    {
        return [
            'cv' => [
                'label' => 'CV',
                'icon' => '📄',
                'path_field' => 'cv_path',
                'link_field' => 'cv_drive_link',
            ],
            'cover_letter' => [
                'label' => 'Lettre de motivation',
                'icon' => '✉️',
                'path_field' => 'cover_letter_path',
                'link_field' => 'cover_letter_drive_link',
            ],
            'identity_card' => [
                'label' => 'Carte d\'identité',
                'icon' => '🪪',
                'path_field' => 'identity_card_path',
                'link_field' => 'identity_card_drive_link',
            ],
            'proof_of_address' => [
                'label' => 'Justificatif de domicile',
                'icon' => '🏠',
                'path_field' => 'proof_of_address_path',
                'link_field' => 'proof_of_address_drive_link',
            ],
            'legal_status' => [
                'label' => 'Statut juridique',
                'icon' => '⚖️',
                'path_field' => 'legal_status_path',
                'link_field' => 'legal_status_drive_link',
            ],
            'rcp_insurance' => [
                'label' => 'RCP (Assurance obligatoire)',
                'icon' => '🛡️',
                'path_field' => 'rcp_insurance_path',
                'link_field' => 'rcp_insurance_drive_link',
            ],
            'signed_contract' => [
                'label' => 'Contrat signé',
                'icon' => '📝',
                'path_field' => 'signed_contract_path',
                'link_field' => 'signed_contract_drive_link',
            ],
            'criminal_record' => [
                'label' => 'Casier judiciaire / Non-condamnation',
                'icon' => '📋',
                'path_field' => 'criminal_record_path',
                'link_field' => 'criminal_record_drive_link',
            ],
            'rib' => [
                'label' => 'RIB',
                'icon' => '🏦',
                'path_field' => 'rib_path',
                'link_field' => 'rib_drive_link',
            ],
            'training_certificate' => [
                'label' => 'Attestation de formation',
                'icon' => '🎓',
                'path_field' => 'training_certificate_path',
                'link_field' => 'training_certificate_drive_link',
            ],
        ];
    }

    /**
     * Vérifie si un document existe
     */
    public function hasDocument(string $type): bool
    {
        $types = self::getDocumentTypes();
        if (!isset($types[$type])) {
            return false;
        }

        $pathField = $types[$type]['path_field'];
        $linkField = $types[$type]['link_field'];

        return !empty($this->$pathField) || !empty($this->$linkField);
    }

    /**
     * Retourne l'URL d'un document
     */
    public function getDocumentUrl(string $type): ?string
    {
        $types = self::getDocumentTypes();
        if (!isset($types[$type])) {
            return null;
        }

        $linkField = $types[$type]['link_field'];
        if (!empty($this->$linkField)) {
            return $this->$linkField;
        }

        $pathField = $types[$type]['path_field'];
        if (!empty($this->$pathField) && !str_starts_with($this->$pathField, 'http')) {
            return asset('storage/' . $this->$pathField);
        }

        return null;
    }

    /**
     * Retourne tous les documents du candidat avec leur statut
     */
    public function getDocumentsStatus(): array
    {
        $documents = [];
        foreach (self::getDocumentTypes() as $type => $config) {
            $documents[$type] = [
                'label' => $config['label'],
                'icon' => $config['icon'],
                'uploaded' => $this->hasDocument($type),
                'url' => $this->getDocumentUrl($type),
            ];
        }
        return $documents;
    }

    /**
     * Compte les documents uploadés
     */
    public function getUploadedDocumentsCount(): int
    {
        $count = 0;
        foreach (self::getDocumentTypes() as $type => $config) {
            if ($this->hasDocument($type)) {
                $count++;
            }
        }
        return $count;
    }

    // Méthodes de compatibilité pour CV et lettre de motivation
    public function hasCv(): bool
    {
        return $this->hasDocument('cv');
    }

    public function hasCoverLetter(): bool
    {
        return $this->hasDocument('cover_letter');
    }

    public function getCvUrlAttribute(): ?string
    {
        return $this->getDocumentUrl('cv');
    }

    public function getCoverLetterUrlAttribute(): ?string
    {
        return $this->getDocumentUrl('cover_letter');
    }

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
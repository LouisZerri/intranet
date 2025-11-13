<?php

namespace App\Traits;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;

trait GeneratesPdfDocuments
{
    /**
     * Obtenir les informations professionnelles de l'utilisateur pour les PDF
     */
    protected function getUserProfessionalInfo(User $user): array
    {
        return [
            // Informations de base
            'full_name' => $user->full_name,
            'email' => $user->effective_email, // Utilise l'accesseur
            'phone' => $user->effective_phone, // Utilise l'accesseur
            
            // Informations professionnelles
            'rsac_number' => $user->rsac_number,
            'professional_address' => $user->professional_address,
            'professional_city' => $user->professional_city,
            'professional_postal_code' => $user->professional_postal_code,
            'formatted_address' => $user->formatted_professional_address, // Utilise l'accesseur
            
            // Mentions et textes
            'legal_mentions' => $user->legal_mentions,
            'footer_text' => $user->footer_text,
            
            // Signature
            'signature_url' => $user->signature_url, // Utilise l'accesseur
            'has_signature' => !empty($user->signature_image),
        ];
    }

    /**
     * Générer l'en-tête du PDF avec les infos du conseiller
     */
    protected function generatePdfHeader(User $user): string
    {
        $info = $this->getUserProfessionalInfo($user);
        
        $html = '<div style="margin-bottom: 30px; border-bottom: 2px solid #4F46E5; padding-bottom: 15px;">';
        $html .= '<div style="display: flex; justify-content: space-between; align-items: center;">';
        
        // Logo ou nom de l'entreprise (à gauche)
        $html .= '<div style="width: 50%;">';
        $html .= '<h2 style="margin: 0; color: #4F46E5; font-size: 24px;">' . htmlspecialchars($info['full_name']) . '</h2>';
        if ($info['rsac_number']) {
            $html .= '<p style="margin: 5px 0; font-size: 12px; color: #6B7280;">RSAC: ' . htmlspecialchars($info['rsac_number']) . '</p>';
        }
        $html .= '</div>';
        
        // Coordonnées (à droite)
        $html .= '<div style="width: 45%; text-align: right; font-size: 11px; color: #374151;">';
        if ($info['professional_address']) {
            $html .= '<p style="margin: 2px 0;">' . nl2br(htmlspecialchars($info['professional_address'])) . '</p>';
            if ($info['professional_postal_code'] || $info['professional_city']) {
                $html .= '<p style="margin: 2px 0;">';
                if ($info['professional_postal_code']) {
                    $html .= htmlspecialchars($info['professional_postal_code']) . ' ';
                }
                if ($info['professional_city']) {
                    $html .= htmlspecialchars($info['professional_city']);
                }
                $html .= '</p>';
            }
        }
        if ($info['email']) {
            $html .= '<p style="margin: 2px 0;">📧 ' . htmlspecialchars($info['email']) . '</p>';
        }
        if ($info['phone']) {
            $html .= '<p style="margin: 2px 0;">📞 ' . htmlspecialchars($info['phone']) . '</p>';
        }
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Générer le pied de page du PDF
     */
    protected function generatePdfFooter(User $user): string
    {
        $info = $this->getUserProfessionalInfo($user);
        
        $html = '<div style="margin-top: 40px; border-top: 1px solid #E5E7EB; padding-top: 20px;">';
        
        // Signature (si disponible)
        if ($info['has_signature'] && $info['signature_url']) {
            $html .= '<div style="text-align: right; margin-bottom: 20px;">';
            $html .= '<img src="' . $info['signature_url'] . '" style="max-width: 200px; max-height: 80px;" alt="Signature">';
            $html .= '</div>';
        }
        
        // Texte de pied de page personnalisé
        if ($info['footer_text']) {
            $html .= '<div style="text-align: center; font-size: 11px; color: #6B7280; font-style: italic; margin-bottom: 15px;">';
            $html .= '<p style="margin: 5px 0;">' . nl2br(htmlspecialchars($info['footer_text'])) . '</p>';
            $html .= '</div>';
        }
        
        // Mentions légales
        if ($info['legal_mentions']) {
            $html .= '<div style="font-size: 9px; color: #9CA3AF; text-align: center; line-height: 1.4;">';
            $html .= '<p style="margin: 5px 0;">' . nl2br(htmlspecialchars($info['legal_mentions'])) . '</p>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Générer un PDF complet (devis ou facture)
     */
    protected function generateDocument(User $user, string $type, array $data, string $viewName): \Barryvdh\DomPDF\PDF
    {
        // Fusionner les données avec les infos professionnelles
        $pdfData = array_merge($data, [
            'user_info' => $this->getUserProfessionalInfo($user),
            'header_html' => $this->generatePdfHeader($user),
            'footer_html' => $this->generatePdfFooter($user),
            'document_type' => $type,
        ]);

        return Pdf::loadView($viewName, $pdfData)
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');
    }
}
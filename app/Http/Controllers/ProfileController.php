<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Afficher le profil de l'utilisateur
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Mettre à jour le profil utilisateur (UNIQUEMENT INFO PERSONNELLES)
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validation selon le rôle
        $rules = $this->getValidationRules($user);
        $validated = $request->validate($rules);

        // Mettre à jour uniquement les champs autorisés selon le rôle
        $allowedFields = $this->getAllowedFields($user);
        $updateData = array_intersect_key($validated, array_flip($allowedFields));

        $user->update($updateData);

        return redirect()->route('profile.edit')->with('success', 'Profil mis à jour avec succès');
    }

    /**
     * NOUVELLE MÉTHODE : Mettre à jour UNIQUEMENT les informations professionnelles
     */
    public function updateProfessional(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validation uniquement des champs professionnels
        $validated = $request->validate([
            'rsac_number' => 'nullable|string|max:255',
            'professional_address' => 'nullable|string|max:500',
            'professional_city' => 'nullable|string|max:255',
            'professional_postal_code' => 'nullable|string|max:10',
            'professional_email' => 'nullable|email|max:255',
            'professional_phone' => 'nullable|string|max:255',
            'legal_mentions' => 'nullable|string|max:2000',
            'footer_text' => 'nullable|string|max:1000',
        ], [
            'professional_email.email' => 'L\'email professionnel doit être une adresse email valide.',
            'rsac_number.max' => 'Le numéro RSAC ne doit pas dépasser 255 caractères.',
            'professional_address.max' => 'L\'adresse ne doit pas dépasser 500 caractères.',
            'legal_mentions.max' => 'Les mentions légales ne doivent pas dépasser 2000 caractères.',
            'footer_text.max' => 'Le texte de pied de page ne doit pas dépasser 1000 caractères.',
        ]);

        // Mise à jour directe (pas de restriction de rôle pour ces champs)
        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Informations professionnelles mises à jour avec succès ! 🏢');
    }

    /**
     * Mettre à jour uniquement l'avatar
     */
    public function updateAvatar(Request $request, $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $this->handleAvatarUpload($request->file('avatar'), $user);
            $user->update(['avatar' => $avatarPath]);

            return redirect()->route('profile.edit')->with('success', 'Photo de profil mise à jour avec succès');
        }

        return redirect()->route('profile.edit')->with('error', 'Aucun fichier sélectionné');
    }

    /**
     * Gérer l'upload de la photo de profil
     */
    private function handleAvatarUpload($file, $user): string
    {
        // Supprimer l'ancien avatar s'il existe
        if ($user->avatar && $user->avatar !== 'default-avatar.png') {
            $oldAvatarPath = storage_path('app/public/avatars/' . $user->avatar);
            if (file_exists($oldAvatarPath)) {
                unlink($oldAvatarPath);
            }
        }

        // Créer le répertoire s'il n'existe pas
        $avatarDir = storage_path('app/public/avatars');
        if (!is_dir($avatarDir)) {
            mkdir($avatarDir, 0755, true);
        }

        $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('avatars', $fileName, 'public');

        return $fileName;
    }

    /**
     * Supprimer la photo de profil
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && $user->avatar !== 'default-avatar.png') {
            $avatarPath = storage_path('app/public/avatars/' . $user->avatar);
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }
        }

        /** @var \App\Models\User $user */
        $user->update(['avatar' => null]);

        return redirect()->route('profile.edit')->with('success', 'Photo de profil supprimée avec succès');
    }

    /**
     * Mettre à jour la signature
     */
    public function updateSignature(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'signature_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('signature_image')) {
            // Supprimer l'ancienne signature
            if ($user->signature_image && $user->signature_image !== 'default-signature.png') {
                $oldSignaturePath = storage_path('app/public/signatures/' . $user->signature_image);
                if (file_exists($oldSignaturePath)) {
                    unlink($oldSignaturePath);
                }
            }

            // Créer le répertoire s'il n'existe pas
            $signatureDir = storage_path('app/public/signatures');
            if (!is_dir($signatureDir)) {
                mkdir($signatureDir, 0755, true);
            }

            $fileName = 'signature_' . $user->id . '_' . time() . '.' . $request->file('signature_image')->getClientOriginalExtension();
            $request->file('signature_image')->storeAs('signatures', $fileName, 'public');

            /** @var \App\Models\User $user */
            $user->update(['signature_image' => $fileName]);

            return redirect()->route('profile.edit')->with('success', 'Signature mise à jour avec succès');
        }

        return redirect()->route('profile.edit')->with('error', 'Aucun fichier sélectionné');
    }

    /**
     * Supprimer la signature
     */
    public function removeSignature()
    {
        $user = Auth::user();

        if ($user->signature_image && $user->signature_image !== 'default-signature.png') {
            $signaturePath = storage_path('app/public/signatures/' . $user->signature_image);
            if (file_exists($signaturePath)) {
                unlink($signaturePath);
            }
        }

        /** @var \App\Models\User $user */
        $user->update(['signature_image' => null]);

        return redirect()->route('profile.edit')->with('success', 'Signature supprimée avec succès');
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Vérifier le mot de passe actuel
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ]);
        }

        /** @var \App\Models\User $user */
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Mot de passe mis à jour avec succès');
    }

    /**
     * Obtenir les règles de validation selon le rôle (SANS les champs professionnels)
     */
    private function getValidationRules($user): array
    {
        $baseRules = [
            'phone' => 'nullable|string|max:255',
        ];

        // Seuls les administrateurs peuvent modifier tous les champs personnels
        if ($user->isAdministrateur()) {
            return array_merge($baseRules, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'position' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
            ]);
        }

        return $baseRules;
    }

    /**
     * Obtenir les champs autorisés selon le rôle (SANS les champs professionnels)
     */
    private function getAllowedFields($user): array
    {
        if ($user->isAdministrateur()) {
            return [
                'first_name',
                'last_name',
                'email',
                'phone',
                'position',
                'department',
            ];
        }

        // Managers et Collaborateurs : téléphone uniquement
        return ['phone'];
    }

    /**
     * Vérifier si l'utilisateur peut modifier un champ spécifique
     */
    public function canEditField($user, string $field): bool
    {
        $allowedFields = $this->getAllowedFields($user);
        return in_array($field, $allowedFields);
    }
}
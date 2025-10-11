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
     * Mettre à jour le profil utilisateur
     * Restrictions selon le rôle
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Si c'est uniquement un upload d'avatar
        if ($request->hasFile('avatar') && !$request->has('first_name')) {
            return $this->updateAvatar($request, $user);
        }

        // Sinon, mise à jour des informations du profil
        $rules = $this->getValidationRules($user);
        $validated = $request->validate($rules);

        // Mettre à jour uniquement les champs autorisés selon le rôle
        $allowedFields = $this->getAllowedFields($user);
        $updateData = array_intersect_key($validated, array_flip($allowedFields));

        $user->update($updateData);

        return redirect()->route('profile.edit')->with('success', 'Profil mis à jour avec succès');
    }

    /**
     * Mettre à jour uniquement l'avatar
     */
    public function updateAvatar(Request $request, $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        // Validation spécifique pour l'avatar
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $this->handleAvatarUpload($request->file('avatar'), $user);

            // Mise à jour directe de l'avatar
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
        // Supprimer l'ancien avatar s'il existe (sauf l'avatar par défaut)
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

        // Créer le nom du fichier avec timestamp pour éviter les conflits
        $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

        // CORRECTION : Stocker directement dans le bon dossier
        $file->storeAs('avatars', $fileName, 'public');

        return $fileName;
    }

    /**
     * Supprimer la photo de profil
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        // Supprimer le fichier s'il existe
        if ($user->avatar && $user->avatar !== 'default-avatar.png') {
            $avatarPath = storage_path('app/public/avatars/' . $user->avatar);
            if (file_exists($avatarPath)) {
                unlink($avatarPath);
            }
        }

        // Remettre l'avatar par défaut
        /** @var \App\Models\User $user */
        $user->update(['avatar' => null]);

        return redirect()->route('profile.edit')->with('success', 'Photo de profil supprimée avec succès');
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

        // Mettre à jour le mot de passe
        /** @var \App\Models\User $user */
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Mot de passe mis à jour avec succès');
    }

    /**
     * Obtenir les règles de validation selon le rôle
     */
    private function getValidationRules($user): array
    {
        $baseRules = [
            'phone' => 'nullable|string|max:255',
        ];

        // Seuls les administrateurs peuvent modifier tous les champs
        if ($user->isAdministrateur()) {
            return array_merge($baseRules, [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'position' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
            ]);
        }

        // Managers et Collaborateurs : uniquement téléphone
        return $baseRules;
    }

    /**
     * Obtenir les champs autorisés selon le rôle
     */
    private function getAllowedFields($user): array
    {
        // Seuls les administrateurs peuvent modifier tous les champs
        if ($user->isAdministrateur()) {
            return [
                'first_name',
                'last_name',
                'email',
                'phone',
                'position',
                'department'
            ];
        }

        // Managers et Collaborateurs : uniquement téléphone
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

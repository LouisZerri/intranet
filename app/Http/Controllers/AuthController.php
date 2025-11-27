<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    // Affiche le formulaire de connexion
    public function showLogin()
    {
        return view('auth.login');
    }

    // Tentative de connexion utilisateur
    public function login(Request $request)
    {
        // Validation des identifiants
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Met à jour la date de dernière connexion
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user) {
                $user->last_login_at = now();
                $user->save();
            }

            return $this->redirectToDashboard();
        }

        // Authentification échouée
        throw ValidationException::withMessages([
            'email' => 'Ces identifiants ne correspondent à aucun compte.',
        ]);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    // Redirection selon le statut de l'utilisateur
    private function redirectToDashboard()
    {
        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a été désactivé. Contactez l\'administrateur.'
            ]);
        }

        $welcomeMessage = $this->getWelcomeMessage($user);
        return redirect()->route('dashboard')->with('success', $welcomeMessage);
    }

    // Message de bienvenue adapté au rôle
    private function getWelcomeMessage(User $user): string
    {
        $timeOfDay = $this->getTimeOfDay();
        $roleLabel = match ($user->role) {
            'administrateur' => 'Administrateur',
            'manager' => 'Manager',
            'collaborateur' => 'Collaborateur',
            default => 'Utilisateur'
        };

        return "{$timeOfDay} {$user->first_name} ! Bienvenue sur votre espace {$roleLabel}.";
    }

    // Salutation selon l'heure
    private function getTimeOfDay(): string
    {
        $hour = now()->format('H');

        return match (true) {
            $hour < 12 => 'Bonjour',
            $hour < 18 => 'Bon après-midi',
            default => 'Bonsoir'
        };
    }
}

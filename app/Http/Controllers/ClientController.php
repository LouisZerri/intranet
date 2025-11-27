<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Liste les clients de l'utilisateur connecté avec recherche et filtres
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Base de la requête filtrée par utilisateur
        $query = Client::forUser($user);

        // Recherche par mot clé (nom, etc.)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->search($search);
        }

        // Filtre par type de client
        if ($request->filled('type')) {
            if ($request->type === 'particulier') {
                $query->particulier();
            } elseif ($request->type === 'professionnel') {
                $query->professionnel();
            }
        }

        // Filtre par statut (actif ou inactif)
        if ($request->filled('status')) {
            if ($request->status === 'actif') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactif') {
                $query->where('is_active', false);
            }
        }

        // Récupère la liste paginée des clients avec compte devis/factures
        $clients = $query->withCount(['quotes', 'invoices'])
            ->orderBy('name')
            ->paginate(15);
        $clients->appends($request->all()); // Garde les filtres dans la pagination

        // Statistiques (total, par type, actifs)
        $stats = [
            'total' => Client::forUser($user)->count(),
            'particuliers' => Client::forUser($user)->particulier()->count(),
            'professionnels' => Client::forUser($user)->professionnel()->count(),
            'actifs' => Client::forUser($user)->active()->count(),
        ];

        return view('clients.index', compact('clients', 'stats', 'request'));
    }

    /**
     * Affiche le formulaire de création rapide d'un client
     */
    public function create()
    {
        return view('clients.form');
    }

    /**
     * Crée un nouveau client à partir du formulaire
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:particulier,professionnel',
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|required_if:type,professionnel|string|max:255',
            'siret' => 'nullable|string|max:14',
            'tva_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'type.required' => 'Le type de client est obligatoire.',
            'name.required' => 'Le nom du contact est obligatoire.',
            'company_name.required_if' => 'Le nom de l\'entreprise est obligatoire pour un client professionnel.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
        ]);

        // Par défaut, le client est actif si absent dans le formulaire (checkbox décochée)
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        // Associe le client à l'utilisateur connecté
        $validated['user_id'] = Auth::id();

        $client = Client::create($validated);

        // Réponse AJAX pour création rapide
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'client' => $client,
                'message' => 'Client créé avec succès !'
            ]);
        }

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client créé avec succès !');
    }

    /**
     * Affiche le détail d'un client (avec stats, derniers devis/factures)
     */
    public function show(Client $client)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifie droits d'accès à ce client
        if (!$this->canUserViewClient($user, $client)) {
            abort(403, 'Vous n\'avez pas accès à ce client.');
        }

        // Précharge relations nécessaires
        $client->load(['quotes', 'invoices.payments', 'user']);

        // Récupère statistiques
        $stats = $client->getStatistics();

        // 5 derniers devis
        $recentQuotes = $client->quotes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 5 dernières factures
        $recentInvoices = $client->invoices()
            ->with(['user', 'payments'])
            ->orderBy('issued_at', 'desc')
            ->take(5)
            ->get();

        return view('clients.show', compact('client', 'stats', 'recentQuotes', 'recentInvoices'));
    }

    /**
     * Formulaire d'édition d'un client existant
     */
    public function edit(Client $client)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifie droits de modification
        if (!$this->canUserEditClient($user, $client)) {
            abort(403, 'Vous n\'avez pas les droits pour modifier ce client.');
        }

        return view('clients.form', compact('client'));
    }

    /**
     * Met à jour les informations d'un client
     */
    public function update(Request $request, Client $client)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifie droits de modification
        if (!$this->canUserEditClient($user, $client)) {
            abort(403, 'Vous n\'avez pas les droits pour modifier ce client.');
        }

        $validated = $request->validate([
            'type' => 'required|in:particulier,professionnel',
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|required_if:type,professionnel|string|max:255',
            'siret' => 'nullable|string|max:14',
            'tva_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Détermine la valeur de is_active (checkbox)
        $validated['is_active'] = $request->has('is_active');

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client mis à jour avec succès !');
    }

    /**
     * Suppression définitive d'un client (si aucun devis/facture associé)
     */
    public function destroy(Client $client)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifie droits de suppression
        if (!$this->canUserDeleteClient($user, $client)) {
            abort(403, 'Vous n\'avez pas les droits pour supprimer ce client.');
        }

        // Interdit suppression si devis/factures associés
        if ($client->quotes()->count() > 0 || $client->invoices()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce client car il a des devis ou factures associés.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }

    /**
     * API : recherche AJAX des clients actifs pour l'autocomplete
     */
    public function search(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $search = $request->get('q', '');

        // Recherche max 10 clients actifs de l'utilisateur
        $clients = Client::forUser($user)
            ->active()
            ->search($search)
            ->take(10)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'text' => $client->display_name,
                    'type' => $client->type,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'address' => $client->full_address,
                ];
            });

        return response()->json(['results' => $clients]);
    }

    /**
     * Historique complet du client (tous devis et factures)
     */
    public function history(Client $client)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserViewClient($user, $client)) {
            abort(403);
        }

        // Tous les devis
        $quotes = $client->quotes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Toutes les factures
        $invoices = $client->invoices()
            ->with(['user', 'payments'])
            ->orderBy('issued_at', 'desc')
            ->get();

        return view('clients.history', compact('client', 'quotes', 'invoices'));
    }

    /**
     * Top clients : export ranking (réservé à l'admin)
     */
    public function topClients(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403);
        }

        // Nombre de clients à extraire (par défaut 20)
        $limit = $request->get('limit', 20);
        $topClients = Client::getTopClients($limit);

        return view('clients.top', compact('topClients'));
    }

    /**
     * Liste les clients ayant des factures en retard (admin & manager)
     */
    public function withOverdueInvoices()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur() && !$user->isManager()) {
            abort(403);
        }

        // Retourne la liste directement fournie par la méthode modèle
        $clientsWithOverdue = Client::getClientsWithOverdueInvoices();

        return view('clients.overdue', compact('clientsWithOverdue'));
    }

    /**
     * Vérifie si l'utilisateur a accès à ce client
     */
    private function canUserViewClient($user, $client): bool
    {
        if ($user->isAdministrateur()) return true;
        if ($user->isManager()) {
            return $client->user_id === $user->id 
                || ($client->user && $client->user->manager_id === $user->id);
        }
        return $client->user_id === $user->id;
    }

    /**
     * Vérifie droits d'édition
     */
    private function canUserEditClient($user, $client): bool
    {
        if ($user->isAdministrateur()) return true;
        if ($user->isManager()) {
            return $client->user_id === $user->id 
                || ($client->user && $client->user->manager_id === $user->id);
        }
        return $client->user_id === $user->id;
    }

    /**
     * Vérifie droits de suppression
     */
    private function canUserDeleteClient($user, $client): bool
    {
        if ($user->isAdministrateur()) return true;
        return $client->user_id === $user->id;
    }
}
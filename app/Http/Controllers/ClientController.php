<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Liste des clients avec recherche et filtres
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->search($search);
        }

        // Filtre par type
        if ($request->filled('type')) {
            if ($request->type === 'particulier') {
                $query->particulier();
            } elseif ($request->type === 'professionnel') {
                $query->professionnel();
            }
        }

        // Filtre par statut
        if ($request->filled('status')) {
            if ($request->status === 'actif') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactif') {
                $query->where('is_active', false);
            }
        }

        $clients = $query->orderBy('name')->paginate(15);
        $clients->appends($request->all());

        return view('clients.index', compact('clients', 'request'));
    }

    /**
     * Formulaire de création rapide (selon CDC)
     */
    public function create()
    {
        return view('clients.form');
    }

    /**
     * Création rapide d'un client (selon CDC)
     * 
     * CORRECTION: Utiliser 'name' au lieu de 'first_name' et 'last_name'
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

        // Gérer le checkbox is_active (par défaut à true si non envoyé)
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $client = Client::create($validated);

        // Si création depuis un formulaire AJAX (création rapide dans devis)
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
     * Afficher un client avec ses statistiques complètes
     */
    public function show(Client $client)
    {
        // Charger les relations
        $client->load(['quotes', 'invoices.payments']);

        // Statistiques du client
        $stats = $client->getStatistics();

        // Devis récents
        $recentQuotes = $client->quotes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Factures récentes
        $recentInvoices = $client->invoices()
            ->with(['user', 'payments'])
            ->orderBy('issued_at', 'desc')
            ->take(5)
            ->get();

        return view('clients.show', compact('client', 'stats', 'recentQuotes', 'recentInvoices'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Client $client)
    {
        return view('clients.form', compact('client'));
    }

    /**
     * Mise à jour d'un client
     */
    public function update(Request $request, Client $client)
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
        ]);

        // Gérer le checkbox is_active
        $validated['is_active'] = $request->has('is_active');

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client mis à jour avec succès !');
    }

    /**
     * Suppression d'un client
     */
    public function destroy(Client $client)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Seuls les administrateurs peuvent supprimer
        if (!$user->isAdministrateur()) {
            abort(403, 'Vous n\'avez pas les droits pour supprimer un client.');
        }

        // Vérifier qu'il n'y a pas de devis/factures associés
        if ($client->quotes()->count() > 0 || $client->invoices()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce client car il a des devis ou factures associés.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }

    /**
     * API : Recherche de clients (pour autocomplete dans devis/factures)
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $clients = Client::active()
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
     * Historique complet du client (devis + factures)
     */
    public function history(Client $client)
    {
        $quotes = $client->quotes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $invoices = $client->invoices()
            ->with(['user', 'payments'])
            ->orderBy('issued_at', 'desc')
            ->get();

        return view('clients.history', compact('client', 'quotes', 'invoices'));
    }

    /**
     * Export des meilleurs clients (pour admin/comptabilité)
     */
    public function topClients(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403);
        }

        $limit = $request->get('limit', 20);
        $topClients = Client::getTopClients($limit);

        return view('clients.top', compact('topClients'));
    }

    /**
     * Liste des clients avec factures en retard
     */
    public function withOverdueInvoices()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur() && !$user->isManager()) {
            abort(403);
        }

        $clientsWithOverdue = Client::getClientsWithOverdueInvoices();

        return view('clients.overdue', compact('clientsWithOverdue'));
    }
}
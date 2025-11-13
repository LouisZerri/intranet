<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Client;
use App\Models\PredefinedService;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    /**
     * Liste des factures avec filtres et statistiques
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Invoice::forUser($user)->with(['client', 'user']);

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par période
        if ($request->filled('period')) {
            $period = $request->period;
            if ($period === 'month') {
                $query->thisMonth();
            } elseif ($period === 'year') {
                $query->thisYear();
            } elseif ($period === 'overdue') {
                $query->overdue();
            }
        }

        // Recherche par client
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%");
                    });
            });
        }

        // Tri
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $invoices = $query->paginate(15);
        $invoices->appends($request->all());

        // Statistiques
        $stats = [
            'total' => Invoice::forUser($user)->count(),
            'brouillon' => Invoice::forUser($user)->draft()->count(),
            'emise' => Invoice::forUser($user)->issued()->count(),
            'payee' => Invoice::forUser($user)->paid()->count(),
            'en_retard' => Invoice::forUser($user)->overdue()->count(),
            'ca_month' => Invoice::forUser($user)->paidThisMonth()->sum('total_ht'),
            'ca_year' => Invoice::forUser($user)->thisYear()->where('status', 'payee')->sum('total_ht'),
            'unpaid_amount' => Invoice::forUser($user)->issued()->get()->sum('remaining_amount'),
        ];

        return view('invoices.index', compact('invoices', 'stats', 'request'));
    }

    /**
     * Formulaire de création
     */
    public function create(Request $request)
    {
        $clients = Client::active()->orderBy('name')->get();
        $quote = null;
        $predefinedServices = PredefinedService::active()->ordered()->get();

        // Si création depuis un devis
        if ($request->filled('quote_id')) {
            $quote = Quote::with('items')->findOrFail($request->quote_id);
        }

        return view('invoices.create', compact('clients', 'quote', 'predefinedServices'));
    }

    /**
     * Enregistrer une nouvelle facture
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'due_date' => 'nullable|date|after:today',
            'payment_terms' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'internal_notes' => 'nullable|string',

            // Lignes
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tva_rate' => 'required|numeric|min:0|max:100',
        ], [
            'items.required' => 'Vous devez ajouter au moins une ligne à la facture.',
        ]);

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'client_id' => $validated['client_id'],
                'user_id' => Auth::id(),
                'status' => 'brouillon',
                'due_date' => $validated['due_date'] ?? now()->addDays(30),
                'payment_terms' => $validated['payment_terms'] ?? 'Paiement à 30 jours fin de mois',
                'discount_percentage' => $validated['discount_percentage'] ?? null,
                'discount_amount' => $validated['discount_amount'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
            ]);

            // Ajouter les lignes
            foreach ($validated['items'] as $index => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tva_rate' => $item['tva_rate'],
                    'sort_order' => $index,
                ]);
            }

            // Les totaux se calculent automatiquement via les événements du modèle
            $invoice->refresh();

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Facture créée avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    /**
     * Afficher une facture
     */
    public function show(Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserViewInvoice($user, $invoice)) {
            abort(403);
        }

        $invoice->load(['client', 'user', 'items', 'payments', 'quote']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditInvoice($user, $invoice)) {
            abort(403);
        }

        if (!$invoice->canBeEdited()) {
            return back()->with('error', 'Cette facture ne peut plus être modifiée (statut : ' . $invoice->status_label . ')');
        }

        $invoice->load('items');
        $clients = Client::active()->orderBy('name')->get();
        $predefinedServices = PredefinedService::active()->ordered()->get();

        return view('invoices.edit', compact('invoice', 'clients', 'predefinedServices'));
    }

    /**
     * Mettre à jour une facture
     */
    public function update(Request $request, Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditInvoice($user, $invoice)) {
            abort(403);
        }

        if (!$invoice->canBeEdited()) {
            return back()->with('error', 'Cette facture ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'due_date' => 'nullable|date',
            'payment_terms' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tva_rate' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $invoice->update([
                'client_id' => $validated['client_id'],
                'due_date' => $validated['due_date'],
                'payment_terms' => $validated['payment_terms'],
                'internal_notes' => $validated['internal_notes'],
            ]);

            // Supprimer les anciennes lignes
            $invoice->items()->delete();

            // Ajouter les nouvelles lignes
            foreach ($validated['items'] as $index => $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tva_rate' => $item['tva_rate'],
                    'sort_order' => $index,
                ]);
            }

            $invoice->refresh();

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Facture mise à jour avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Émettre la facture (passe de brouillon à émise)
     */
    public function issue(Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditInvoice($user, $invoice)) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            if ($invoice->issue()) {
                // NOUVEAU : Envoi automatique email avec PDF (CDC Section B)
                if ($invoice->client->email) {
                    try {
                        // Générer le PDF avec infos professionnelles
                        $invoice->load(['client', 'user', 'items', 'payments']);
                        $userInfo = $this->getUserProfessionalInfo($invoice->user); // ⬅️ AJOUT ICI

                        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'userInfo')) // ⬅️ AJOUT userInfo
                            ->setPaper('a4', 'portrait');
                        $pdfContent = $pdf->output();

                        // Envoyer l'email avec PDF en pièce jointe
                        Mail::to($invoice->client->email)
                            ->cc($invoice->user->email)
                            ->send(new \App\Mail\InvoiceSentMail($invoice, $pdfContent));

                        DB::commit();

                        return back()->with(
                            'success',
                            'Facture émise avec succès ! Email envoyé à ' . $invoice->client->email .
                                ' avec le PDF en pièce jointe. Numéro : ' . $invoice->invoice_number
                        );
                    } catch (\Exception $e) {
                        DB::commit();

                        return back()->with(
                            'warning',
                            'Facture émise (n° ' . $invoice->invoice_number . ') mais erreur d\'envoi email : ' .
                                $e->getMessage() . '. Veuillez vérifier votre configuration email.'
                        );
                    }
                }

                DB::commit();

                return back()->with(
                    'success',
                    'Facture émise avec succès ! Numéro : ' . $invoice->invoice_number .
                        ' (Le client n\'a pas d\'email renseigné)'
                );
            }

            DB::rollBack();
            return back()->with('error', 'Impossible d\'émettre cette facture.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Enregistrer un paiement
     */
    public function recordPayment(Request $request, Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditInvoice($user, $invoice)) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->remaining_amount,
            'payment_method' => 'required|in:especes,cheque,virement,carte,prelevement',
            'payment_reference' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $payment = InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'payment_date' => $validated['payment_date'] ?? now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // Vérifier si la facture est totalement payée
            $invoice->refresh();
            if ($invoice->isFullyPaid()) {
                $invoice->status = 'payee';
                $invoice->paid_at = now();
                $invoice->save();
            }

            return back()->with('success', 'Paiement enregistré avec succès ! Montant : ' . number_format($validated['amount'], 2, ',', ' ') . ' €');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Envoyer un rappel de paiement
     */
    public function sendReminder(Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditInvoice($user, $invoice)) {
            abort(403);
        }

        if ($invoice->sendPaymentReminder()) {
            return back()->with('success', 'Rappel de paiement envoyé au client.');
        }

        return back()->with('error', 'Impossible d\'envoyer le rappel.');
    }

    /**
     * Annuler une facture (admin uniquement)
     */
    public function cancel(Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403, 'Seuls les administrateurs peuvent annuler des factures.');
        }

        if ($invoice->cancel()) {
            return back()->with('success', 'Facture annulée avec succès.');
        }

        return back()->with('error', 'Impossible d\'annuler cette facture (déjà payée).');
    }

    /**
     * Historique de la facture
     */
    public function history(Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserViewInvoice($user, $invoice)) {
            abort(403);
        }

        $invoice->load(['payments' => function ($query) {
            $query->orderBy('payment_date', 'desc');
        }]);

        return view('invoices.history', compact('invoice'));
    }

    /**
     * Télécharger le PDF
     */
    public function downloadPdf(Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserViewInvoice($user, $invoice)) {
            abort(403);
        }

        try {
            $invoice->load(['client', 'user', 'items', 'payments']);
            $userInfo = $this->getUserProfessionalInfo($invoice->user); // ⬅️ AJOUT ICI

            $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'userInfo')) // ⬅️ AJOUT userInfo
                ->setPaper('a4', 'portrait');

            $filename = 'facture-' . $invoice->invoice_number . '-' . $invoice->client->display_name . '.pdf';
            $filename = $this->sanitizeFilename($filename);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
        }
    }

    /**
     * Visualiser le PDF
     */
    public function viewPdf(Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserViewInvoice($user, $invoice)) {
            abort(403);
        }

        try {
            $invoice->load(['client', 'user', 'items', 'payments']);
            $userInfo = $this->getUserProfessionalInfo($invoice->user); // ⬅️ AJOUT ICI

            $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'userInfo')) // ⬅️ AJOUT userInfo
                ->setPaper('a4', 'portrait');

            return $pdf->stream('facture-' . $invoice->invoice_number . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une facture (brouillon uniquement)
     */
    public function destroy(Invoice $invoice)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur() && $invoice->user_id !== $user->id) {
            abort(403);
        }

        if ($invoice->status !== 'brouillon') {
            return back()->with('error', 'Seules les factures en brouillon peuvent être supprimées.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Facture supprimée avec succès.');
    }

    /**
     * Récupérer les informations professionnelles de l'utilisateur
     */
    private function getUserProfessionalInfo(User $user): array
    {
        return [
            // Informations de base
            'full_name' => $user->full_name,
            'email' => $user->effective_email,
            'phone' => $user->effective_phone,

            // Informations professionnelles
            'rsac_number' => $user->rsac_number,
            'professional_address' => $user->professional_address,
            'professional_city' => $user->professional_city,
            'professional_postal_code' => $user->professional_postal_code,

            // Mentions et textes
            'legal_mentions' => $user->legal_mentions,
            'footer_text' => $user->footer_text,

            // Signature
            'signature_url' => $user->signature_url,
            'has_signature' => !empty($user->signature_image),
        ];
    }

    private function canUserViewInvoice($user, $invoice): bool
    {
        if ($user->isAdministrateur()) {
            return true;
        }

        if ($user->isManager()) {
            return $invoice->user_id === $user->id || $invoice->user->manager_id === $user->id;
        }

        return $invoice->user_id === $user->id;
    }

    private function canUserEditInvoice($user, $invoice): bool
    {
        if ($user->isAdministrateur()) {
            return true;
        }

        if ($user->isManager()) {
            return $invoice->user_id === $user->id || $invoice->user->manager_id === $user->id;
        }

        return $invoice->user_id === $user->id;
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename);
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '-', $filename);
        $filename = preg_replace('/-+/', '-', $filename);
        return $filename;
    }
}

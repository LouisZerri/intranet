<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Client;
use App\Mail\QuoteSentMail;
use App\Models\PredefinedService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Quote::forUser($user)->with(['client', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('user_id') && ($user->isManager() || $user->isAdministrateur())) {
            $query->where('user_id', $request->user_id);
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(15);
        $quotes->appends($request->all());

        $stats = [
            'total' => Quote::forUser($user)->count(),
            'brouillon' => Quote::forUser($user)->draft()->count(),
            'envoye' => Quote::forUser($user)->sent()->count(),
            'accepte' => Quote::forUser($user)->accepted()->count(),
            'converti' => Quote::forUser($user)->converted()->count(),
            'refuse' => Quote::forUser($user)->refused()->count(),
        ];

        $totalSent = $stats['envoye'] + $stats['accepte'] + $stats['converti'] + $stats['refuse'];
        $totalAccepted = $stats['accepte'] + $stats['converti'];
        $conversionRate = $totalSent > 0 ? round(($totalAccepted / $totalSent) * 100, 1) : 0;

        return view('quotes.index', compact('quotes', 'stats', 'conversionRate', 'request'));
    }

    public function create()
    {
        $clients = Client::active()->orderBy('name')->get();
        $predefinedServices = PredefinedService::active()->ordered()->get();

        return view('quotes.create', compact('clients', 'predefinedServices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'validity_date' => 'nullable|date|after:today',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'internal_notes' => 'nullable|string',
            'client_notes' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'delivery_terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tva_rate' => 'required|numeric|min:0|max:100',
        ], [
            'client_id.required' => 'Vous devez sélectionner un client.',
            'items.required' => 'Vous devez ajouter au moins une ligne au devis.',
            'items.*.description.required' => 'La description est obligatoire pour chaque ligne.',
            'items.*.quantity.required' => 'La quantité est obligatoire.',
            'items.*.unit_price.required' => 'Le prix unitaire est obligatoire.',
        ]);

        DB::beginTransaction();
        try {
            $quote = Quote::create([
                'quote_number' => Quote::generateQuoteNumber(),
                'client_id' => $validated['client_id'],
                'user_id' => Auth::id(),
                'status' => 'brouillon',
                'validity_date' => $validated['validity_date'] ?? null,
                'discount_percentage' => $validated['discount_percentage'] ?? null,
                'discount_amount' => $validated['discount_amount'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
                'client_notes' => $validated['client_notes'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? 'Paiement à 30 jours fin de mois',
                'delivery_terms' => $validated['delivery_terms'] ?? null,
            ]);

            foreach ($validated['items'] as $index => $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tva_rate' => $item['tva_rate'],
                    'sort_order' => $index,
                ]);
            }

            $quote->refresh();
            $quote->calculateTotals();
            $quote->save();

            DB::commit();

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Devis créé avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la création du devis : ' . $e->getMessage());
        }
    }

    public function show(Quote $quote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserViewQuote($user, $quote)) {
            abort(403, 'Vous n\'avez pas accès à ce devis.');
        }

        $quote->load(['client', 'user', 'items', 'mission', 'invoice']);

        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditQuote($user, $quote)) {
            abort(403);
        }

        if (!$quote->canBeEdited()) {
            return back()->with('error', 'Ce devis ne peut plus être modifié (statut : ' . $quote->status_label . ')');
        }

        $quote->load('items');
        $clients = Client::active()->orderBy('name')->get();
        $predefinedServices = PredefinedService::active()->ordered()->get();

        return view('quotes.edit', compact('quote', 'clients', 'predefinedServices'));
    }

    public function update(Request $request, Quote $quote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditQuote($user, $quote)) {
            abort(403);
        }

        if (!$quote->canBeEdited()) {
            return back()->with('error', 'Ce devis ne peut plus être modifié.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'validity_date' => 'nullable|date|after:today',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'internal_notes' => 'nullable|string',
            'client_notes' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'delivery_terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tva_rate' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $quote->update([
                'client_id' => $validated['client_id'],
                'validity_date' => $validated['validity_date'] ?? null,
                'discount_percentage' => $validated['discount_percentage'] ?? null,
                'discount_amount' => $validated['discount_amount'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
                'client_notes' => $validated['client_notes'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'delivery_terms' => $validated['delivery_terms'] ?? null,
            ]);

            $quote->items()->delete();

            foreach ($validated['items'] as $index => $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tva_rate' => $item['tva_rate'],
                    'sort_order' => $index,
                ]);
            }

            $quote->refresh();
            $quote->calculateTotals();
            $quote->save();

            DB::commit();

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Devis mis à jour avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    public function send(Quote $quote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditQuote($user, $quote)) {
            abort(403);
        }

        if ($quote->status !== 'brouillon') {
            return back()->with('error', 'Seuls les devis en brouillon peuvent être envoyés.');
        }

        if (!$quote->client->email) {
            return back()->with('error', 'Le client n\'a pas d\'adresse email renseignée.');
        }

        DB::beginTransaction();
        try {
            $quote->load(['client', 'user', 'items']);
            $userInfo = $this->getUserProfessionalInfo($quote->user);

            // ⬇️ OPTIMISATION DU PDF
            $pdf = Pdf::loadView('quotes.pdf', compact('quote', 'userInfo'))
                ->setPaper('a4', 'portrait')
                ->setOption('enable-local-file-access', true) // Important pour les images
                ->setOption('image-dpi', 96) // Réduire la qualité des images
                ->setOption('image-quality', 85); // Compression des images

            $pdfContent = $pdf->output();

            // ⬇️ VÉRIFIER LA TAILLE DU PDF
            $pdfSizeMB = strlen($pdfContent) / 1024 / 1024;

            if ($pdfSizeMB > 10) {
                return back()->with('error', 'Le PDF est trop volumineux (' . round($pdfSizeMB, 2) . ' MB). Veuillez réduire le nombre d\'éléments.');
            }

            $quote->status = 'envoye';
            $quote->sent_at = now();
            $quote->save();

            Mail::to($quote->client->email)
                ->cc($quote->user->email)
                ->send(new QuoteSentMail($quote, $pdfContent));

            DB::commit();

            return back()->with(
                'success',
                'Devis envoyé avec succès ! Email envoyé à ' . $quote->client->email
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with(
                'error',
                'Erreur lors de l\'envoi : ' . $e->getMessage()
            );
        }
    }

    public function accept(Quote $quote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditQuote($user, $quote)) {
            abort(403);
        }

        if ($quote->status !== 'envoye') {
            return back()->with('error', 'Seuls les devis envoyés peuvent être acceptés.');
        }

        DB::beginTransaction();
        try {
            $quote->signed_electronically = true;
            $quote->signature_date = now();

            if ($quote->accept()) {
                DB::commit();
                return back()->with(
                    'success',
                    'Devis accepté avec signature électronique ! Une mission a été créée automatiquement.'
                );
            }

            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'acceptation du devis.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function refuse(Quote $quote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditQuote($user, $quote)) {
            abort(403);
        }

        if ($quote->status !== 'envoye') {
            return back()->with('error', 'Seuls les devis envoyés peuvent être refusés.');
        }

        if ($quote->refuse()) {
            return back()->with('success', 'Devis marqué comme refusé.');
        }

        return back()->with('error', 'Erreur lors du refus du devis.');
    }

    public function convertToInvoice(Quote $quote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEditQuote($user, $quote)) {
            abort(403);
        }

        if (!$quote->canBeConverted()) {
            return back()->with('error', 'Ce devis ne peut pas être converti en facture.');
        }

        $invoice = $quote->convertToInvoice();

        if ($invoice) {
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Devis converti en facture avec succès !');
        }

        return back()->with('error', 'Erreur lors de la conversion en facture.');
    }

    public function destroy(Quote $quote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur() && $quote->user_id !== $user->id) {
            abort(403);
        }

        if ($quote->status !== 'brouillon') {
            return back()->with('error', 'Seuls les devis en brouillon peuvent être supprimés.');
        }

        $quote->delete();

        return redirect()->route('quotes.index')
            ->with('success', 'Devis supprimé avec succès.');
    }

    public function downloadPdf(Quote $quote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserViewQuote($user, $quote)) {
            abort(403);
        }

        try {
            $quote->load(['client', 'user', 'items']);
            $userInfo = $this->getUserProfessionalInfo($quote->user);

            $pdf = Pdf::loadView('quotes.pdf', compact('quote', 'userInfo'))
                ->setPaper('a4', 'portrait');

            $filename = 'devis-' . $quote->quote_number . '-' . $quote->client->display_name . '.pdf';
            $filename = $this->sanitizeFilename($filename);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
        }
    }

    public function viewPdf(Quote $quote)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserViewQuote($user, $quote)) {
            abort(403);
        }

        try {
            $quote->load(['client', 'user', 'items']);
            $userInfo = $this->getUserProfessionalInfo($quote->user);

            $pdf = Pdf::loadView('quotes.pdf', compact('quote', 'userInfo'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('devis-' . $quote->quote_number . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la visualisation du PDF : ' . $e->getMessage());
        }
    }

    public function getPredefinedServices(Request $request)
    {
        $category = $request->get('category');

        $query = PredefinedService::active()->ordered();

        if ($category) {
            $query->byCategory($category);
        }

        $services = $query->get();

        return response()->json($services);
    }

    // ===== Méthodes privées =====

    private function getUserProfessionalInfo(User $user): array
    {
        return [
            'full_name' => $user->full_name,
            'email' => $user->effective_email,
            'phone' => $user->effective_phone,
            'rsac_number' => $user->rsac_number,
            'professional_address' => $user->professional_address,
            'professional_city' => $user->professional_city,
            'professional_postal_code' => $user->professional_postal_code,
            'legal_mentions' => $user->legal_mentions,
            'footer_text' => $user->footer_text,
            'signature_url' => $user->signature_url,
            'has_signature' => !empty($user->signature_image),
        ];
    }

    private function canUserViewQuote($user, $quote): bool
    {
        if ($user->isAdministrateur()) {
            return true;
        }

        if ($user->isManager()) {
            return $quote->user_id === $user->id || $quote->user->manager_id === $user->id;
        }

        return $quote->user_id === $user->id;
    }

    private function canUserEditQuote($user, $quote): bool
    {
        if ($user->isAdministrateur()) {
            return true;
        }

        if ($user->isManager()) {
            return $quote->user_id === $user->id || $quote->user->manager_id === $user->id;
        }

        return $quote->user_id === $user->id;
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename);
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '-', $filename);
        $filename = preg_replace('/-+/', '-', $filename);
        return $filename;
    }
}

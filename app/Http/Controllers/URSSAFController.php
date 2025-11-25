<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class URSSAFController extends Controller
{
    /**
     * Formulaire de génération du récapitulatif URSSAF (CDC Section D)
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Années disponibles (depuis la première facture)
        $firstInvoiceYear = Invoice::where('user_id', $user->id)
            ->where('status', 'payee')
            ->min('paid_at');

        $startYear = $firstInvoiceYear ? Carbon::parse($firstInvoiceYear)->year : now()->year;
        $years = range(now()->year, $startYear);

        return view('urssaf.index', compact('years'));
    }

    /**
     * Générer le récapitulatif URSSAF (CDC Section D)
     */
    public function generate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'period_type' => 'required|in:month,quarter,year',
            'year' => 'required|integer|min:2020|max:' . (now()->year + 1),
            'month' => 'required_if:period_type,month|nullable|integer|min:1|max:12',
            'quarter' => 'required_if:period_type,quarter|nullable|integer|min:1|max:4',
        ], [
            'period_type.required' => 'Vous devez sélectionner un type de période.',
            'year.required' => 'L\'année est obligatoire.',
            'month.required_if' => 'Le mois est obligatoire pour une période mensuelle.',
            'quarter.required_if' => 'Le trimestre est obligatoire pour une période trimestrielle.',
        ]);

        // Calculer les dates de début et fin selon le type de période
        [$startDate, $endDate] = $this->calculatePeriodDates(
            $validated['period_type'],
            $validated['year'],
            $validated['month'] ?? null,
            $validated['quarter'] ?? null
        );

        // Générer le récapitulatif URSSAF
        $data = Invoice::getURSSAFRevenue($user, $startDate, $endDate);

        // Ajouter des infos supplémentaires
        $data['period_type'] = $validated['period_type'];
        $data['period_label'] = $this->getPeriodLabel($validated['period_type'], $validated);

        return view('urssaf.report', compact('data'));
    }

    /**
     * Afficher le rapport URSSAF (GET) - Alias de generate pour route GET
     */
    public function report(Request $request)
    {
        return $this->generate($request);
    }

    /**
     * Export PDF du récapitulatif URSSAF (CDC Section D)
     */
    public function exportPdf(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'period_type' => 'required|in:month,quarter,year',
            'year' => 'required|integer',
            'month' => 'nullable|integer',
            'quarter' => 'nullable|integer',
        ]);

        [$startDate, $endDate] = $this->calculatePeriodDates(
            $validated['period_type'],
            $validated['year'],
            $validated['month'] ?? null,
            $validated['quarter'] ?? null
        );

        $data = Invoice::getURSSAFRevenue($user, $startDate, $endDate);
        $data['period_type'] = $validated['period_type'];
        $data['period_label'] = $this->getPeriodLabel($validated['period_type'], $validated);

        // Ajouter les informations du mandataire
        $data['user_name'] = $user->full_name;
        $data['user_email'] = $user->email;
        $data['user_phone'] = $user->phone ?? 'Non renseigné';
        $data['user_siret'] = $user->siret ?? 'Non renseigné';
        $data['commission_rate'] = 0; // À adapter selon votre logique métier

        try {
            $pdf = Pdf::loadView('urssaf.pdf', compact('data'))
                ->setPaper('a4', 'portrait');

            $filename = 'URSSAF-' . $data['period_label'] . '-' . $user->full_name . '.pdf';
            $filename = $this->sanitizeFilename($filename);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
        }
    }

    /**
     * Export Excel du récapitulatif URSSAF (CDC Section D)
     */
    public function exportExcel(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'period_type' => 'required|in:month,quarter,year',
            'year' => 'required|integer',
            'month' => 'nullable|integer',
            'quarter' => 'nullable|integer',
        ]);

        [$startDate, $endDate] = $this->calculatePeriodDates(
            $validated['period_type'],
            $validated['year'],
            $validated['month'] ?? null,
            $validated['quarter'] ?? null
        );

        $data = Invoice::getURSSAFRevenue($user, $startDate, $endDate);
        $data['period_label'] = $this->getPeriodLabel($validated['period_type'], $validated);
        $data['user_name'] = $user->full_name;

        // Créer un export CSV simple
        $filename = 'URSSAF-' . $data['period_label'] . '-' . $user->full_name . '.csv';
        $filename = $this->sanitizeFilename($filename);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // En-tête du fichier
            fputcsv($file, ['RÉCAPITULATIF URSSAF - ' . $data['period_label']], ';');
            fputcsv($file, ['Mandataire : ' . $data['user_name']], ';');
            fputcsv($file, ['Généré le : ' . now()->format('d/m/Y à H:i')], ';');
            fputcsv($file, [], ';'); // Ligne vide

            // Résumé
            fputcsv($file, ['RÉSUMÉ'], ';');
            fputcsv($file, ['Nombre de factures', $data['invoice_count']], ';');
            fputcsv($file, ['Total HT', number_format($data['total_ht'], 2, ',', ' ') . ' €'], ';');
            fputcsv($file, ['Total TVA', number_format($data['total_tva'], 2, ',', ' ') . ' €'], ';');
            fputcsv($file, ['Total TTC', number_format($data['total_ttc'], 2, ',', ' ') . ' €'], ';');
            fputcsv($file, [], ';'); // Ligne vide

            // Détail des factures
            fputcsv($file, ['DÉTAIL DES FACTURES'], ';');
            fputcsv($file, [
                'N° Facture',
                'Client',
                'Date paiement',
                'Montant HT',
                'TVA',
                'Montant TTC'
            ], ';');

            foreach ($data['invoices'] as $invoice) {
                fputcsv($file, [
                    $invoice['invoice_number'],
                    $invoice['client_name'],
                    $invoice['paid_at'],
                    number_format($invoice['total_ht'], 2, ',', ' '),
                    number_format($invoice['total_tva'], 2, ',', ' '),
                    number_format($invoice['total_ttc'], 2, ',', ' '),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Vue pour tous les mandataires (pour admin/managers)
     */
    public function allMandataires(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur() && !$user->isManager()) {
            abort(403, 'Accès réservé aux managers et administrateurs.');
        }

        // Récupérer les mandataires
        if ($user->isAdministrateur()) {
            $mandataires = User::where('is_active', true)
                ->whereIn('role', ['collaborateur', 'manager'])
                ->get();
        } else {
            $mandataires = $user->subordinates()->where('is_active', true)->get();
        }

        $validated = $request->validate([
            'period_type' => 'nullable|in:month,quarter,year',
            'year' => 'nullable|integer',
            'month' => 'nullable|integer',
            'quarter' => 'nullable|integer',
        ]);

        // Par défaut : année en cours
        $periodType = $validated['period_type'] ?? 'year';
        $year = $validated['year'] ?? now()->year;
        $month = $validated['month'] ?? null;
        $quarter = $validated['quarter'] ?? null;

        [$startDate, $endDate] = $this->calculatePeriodDates(
            $periodType,
            $year,
            $month,
            $quarter
        );

        // Générer les données pour chaque mandataire
        $mandatairesData = [];
        foreach ($mandataires as $mandataire) {
            $data = Invoice::getURSSAFRevenue($mandataire, $startDate, $endDate);
            if ($data['total_ht'] > 0) {
                $mandatairesData[] = $data;
            }
        }

        // Trier par CA décroissant
        usort($mandatairesData, function ($a, $b) {
            return $b['total_ht'] <=> $a['total_ht'];
        });

        // Construire le tableau de paramètres pour getPeriodLabel
        $periodParams = [
            'year' => $year,
            'month' => $month,
            'quarter' => $quarter,
        ];

        $periodLabel = $this->getPeriodLabel($periodType, $periodParams);

        return view('urssaf.all-mandataires', compact('mandatairesData', 'periodLabel', 'mandataires', 'request'));
    }

    /**
     * Export PDF consolidé pour tous les mandataires (admin/manager)
     */
    public function exportAllMandatairesPdf(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur() && !$user->isManager()) {
            abort(403, 'Accès réservé aux managers et administrateurs.');
        }

        $validated = $request->validate([
            'period_type' => 'required|in:month,quarter,year',
            'year' => 'required|integer',
            'month' => 'nullable|integer',
            'quarter' => 'nullable|integer',
        ]);

        // Récupérer les mandataires
        if ($user->isAdministrateur()) {
            $mandataires = User::where('is_active', true)
                ->whereIn('role', ['collaborateur', 'manager'])
                ->get();
        } else {
            $mandataires = $user->subordinates()->where('is_active', true)->get();
        }

        $periodType = $validated['period_type'];
        $year = $validated['year'];
        $month = $validated['month'] ?? null;
        $quarter = $validated['quarter'] ?? null;

        [$startDate, $endDate] = $this->calculatePeriodDates(
            $periodType,
            $year,
            $month,
            $quarter
        );

        // Générer les données pour chaque mandataire
        $mandatairesData = [];
        $totalGlobal = [
            'total_ht' => 0,
            'total_tva' => 0,
            'total_ttc' => 0,
            'invoice_count' => 0,
        ];

        foreach ($mandataires as $mandataire) {
            $data = Invoice::getURSSAFRevenue($mandataire, $startDate, $endDate);
            if ($data['total_ht'] > 0) {
                $mandatairesData[] = $data;
                $totalGlobal['total_ht'] += $data['total_ht'];
                $totalGlobal['total_tva'] += $data['total_tva'];
                $totalGlobal['total_ttc'] += $data['total_ttc'];
                $totalGlobal['invoice_count'] += $data['invoice_count'];
            }
        }

        // Trier par CA décroissant
        usort($mandatairesData, function ($a, $b) {
            return $b['total_ht'] <=> $a['total_ht'];
        });

        // Construire le tableau de paramètres pour getPeriodLabel
        $periodParams = [
            'year' => $year,
            'month' => $month,
            'quarter' => $quarter,
        ];

        $periodLabel = $this->getPeriodLabel($periodType, $periodParams);

        try {
            $pdf = Pdf::loadView('urssaf.all-mandataires-pdf', compact('mandatairesData', 'totalGlobal', 'periodLabel'))
                ->setPaper('a4', 'landscape');

            $filename = 'URSSAF-Tous-Mandataires-' . $periodLabel . '.pdf';
            $filename = $this->sanitizeFilename($filename);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
        }
    }

    /**
     * Export Excel consolidé pour tous les mandataires
     */
    public function exportAllMandatairesExcel(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur() && !$user->isManager()) {
            abort(403, 'Accès réservé aux managers et administrateurs.');
        }

        $validated = $request->validate([
            'period_type' => 'required|in:month,quarter,year',
            'year' => 'required|integer',
            'month' => 'nullable|integer',
            'quarter' => 'nullable|integer',
        ]);

        // Récupérer les mandataires
        if ($user->isAdministrateur()) {
            $mandataires = User::where('is_active', true)
                ->whereIn('role', ['collaborateur', 'manager'])
                ->get();
        } else {
            $mandataires = $user->subordinates()->where('is_active', true)->get();
        }

        $periodType = $validated['period_type'];
        $year = $validated['year'];
        $month = $validated['month'] ?? null;
        $quarter = $validated['quarter'] ?? null;

        [$startDate, $endDate] = $this->calculatePeriodDates(
            $periodType,
            $year,
            $month,
            $quarter
        );

        // Construire le tableau de paramètres pour getPeriodLabel
        $periodParams = [
            'year' => $year,
            'month' => $month,
            'quarter' => $quarter,
        ];

        $periodLabel = $this->getPeriodLabel($periodType, $periodParams);

        // Créer un export CSV
        $filename = 'URSSAF-Tous-Mandataires-' . $periodLabel . '.csv';
        $filename = $this->sanitizeFilename($filename);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($mandataires, $startDate, $endDate, $periodLabel) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // En-tête du fichier
            fputcsv($file, ['RÉCAPITULATIF URSSAF - TOUS LES MANDATAIRES'], ';');
            fputcsv($file, ['Période : ' . $periodLabel], ';');
            fputcsv($file, ['Généré le : ' . now()->format('d/m/Y à H:i')], ';');
            fputcsv($file, [], ';');

            // En-têtes des colonnes
            fputcsv($file, [
                'Mandataire',
                'Email',
                'Téléphone',
                'SIRET',
                'Nb factures',
                'Total HT',
                'Total TVA',
                'Total TTC'
            ], ';');

            $totalGlobal = [
                'invoice_count' => 0,
                'total_ht' => 0,
                'total_tva' => 0,
                'total_ttc' => 0,
            ];

            foreach ($mandataires as $mandataire) {
                $data = Invoice::getURSSAFRevenue($mandataire, $startDate, $endDate);

                if ($data['total_ht'] > 0) {
                    fputcsv($file, [
                        $data['user_name'],
                        $mandataire->email,
                        $mandataire->phone ?? 'N/A',
                        $mandataire->siret ?? 'N/A',
                        $data['invoice_count'],
                        number_format($data['total_ht'], 2, ',', ' '),
                        number_format($data['total_tva'], 2, ',', ' '),
                        number_format($data['total_ttc'], 2, ',', ' '),
                    ], ';');

                    $totalGlobal['invoice_count'] += $data['invoice_count'];
                    $totalGlobal['total_ht'] += $data['total_ht'];
                    $totalGlobal['total_tva'] += $data['total_tva'];
                    $totalGlobal['total_ttc'] += $data['total_ttc'];
                }
            }

            // Ligne de total
            fputcsv($file, [], ';');
            fputcsv($file, [
                'TOTAL GLOBAL',
                '',
                '',
                '',
                $totalGlobal['invoice_count'],
                number_format($totalGlobal['total_ht'], 2, ',', ' '),
                number_format($totalGlobal['total_tva'], 2, ',', ' '),
                number_format($totalGlobal['total_ttc'], 2, ',', ' '),
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ===== Méthodes utilitaires =====

    /**
     * Calculer les dates de début et fin selon le type de période
     */
    private function calculatePeriodDates(string $periodType, int $year, ?int $month, ?int $quarter): array
    {
        switch ($periodType) {
            case 'month':
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                break;

            case 'quarter':
                $startMonth = ($quarter - 1) * 3 + 1;
                $startDate = Carbon::create($year, $startMonth, 1)->startOfMonth();
                $endDate = $startDate->copy()->addMonths(2)->endOfMonth();
                break;

            case 'year':
                $startDate = Carbon::create($year, 1, 1)->startOfYear();
                $endDate = $startDate->copy()->endOfYear();
                break;

            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }

        return [$startDate, $endDate];
    }

    /**
     * Obtenir le libellé de la période
     */
    private function getPeriodLabel(string $periodType, array $params): string
    {
        $year = $params['year'];

        switch ($periodType) {
            case 'month':
                $month = $params['month'];
                $monthName = Carbon::create($year, $month, 1)->locale('fr')->monthName;
                return ucfirst($monthName) . ' ' . $year;

            case 'quarter':
                $quarter = $params['quarter'];
                return 'T' . $quarter . ' ' . $year;

            case 'year':
                return 'Année ' . $year;

            default:
                return '';
        }
    }

    /**
     * Nettoyer le nom de fichier
     */
    private function sanitizeFilename(string $filename): string
    {
        $filename = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename);
        $filename = preg_replace('/[^A-Za-z0-9._-]/', '-', $filename);
        $filename = preg_replace('/-+/', '-', $filename);
        return $filename;
    }
}

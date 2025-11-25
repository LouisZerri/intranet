<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif URSSAF - Tous les Mandataires - {{ $periodLabel }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
        }

        .container {
            padding: 15px;
        }

        /* En-tête */
        .header {
            margin-bottom: 20px;
            border-bottom: 3px solid #2563EB;
            padding-bottom: 15px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-left, .header-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #2563EB;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 8pt;
            color: #666;
            line-height: 1.6;
        }

        .document-title {
            text-align: right;
            font-size: 20pt;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 3px;
        }

        .document-subtitle {
            text-align: right;
            font-size: 12pt;
            color: #2563EB;
            font-weight: bold;
        }

        .document-info {
            text-align: right;
            margin-top: 8px;
            font-size: 8pt;
            color: #666;
        }

        /* Période */
        .period-box {
            background-color: #FEF3C7;
            border: 2px solid #F59E0B;
            padding: 10px;
            margin: 15px 0;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            color: #92400E;
        }

        /* Résumé global */
        .summary-global {
            background-color: #DBEAFE;
            border: 2px solid #2563EB;
            padding: 15px;
            margin: 20px 0;
        }

        .summary-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1E40AF;
            margin-bottom: 10px;
            text-align: center;
        }

        .summary-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 5px;
        }

        .summary-label {
            font-size: 8pt;
            color: #1F2937;
            display: block;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 13pt;
            font-weight: bold;
            color: #1E40AF;
            display: block;
        }

        /* Ventilation par type */
        .ventilation-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
        }

        .ventilation-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 12px;
            text-align: center;
        }

        .ventilation-grid {
            display: table;
            width: 100%;
        }

        .ventilation-item {
            display: table-cell;
            width: 25%;
            padding: 8px;
            text-align: center;
        }

        .ventilation-box {
            padding: 10px;
            border-radius: 4px;
        }

        .ventilation-box-transaction {
            background-color: #DBEAFE;
            border-left: 4px solid #2563EB;
        }

        .ventilation-box-location {
            background-color: #D1FAE5;
            border-left: 4px solid #10B981;
        }

        .ventilation-box-syndic {
            background-color: #EDE9FE;
            border-left: 4px solid #8B5CF6;
        }

        .ventilation-box-autres {
            background-color: #F3F4F6;
            border-left: 4px solid #6B7280;
        }

        .ventilation-type-label {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .ventilation-type-value {
            font-size: 11pt;
            font-weight: bold;
        }

        .ventilation-type-count {
            font-size: 7pt;
            color: #6B7280;
            margin-top: 3px;
        }

        /* Tableau des mandataires */
        .mandataires-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 8pt;
        }

        .mandataires-table thead {
            background-color: #2563EB;
            color: white;
        }

        .mandataires-table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 8pt;
            font-weight: bold;
        }

        .mandataires-table tbody tr {
            border-bottom: 1px solid #E5E7EB;
        }

        .mandataires-table tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .mandataires-table tbody tr:hover {
            background-color: #EFF6FF;
        }

        .mandataires-table td {
            padding: 6px 5px;
            font-size: 8pt;
        }

        .mandataires-table .text-right {
            text-align: right;
        }

        .mandataires-table .text-center {
            text-align: center;
        }

        /* Ligne de total */
        .mandataires-table tfoot {
            background-color: #DBEAFE;
            font-weight: bold;
        }

        .mandataires-table tfoot td {
            padding: 10px 5px;
            font-size: 9pt;
            border-top: 2px solid #2563EB;
        }

        /* Top performers */
        .top-performers {
            background-color: #F0FDF4;
            border: 2px solid #10B981;
            padding: 15px;
            margin: 20px 0;
        }

        .top-performers-title {
            font-size: 11pt;
            font-weight: bold;
            color: #065F46;
            margin-bottom: 10px;
        }

        .top-performer-item {
            padding: 5px 0;
            border-bottom: 1px solid #D1FAE5;
            font-size: 9pt;
        }

        .top-performer-rank {
            display: inline-block;
            width: 30px;
            font-weight: bold;
            color: #059669;
        }

        .top-performer-name {
            display: inline-block;
            width: 200px;
        }

        .top-performer-amount {
            display: inline-block;
            font-weight: bold;
            color: #065F46;
        }

        /* Statistiques */
        .stats-box {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 12px;
            margin: 15px 0;
        }

        .stats-title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 8px;
            color: #92400E;
        }

        .stats-content {
            font-size: 8pt;
            color: #78350F;
            line-height: 1.5;
        }

        /* Notes */
        .notes-section {
            margin-top: 20px;
            padding: 12px;
            background-color: #FFFBEB;
            border-left: 4px solid #F59E0B;
        }

        .notes-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 6px;
            color: #92400E;
        }

        .notes-content {
            font-size: 8pt;
            color: #78350F;
            line-height: 1.4;
        }

        /* Pied de page */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            font-size: 7pt;
            color: #6B7280;
            text-align: center;
            line-height: 1.6;
        }

        .footer-highlight {
            font-weight: bold;
            color: #2563EB;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .badge-success {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-warning {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .badge-transaction {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .badge-location {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-syndic {
            background-color: #EDE9FE;
            color: #5B21B6;
        }

        .badge-autres {
            background-color: #F3F4F6;
            color: #374151;
        }

        /* Utilitaires */
        .text-bold { font-weight: bold; }
        .text-muted { color: #6B7280; }
        .mb-10 { margin-bottom: 10px; }
        .mt-20 { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        {{-- En-tête --}}
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="company-name">GEST'IMMO</div>
                    <div class="company-info">
                        35 Rue Aliénor d'Aquitaine<br>
                        19360 Malemort<br>
                        Tél : 06 13 25 05 96<br>
                        Email : contact@gestimmo-presta.fr<br>
                        SIRET : 99087741700016<br>
                        TVA : FR42990877417
                    </div>
                </div>
                <div class="header-right">
                    <div class="document-title">RÉCAPITULATIF URSSAF</div>
                    <div class="document-subtitle">Tous les Mandataires</div>
                    <div class="document-info">
                        Généré le {{ now()->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Période --}}
        <div class="period-box">
            📅 Période : {{ $periodLabel }}
        </div>

        {{-- Résumé global --}}
        <div class="summary-global">
            <div class="summary-title">💰 RÉSUMÉ GLOBAL</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Mandataires actifs</span>
                    <span class="summary-value">{{ count($mandatairesData) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Factures payées</span>
                    <span class="summary-value">{{ $totalGlobal['invoice_count'] }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">CA HT total</span>
                    <span class="summary-value">{{ number_format($totalGlobal['total_ht'], 0, ',', ' ') }} €</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">CA TTC total</span>
                    <span class="summary-value">{{ number_format($totalGlobal['total_ttc'], 0, ',', ' ') }} €</span>
                </div>
            </div>
        </div>

        {{-- Ventilation par type d'activité (NOUVEAU) --}}
        @php
            // Calculer les totaux par type à partir des données des mandataires
            $totalsByType = [
                'transaction' => ['total_ht' => 0, 'invoice_count' => 0],
                'location' => ['total_ht' => 0, 'invoice_count' => 0],
                'syndic' => ['total_ht' => 0, 'invoice_count' => 0],
                'autres' => ['total_ht' => 0, 'invoice_count' => 0],
            ];
            
            foreach($mandatairesData as $data) {
                if (isset($data['by_type'])) {
                    foreach (['transaction', 'location', 'syndic', 'autres'] as $type) {
                        $totalsByType[$type]['total_ht'] += $data['by_type'][$type]['total_ht'] ?? 0;
                        $totalsByType[$type]['invoice_count'] += $data['by_type'][$type]['invoice_count'] ?? 0;
                    }
                }
            }
        @endphp

        <div class="ventilation-section">
            <div class="ventilation-title">📊 VENTILATION PAR TYPE D'ACTIVITÉ</div>
            <div class="ventilation-grid">
                <div class="ventilation-item">
                    <div class="ventilation-box ventilation-box-transaction">
                        <div class="ventilation-type-label" style="color: #1E40AF;">🏠 Transaction</div>
                        <div class="ventilation-type-value" style="color: #1E40AF;">{{ number_format($totalsByType['transaction']['total_ht'], 0, ',', ' ') }} €</div>
                        <div class="ventilation-type-count">{{ $totalsByType['transaction']['invoice_count'] }} facture(s)</div>
                    </div>
                </div>
                <div class="ventilation-item">
                    <div class="ventilation-box ventilation-box-location">
                        <div class="ventilation-type-label" style="color: #065F46;">🔑 Location</div>
                        <div class="ventilation-type-value" style="color: #065F46;">{{ number_format($totalsByType['location']['total_ht'], 0, ',', ' ') }} €</div>
                        <div class="ventilation-type-count">{{ $totalsByType['location']['invoice_count'] }} facture(s)</div>
                    </div>
                </div>
                <div class="ventilation-item">
                    <div class="ventilation-box ventilation-box-syndic">
                        <div class="ventilation-type-label" style="color: #5B21B6;">🏢 Syndic</div>
                        <div class="ventilation-type-value" style="color: #5B21B6;">{{ number_format($totalsByType['syndic']['total_ht'], 0, ',', ' ') }} €</div>
                        <div class="ventilation-type-count">{{ $totalsByType['syndic']['invoice_count'] }} facture(s)</div>
                    </div>
                </div>
                <div class="ventilation-item">
                    <div class="ventilation-box ventilation-box-autres">
                        <div class="ventilation-type-label" style="color: #374151;">📋 Autres</div>
                        <div class="ventilation-type-value" style="color: #374151;">{{ number_format($totalsByType['autres']['total_ht'], 0, ',', ' ') }} €</div>
                        <div class="ventilation-type-count">{{ $totalsByType['autres']['invoice_count'] }} facture(s)</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistiques complémentaires --}}
        <div class="stats-box">
            <div class="stats-title">📊 Statistiques</div>
            <div class="stats-content">
                <strong>CA moyen par mandataire :</strong> 
                {{ count($mandatairesData) > 0 ? number_format($totalGlobal['total_ht'] / count($mandatairesData), 2, ',', ' ') : '0,00' }} € HT
                <br>
                <strong>Nombre moyen de factures par mandataire :</strong> 
                {{ count($mandatairesData) > 0 ? number_format($totalGlobal['invoice_count'] / count($mandatairesData), 1, ',', ' ') : '0' }}
                <br>
                <strong>TVA collectée totale :</strong> 
                {{ number_format($totalGlobal['total_tva'], 2, ',', ' ') }} €
            </div>
        </div>

        {{-- Top 5 performers --}}
        @if(count($mandatairesData) > 0)
            <div class="top-performers">
                <div class="top-performers-title">🏆 Top 5 des mandataires</div>
                @foreach(array_slice($mandatairesData, 0, 5) as $index => $data)
                    <div class="top-performer-item">
                        <span class="top-performer-rank">{{ $index + 1 }}.</span>
                        <span class="top-performer-name">{{ $data['user_name'] }}</span>
                        <span class="top-performer-amount">{{ number_format($data['total_ht'], 2, ',', ' ') }} € HT</span>
                        <span class="badge badge-success">{{ $data['invoice_count'] }} factures</span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Tableau détaillé des mandataires --}}
        <h3 style="margin-top: 20px; margin-bottom: 10px; color: #1F2937; font-size: 11pt;">📋 Détail par mandataire</h3>
        <table class="mandataires-table">
            <thead>
                <tr>
                    <th style="width: 3%;">#</th>
                    <th style="width: 18%;">Mandataire</th>
                    <th style="width: 6%;" class="text-center">Fact.</th>
                    <th style="width: 12%;" class="text-right">Total HT</th>
                    <th style="width: 10%;" class="text-right">TVA</th>
                    <th style="width: 12%;" class="text-right">Total TTC</th>
                    <th style="width: 10%;" class="text-right" title="Transaction">🏠 Trans.</th>
                    <th style="width: 10%;" class="text-right" title="Location">🔑 Loc.</th>
                    <th style="width: 10%;" class="text-right" title="Syndic">🏢 Synd.</th>
                    <th style="width: 9%;" class="text-right" title="Autres">📋 Autres</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mandatairesData as $index => $data)
                    @php
                        $transHT = $data['by_type']['transaction']['total_ht'] ?? 0;
                        $locHT = $data['by_type']['location']['total_ht'] ?? 0;
                        $syndHT = $data['by_type']['syndic']['total_ht'] ?? 0;
                        $autresHT = $data['by_type']['autres']['total_ht'] ?? 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td><strong>{{ $data['user_name'] }}</strong></td>
                        <td class="text-center">
                            <span class="badge badge-success">{{ $data['invoice_count'] }}</span>
                        </td>
                        <td class="text-right"><strong>{{ number_format($data['total_ht'], 2, ',', ' ') }} €</strong></td>
                        <td class="text-right">{{ number_format($data['total_tva'], 2, ',', ' ') }} €</td>
                        <td class="text-right"><strong>{{ number_format($data['total_ttc'], 2, ',', ' ') }} €</strong></td>
                        <td class="text-right" style="color: #1E40AF; font-size: 7pt;">
                            @if($transHT > 0)
                                {{ number_format($transHT, 0, ',', ' ') }} €
                            @else
                                <span style="color: #9CA3AF;">-</span>
                            @endif
                        </td>
                        <td class="text-right" style="color: #065F46; font-size: 7pt;">
                            @if($locHT > 0)
                                {{ number_format($locHT, 0, ',', ' ') }} €
                            @else
                                <span style="color: #9CA3AF;">-</span>
                            @endif
                        </td>
                        <td class="text-right" style="color: #5B21B6; font-size: 7pt;">
                            @if($syndHT > 0)
                                {{ number_format($syndHT, 0, ',', ' ') }} €
                            @else
                                <span style="color: #9CA3AF;">-</span>
                            @endif
                        </td>
                        <td class="text-right" style="color: #374151; font-size: 7pt;">
                            @if($autresHT > 0)
                                {{ number_format($autresHT, 0, ',', ' ') }} €
                            @else
                                <span style="color: #9CA3AF;">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>TOTAL :</strong></td>
                    <td class="text-center"><strong>{{ $totalGlobal['invoice_count'] }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalGlobal['total_ht'], 2, ',', ' ') }} €</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalGlobal['total_tva'], 2, ',', ' ') }} €</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalGlobal['total_ttc'], 2, ',', ' ') }} €</strong></td>
                    <td class="text-right" style="color: #1E40AF;"><strong>{{ number_format($totalsByType['transaction']['total_ht'], 0, ',', ' ') }} €</strong></td>
                    <td class="text-right" style="color: #065F46;"><strong>{{ number_format($totalsByType['location']['total_ht'], 0, ',', ' ') }} €</strong></td>
                    <td class="text-right" style="color: #5B21B6;"><strong>{{ number_format($totalsByType['syndic']['total_ht'], 0, ',', ' ') }} €</strong></td>
                    <td class="text-right" style="color: #374151;"><strong>{{ number_format($totalsByType['autres']['total_ht'], 0, ',', ' ') }} €</strong></td>
                </tr>
            </tfoot>
        </table>

        {{-- Répartition par tranches de CA --}}
        @php
            $tranches = [
                '0-5000' => ['min' => 0, 'max' => 5000, 'count' => 0],
                '5000-10000' => ['min' => 5000, 'max' => 10000, 'count' => 0],
                '10000-20000' => ['min' => 10000, 'max' => 20000, 'count' => 0],
                '20000+' => ['min' => 20000, 'max' => PHP_INT_MAX, 'count' => 0],
            ];
            
            foreach($mandatairesData as $data) {
                $ca = $data['total_ht'];
                if ($ca < 5000) {
                    $tranches['0-5000']['count']++;
                } elseif ($ca < 10000) {
                    $tranches['5000-10000']['count']++;
                } elseif ($ca < 20000) {
                    $tranches['10000-20000']['count']++;
                } else {
                    $tranches['20000+']['count']++;
                }
            }
        @endphp

        <div style="margin-top: 20px; background-color: #F3F4F6; padding: 12px; border-radius: 4px;">
            <div style="font-weight: bold; font-size: 10pt; margin-bottom: 8px; color: #1F2937;">
                📈 Répartition par tranche de CA HT
            </div>
            <div style="font-size: 8pt; color: #4B5563;">
                <strong>0 - 5 000 €</strong> : {{ $tranches['0-5000']['count'] }} mandataire(s)<br>
                <strong>5 000 - 10 000 €</strong> : {{ $tranches['5000-10000']['count'] }} mandataire(s)<br>
                <strong>10 000 - 20 000 €</strong> : {{ $tranches['10000-20000']['count'] }} mandataire(s)<br>
                <strong>Plus de 20 000 €</strong> : {{ $tranches['20000+']['count'] }} mandataire(s)
            </div>
        </div>

        {{-- Notes importantes --}}
        <div class="notes-section">
            <div class="notes-title">⚠️ Notes importantes</div>
            <div class="notes-content">
                • Ce document récapitule l'ensemble des revenus encaissés par tous les mandataires durant la période indiquée<br>
                • Les montants correspondent aux factures effectivement payées<br>
                • La ventilation par type (Transaction, Location, Syndic, Autres) permet une déclaration URSSAF précise<br>
                • Ce récapitulatif est à conserver pour la comptabilité générale et les déclarations URSSAF<br>
                • Document confidentiel - Usage interne uniquement<br>
                • Les données sont triées par CA décroissant
            </div>
        </div>

        {{-- Pied de page --}}
        <div class="footer">
            <p>
                <span class="footer-highlight">GEST'IMMO</span> - Micro-entreprise<br>
                35 Rue Aliénor d'Aquitaine, 19360 Malemort<br>
                SIRET : 99087741700016 - TVA : FR42990877417<br>
            </p>
            <p style="margin-top: 8px; font-size: 6pt;">
                Document généré automatiquement par le système de gestion GEST'IMMO<br>
                Ce document est confidentiel et réservé à un usage interne - {{ now()->format('d/m/Y à H:i') }}
            </p>
        </div>
    </div>
</body>
</html>
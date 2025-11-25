<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif URSSAF - {{ $data['period_label'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        /* En-tête */
        .header {
            margin-bottom: 25px;
            border-bottom: 3px solid #4F46E5;
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

        .document-title {
            text-align: right;
            font-size: 22pt;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 5px;
        }

        .document-subtitle {
            text-align: right;
            font-size: 14pt;
            color: #4F46E5;
            font-weight: bold;
        }

        .document-info {
            text-align: right;
            margin-top: 8px;
            font-size: 9pt;
            color: #666;
        }

        /* Infos mandataire */
        .mandataire-info {
            background-color: #F3F4F6;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #4F46E5;
        }

        .mandataire-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 8px;
        }

        .mandataire-details {
            font-size: 9pt;
            color: #4B5563;
            line-height: 1.6;
        }

        /* Période */
        .period-box {
            background-color: #FEF3C7;
            border: 2px solid #F59E0B;
            padding: 12px;
            margin: 15px 0;
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            color: #92400E;
        }

        /* Résumé */
        .summary-box {
            background-color: #EEF2FF;
            border: 2px solid #4F46E5;
            padding: 20px;
            margin: 20px 0;
        }

        .summary-title {
            font-size: 14pt;
            font-weight: bold;
            color: #3730A3;
            margin-bottom: 15px;
            text-align: center;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
        }

        .summary-label {
            font-size: 9pt;
            color: #4B5563;
            display: block;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16pt;
            font-weight: bold;
            color: #3730A3;
            display: block;
        }

        .summary-highlight {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #4F46E5;
            text-align: center;
        }

        .summary-highlight-label {
            font-size: 11pt;
            color: #4B5563;
            margin-bottom: 5px;
        }

        .summary-highlight-value {
            font-size: 24pt;
            font-weight: bold;
            color: #4F46E5;
        }

        /* Ventilation par type */
        .ventilation-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
        }

        .ventilation-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 15px;
            text-align: center;
        }

        .ventilation-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ventilation-table th {
            background-color: #374151;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
        }

        .ventilation-table td {
            padding: 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 9pt;
        }

        .ventilation-table tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .ventilation-table .text-right {
            text-align: right;
        }

        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
        }

        .type-transaction { background-color: #DBEAFE; color: #1E40AF; }
        .type-location { background-color: #D1FAE5; color: #065F46; }
        .type-syndic { background-color: #EDE9FE; color: #5B21B6; }
        .type-autres { background-color: #F3F4F6; color: #374151; }

        /* Tableau des factures */
        .invoices-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9pt;
        }

        .invoices-table thead {
            background-color: #4F46E5;
            color: white;
        }

        .invoices-table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }

        .invoices-table tbody tr {
            border-bottom: 1px solid #E5E7EB;
        }

        .invoices-table tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .invoices-table td {
            padding: 8px;
            font-size: 9pt;
        }

        .invoices-table .text-right {
            text-align: right;
        }

        .invoices-table .text-center {
            text-align: center;
        }

        .invoices-table tfoot {
            background-color: #EEF2FF;
            font-weight: bold;
        }

        .invoices-table tfoot td {
            padding: 10px 8px;
            border-top: 2px solid #4F46E5;
        }

        /* Notes */
        .notes-section {
            margin-top: 25px;
            padding: 15px;
            background-color: #FFFBEB;
            border-left: 4px solid #F59E0B;
        }

        .notes-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            color: #92400E;
        }

        .notes-content {
            font-size: 9pt;
            color: #78350F;
            line-height: 1.5;
        }

        /* Pied de page */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #E5E7EB;
            font-size: 8pt;
            color: #6B7280;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- En-tête --}}
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div style="font-size: 18pt; font-weight: bold; color: #4F46E5; margin-bottom: 5px;">GEST'IMMO</div>
                    <div style="font-size: 8pt; color: #666; line-height: 1.6;">
                        35 Rue Aliénor d'Aquitaine<br>
                        19360 Malemort<br>
                        Tél : 06 13 25 05 96<br>
                        Email : contact@gestimmo-presta.fr
                    </div>
                </div>
                <div class="header-right">
                    <div class="document-title">RÉCAPITULATIF URSSAF</div>
                    <div class="document-subtitle">{{ $data['period_label'] }}</div>
                    <div class="document-info">
                        Généré le {{ now()->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Informations mandataire --}}
        <div class="mandataire-info">
            <div class="mandataire-name">{{ $data['user_name'] }}</div>
            <div class="mandataire-details">
                Email : {{ $data['user_email'] }}<br>
                Téléphone : {{ $data['user_phone'] }}<br>
                SIRET : {{ $data['user_siret'] }}
            </div>
        </div>

        {{-- Période --}}
        <div class="period-box">
            📅 Période du {{ $data['period_start'] }} au {{ $data['period_end'] }}
        </div>

        {{-- Résumé des revenus --}}
        <div class="summary-box">
            <div class="summary-title">💰 RÉSUMÉ DES REVENUS ENCAISSÉS</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-label">Factures payées</span>
                    <span class="summary-value">{{ $data['invoice_count'] }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total HT</span>
                    <span class="summary-value">{{ number_format($data['total_ht'], 2, ',', ' ') }} €</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">TVA collectée</span>
                    <span class="summary-value">{{ number_format($data['total_tva'], 2, ',', ' ') }} €</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total TTC</span>
                    <span class="summary-value">{{ number_format($data['total_ttc'], 2, ',', ' ') }} €</span>
                </div>
            </div>
            <div class="summary-highlight">
                <div class="summary-highlight-label">Montant à déclarer à l'URSSAF (CA HT)</div>
                <div class="summary-highlight-value">{{ number_format($data['total_ht'], 2, ',', ' ') }} €</div>
            </div>
        </div>

        {{-- Ventilation par type d'activité --}}
        @if(isset($data['by_type']))
            <div class="ventilation-section">
                <div class="ventilation-title">📊 VENTILATION PAR TYPE D'ACTIVITÉ</div>
                <table class="ventilation-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Type d'activité</th>
                            <th style="width: 15%;" class="text-center">Nb Factures</th>
                            <th style="width: 20%;" class="text-right">CA HT</th>
                            <th style="width: 20%;" class="text-right">TVA</th>
                            <th style="width: 20%;" class="text-right">CA TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="type-badge type-transaction">🏠 Transaction</span></td>
                            <td class="text-center">{{ $data['by_type']['transaction']['invoice_count'] ?? 0 }}</td>
                            <td class="text-right"><strong>{{ number_format($data['by_type']['transaction']['total_ht'] ?? 0, 2, ',', ' ') }} €</strong></td>
                            <td class="text-right">{{ number_format($data['by_type']['transaction']['total_tva'] ?? 0, 2, ',', ' ') }} €</td>
                            <td class="text-right">{{ number_format($data['by_type']['transaction']['total_ttc'] ?? 0, 2, ',', ' ') }} €</td>
                        </tr>
                        <tr>
                            <td><span class="type-badge type-location">🔑 Location</span></td>
                            <td class="text-center">{{ $data['by_type']['location']['invoice_count'] ?? 0 }}</td>
                            <td class="text-right"><strong>{{ number_format($data['by_type']['location']['total_ht'] ?? 0, 2, ',', ' ') }} €</strong></td>
                            <td class="text-right">{{ number_format($data['by_type']['location']['total_tva'] ?? 0, 2, ',', ' ') }} €</td>
                            <td class="text-right">{{ number_format($data['by_type']['location']['total_ttc'] ?? 0, 2, ',', ' ') }} €</td>
                        </tr>
                        <tr>
                            <td><span class="type-badge type-syndic">🏢 Syndic</span></td>
                            <td class="text-center">{{ $data['by_type']['syndic']['invoice_count'] ?? 0 }}</td>
                            <td class="text-right"><strong>{{ number_format($data['by_type']['syndic']['total_ht'] ?? 0, 2, ',', ' ') }} €</strong></td>
                            <td class="text-right">{{ number_format($data['by_type']['syndic']['total_tva'] ?? 0, 2, ',', ' ') }} €</td>
                            <td class="text-right">{{ number_format($data['by_type']['syndic']['total_ttc'] ?? 0, 2, ',', ' ') }} €</td>
                        </tr>
                        <tr>
                            <td><span class="type-badge type-autres">📋 Autres</span></td>
                            <td class="text-center">{{ $data['by_type']['autres']['invoice_count'] ?? 0 }}</td>
                            <td class="text-right"><strong>{{ number_format($data['by_type']['autres']['total_ht'] ?? 0, 2, ',', ' ') }} €</strong></td>
                            <td class="text-right">{{ number_format($data['by_type']['autres']['total_tva'] ?? 0, 2, ',', ' ') }} €</td>
                            <td class="text-right">{{ number_format($data['by_type']['autres']['total_ttc'] ?? 0, 2, ',', ' ') }} €</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #EEF2FF;">
                            <td><strong>TOTAL</strong></td>
                            <td class="text-center"><strong>{{ $data['invoice_count'] }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($data['total_ht'], 2, ',', ' ') }} €</strong></td>
                            <td class="text-right"><strong>{{ number_format($data['total_tva'], 2, ',', ' ') }} €</strong></td>
                            <td class="text-right"><strong>{{ number_format($data['total_ttc'], 2, ',', ' ') }} €</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        {{-- Détail des factures --}}
        @if(count($data['invoices']) > 0)
            <h3 style="margin-top: 25px; margin-bottom: 10px; color: #1F2937; font-size: 12pt;">📋 Détail des factures payées</h3>
            <table class="invoices-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">N° Facture</th>
                        <th style="width: 25%;">Client</th>
                        <th style="width: 12%;" class="text-center">Type</th>
                        <th style="width: 12%;" class="text-center">Date paiement</th>
                        <th style="width: 12%;" class="text-right">HT</th>
                        <th style="width: 12%;" class="text-right">TVA</th>
                        <th style="width: 12%;" class="text-right">TTC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['invoices'] as $invoice)
                        @php
                            $typeClass = 'type-autres';
                            $revenueType = $invoice['revenue_type'] ?? 'autres';
                            if ($revenueType === 'transaction') $typeClass = 'type-transaction';
                            elseif ($revenueType === 'location') $typeClass = 'type-location';
                            elseif ($revenueType === 'syndic') $typeClass = 'type-syndic';
                        @endphp
                        <tr>
                            <td><strong>{{ $invoice['invoice_number'] }}</strong></td>
                            <td>{{ $invoice['client_name'] }}</td>
                            <td class="text-center">
                                <span class="type-badge {{ $typeClass }}">
                                    {{ $invoice['revenue_type_label'] ?? ucfirst($revenueType) }}
                                </span>
                            </td>
                            <td class="text-center">{{ $invoice['paid_at'] }}</td>
                            <td class="text-right">{{ number_format($invoice['total_ht'], 2, ',', ' ') }} €</td>
                            <td class="text-right">{{ number_format($invoice['total_tva'], 2, ',', ' ') }} €</td>
                            <td class="text-right"><strong>{{ number_format($invoice['total_ttc'], 2, ',', ' ') }} €</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>{{ number_format($data['total_ht'], 2, ',', ' ') }} €</strong></td>
                        <td class="text-right"><strong>{{ number_format($data['total_tva'], 2, ',', ' ') }} €</strong></td>
                        <td class="text-right"><strong>{{ number_format($data['total_ttc'], 2, ',', ' ') }} €</strong></td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div style="text-align: center; padding: 30px; background-color: #F3F4F6; margin: 20px 0;">
                <p style="font-size: 12pt; color: #6B7280;">Aucune facture payée sur cette période.</p>
            </div>
        @endif

        {{-- Notes importantes --}}
        <div class="notes-section">
            <div class="notes-title">⚠️ Notes importantes</div>
            <div class="notes-content">
                • Ce récapitulatif concerne uniquement les factures effectivement encaissées (statut "Payée")<br>
                • Le montant à déclarer à l'URSSAF correspond au CA HT encaissé<br>
                • La ventilation par type d'activité permet une déclaration précise selon les taux applicables<br>
                • Conservez ce document pour votre comptabilité et vos déclarations<br>
                • En cas de doute, consultez votre comptable ou l'URSSAF
            </div>
        </div>

        {{-- Pied de page --}}
        <div class="footer">
            <p>
                Document généré automatiquement par le système de gestion GEST'IMMO<br>
                Ce récapitulatif est un outil d'aide à la déclaration - Vérifiez les montants avant déclaration<br>
                {{ now()->format('d/m/Y à H:i') }}
            </p>
        </div>
    </div>
</body>
</html>
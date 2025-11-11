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
            margin-bottom: 30px;
            border-bottom: 3px solid #2563EB;
            padding-bottom: 20px;
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
            font-size: 20pt;
            font-weight: bold;
            color: #2563EB;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 9pt;
            color: #666;
            line-height: 1.6;
        }

        .document-title {
            text-align: right;
            font-size: 24pt;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 5px;
        }

        .document-subtitle {
            text-align: right;
            font-size: 14pt;
            color: #2563EB;
            font-weight: bold;
        }

        /* Informations mandataire */
        .mandataire-box {
            background-color: #EFF6FF;
            border: 2px solid #2563EB;
            padding: 15px;
            margin: 20px 0;
        }

        .mandataire-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 8px;
            color: #1E40AF;
        }

        .mandataire-info {
            font-size: 10pt;
            line-height: 1.6;
        }

        /* Période */
        .period-box {
            background-color: #FEF3C7;
            border: 2px solid #F59E0B;
            padding: 12px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            color: #92400E;
        }

        /* Tableau des factures */
        .invoices-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .invoices-table thead {
            background-color: #2563EB;
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

        /* Totaux */
        .summary-box {
            background-color: #F0F9FF;
            border: 2px solid #2563EB;
            padding: 20px;
            margin: 30px 0;
        }

        .summary-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1E40AF;
            margin-bottom: 15px;
            text-align: center;
        }

        .summary-row {
            display: table;
            width: 100%;
            padding: 8px 0;
            border-bottom: 1px solid #DBEAFE;
        }

        .summary-label {
            display: table-cell;
            font-size: 11pt;
            color: #1F2937;
            padding-right: 20px;
        }

        .summary-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            font-size: 11pt;
            color: #1E40AF;
        }

        .summary-final {
            background-color: #DBEAFE;
            padding: 12px;
            margin-top: 10px;
            border-radius: 4px;
        }

        .summary-final .summary-label {
            font-size: 13pt;
            font-weight: bold;
            color: #1F2937;
        }

        .summary-final .summary-value {
            font-size: 15pt;
            color: #1E40AF;
        }

        /* Détails commissions */
        .commission-box {
            background-color: #F0FDF4;
            border: 2px solid #10B981;
            padding: 15px;
            margin: 20px 0;
        }

        .commission-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            color: #065F46;
        }

        .commission-details {
            font-size: 9pt;
            color: #047857;
        }

        /* Notes */
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #FFFBEB;
            border-left: 4px solid #F59E0B;
        }

        .notes-title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 8px;
            color: #92400E;
        }

        .notes-content {
            font-size: 9pt;
            color: #78350F;
        }

        /* Pied de page */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #E5E7EB;
            font-size: 8pt;
            color: #6B7280;
            text-align: center;
            line-height: 1.6;
        }

        .footer-highlight {
            font-weight: bold;
            color: #2563EB;
        }

        /* Signature */
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            padding: 15px;
            border: 1px solid #D1D5DB;
            text-align: center;
        }

        .signature-label {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 40px;
            color: #1F2937;
        }

        .signature-line {
            border-top: 1px solid #9CA3AF;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 8pt;
            color: #6B7280;
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
                    <div class="document-subtitle">Déclaration Mandataire</div>
                    <div style="text-align: right; margin-top: 10px; font-size: 9pt; color: #666;">
                        Généré le {{ now()->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Période --}}
        <div class="period-box">
            📅 Période : {{ $data['period_label'] }}
        </div>

        {{-- Informations mandataire --}}
        <div class="mandataire-box">
            <div class="mandataire-title">👤 Mandataire</div>
            <div class="mandataire-info">
                <strong>{{ $data['user_name'] }}</strong><br>
                @if(isset($data['user_email']))
                    Email : {{ $data['user_email'] }}<br>
                @endif
                @if(isset($data['user_phone']))
                    Tél : {{ $data['user_phone'] }}<br>
                @endif
                SIRET : {{ $data['user_siret'] ?? 'Non renseigné' }}
            </div>
        </div>

        {{-- Résumé des revenus --}}
        <div class="summary-box">
            <div class="summary-title">💰 RÉSUMÉ DES REVENUS</div>
            
            <div class="summary-row">
                <div class="summary-label">Nombre de factures payées :</div>
                <div class="summary-value">{{ $data['invoice_count'] }}</div>
            </div>

            <div class="summary-row">
                <div class="summary-label">Chiffre d'affaires HT :</div>
                <div class="summary-value">{{ number_format($data['total_ht'], 2, ',', ' ') }} €</div>
            </div>

            <div class="summary-row">
                <div class="summary-label">TVA collectée :</div>
                <div class="summary-value">{{ number_format($data['total_tva'], 2, ',', ' ') }} €</div>
            </div>

            <div class="summary-row summary-final">
                <div class="summary-label">Total TTC encaissé :</div>
                <div class="summary-value">{{ number_format($data['total_ttc'], 2, ',', ' ') }} €</div>
            </div>
        </div>

        {{-- Détail des commissions (si applicable) --}}
        @if(isset($data['commission_rate']) && $data['commission_rate'] > 0)
            <div class="commission-box">
                <div class="commission-title">📊 Détail des commissions</div>
                <div class="commission-details">
                    <strong>Taux de commission :</strong> {{ $data['commission_rate'] }}%<br>
                    <strong>Base de calcul (CA HT) :</strong> {{ number_format($data['total_ht'], 2, ',', ' ') }} €<br>
                    <strong>Montant des commissions :</strong> {{ number_format($data['total_ht'] * $data['commission_rate'] / 100, 2, ',', ' ') }} €
                </div>
            </div>
        @endif

        {{-- Liste détaillée des factures --}}
        @if(count($data['invoices']) > 0)
            <h3 style="margin-top: 30px; margin-bottom: 15px; color: #1F2937;">📋 Détail des factures</h3>
            <table class="invoices-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">N° Facture</th>
                        <th style="width: 25%;">Client</th>
                        <th style="width: 12%;" class="text-center">Date paiement</th>
                        <th style="width: 12%;" class="text-right">Montant HT</th>
                        <th style="width: 12%;" class="text-right">TVA</th>
                        <th style="width: 12%;" class="text-right">Montant TTC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['invoices'] as $invoice)
                        <tr>
                            <td>{{ $invoice['invoice_number'] }}</td>
                            <td>{{ $invoice['client_name'] }}</td>
                            <td class="text-center">{{ $invoice['paid_at'] }}</td>
                            <td class="text-right">{{ number_format($invoice['total_ht'], 2, ',', ' ') }} €</td>
                            <td class="text-right">{{ number_format($invoice['total_tva'], 2, ',', ' ') }} €</td>
                            <td class="text-right">{{ number_format($invoice['total_ttc'], 2, ',', ' ') }} €</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Notes importantes --}}
        <div class="notes-section">
            <div class="notes-title">⚠️ Notes importantes</div>
            <div class="notes-content">
                • Ce document récapitule les revenus encaissés durant la période indiquée<br>
                • Les montants correspondent aux factures effectivement payées<br>
                • Ce récapitulatif est à conserver pour votre déclaration URSSAF<br>
                • Pour toute question, contactez votre service comptable
            </div>
        </div>

        {{-- Signatures --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">Le mandataire</div>
                <div class="signature-line">Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">GEST'IMMO</div>
                <div class="signature-line">Signature et cachet</div>
            </div>
        </div>

        {{-- Pied de page --}}
        <div class="footer">
            <p>
                <span class="footer-highlight">GEST'IMMO</span> - Micro-entreprise<br>
                35 Rue Aliénor d'Aquitaine, 19360 Malemort<br>
                SIRET : 99087741700016 - TVA : FR42990877417<br>
            </p>
            <p style="margin-top: 10px; font-size: 7pt;">
                Document généré automatiquement par le système de gestion GEST'IMMO<br>
                Ce document n'a pas valeur de facture
            </p>
        </div>
    </div>
</body>
</html>
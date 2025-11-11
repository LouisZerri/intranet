<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice->invoice_number }}</title>
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
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #4F46E5;
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

        .document-number {
            text-align: right;
            font-size: 14pt;
            color: #4F46E5;
            font-weight: bold;
        }

        .document-date {
            text-align: right;
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }

        /* Informations client */
        .parties {
            display: table;
            width: 100%;
            margin: 30px 0;
        }

        .party {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            padding: 15px;
        }

        .party-left {
            background-color: #F3F4F6;
        }

        .party-right {
            background-color: #EEF2FF;
        }

        .party-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            color: #1F2937;
        }

        .party-content {
            font-size: 9pt;
            line-height: 1.6;
        }

        .party-content strong {
            display: block;
            font-size: 10pt;
            margin-bottom: 3px;
        }

        /* Badge statut */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .status-draft {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-sent {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .status-accepted {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-refused {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        /* Tableau des lignes */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table thead {
            background-color: #4F46E5;
            color: white;
        }

        .items-table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #E5E7EB;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .items-table td {
            padding: 10px 8px;
            font-size: 9pt;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        /* Totaux */
        .totals {
            width: 50%;
            margin-left: auto;
            margin-top: 20px;
        }

        .total-row {
            display: table;
            width: 100%;
            padding: 8px 0;
        }

        .total-label {
            display: table-cell;
            text-align: right;
            padding-right: 20px;
            font-size: 10pt;
        }

        .total-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            font-size: 10pt;
        }

        .total-final {
            border-top: 2px solid #4F46E5;
            padding-top: 12px;
            margin-top: 8px;
        }

        .total-final .total-label {
            font-size: 12pt;
            font-weight: bold;
            color: #1F2937;
        }

        .total-final .total-value {
            font-size: 14pt;
            color: #4F46E5;
        }

        /* Notes et conditions */
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
            white-space: pre-line;
        }

        .conditions-section {
            margin-top: 15px;
            padding: 15px;
            background-color: #F3F4F6;
        }

        .conditions-title {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 8px;
            color: #1F2937;
        }

        .conditions-content {
            font-size: 9pt;
            color: #4B5563;
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
            color: #4F46E5;
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

        /* Validité */
        .validity-box {
            background-color: #FEF3C7;
            border: 2px solid #F59E0B;
            padding: 10px;
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            color: #92400E;
        }

        /* Utilitaires */
        .text-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #6B7280;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .mt-20 {
            margin-top: 20px;
        }
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
                        SIRET :  99087741700016<br>
                        TVA : FR42990877417
                    </div>
                </div>
                <div class="header-right">
                    <div class="document-title">FACTURE</div>
                    <div class="document-number">{{ $invoice->invoice_number }}</div>
                    <div class="document-date">
                        @if ($invoice->issued_at)
                            Date : {{ $invoice->issued_at->format('d/m/Y') }}
                        @else
                            Date de création : {{ $invoice->created_at->format('d/m/Y') }}
                        @endif
                        @if ($invoice->due_date)
                            <br>Échéance : {{ $invoice->due_date->format('d/m/Y') }}
                        @endif
                    </div>
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ strtoupper($invoice->status_label) }}
                    </span>
                    @if ($invoice->status === 'payee')
                        <div
                            style="margin-top: 10px; padding: 10px; background-color: #D1FAE5; border: 2px solid #059669; text-align: center; font-weight: bold; color: #065F46;">
                            ✅ FACTURE PAYÉE
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Informations parties --}}
        <div class="parties">
            <div class="party party-left">
                <div class="party-title">Émetteur</div>
                <div class="party-content">
                    <strong>{{ $invoice->user->full_name }}</strong>
                    {{ $invoice->user->email }}<br>
                    @if ($invoice->user->phone)
                        Tél : {{ $invoice->user->phone }}
                    @endif
                </div>
            </div>
            <div class="party party-right">
                <div class="party-title">Client</div>
                <div class="party-content">
                    <strong>{{ $invoice->client->display_name }}</strong>
                    @if ($invoice->client->type === 'professionnel' && $invoice->client->company_name)
                        {{ $invoice->client->company_name }}<br>
                    @endif
                    @if ($invoice->client->address)
                        {{ $invoice->client->address }}<br>
                    @endif
                    @if ($invoice->client->postal_code || $invoice->client->city)
                        {{ $invoice->client->postal_code }} {{ $invoice->client->city }}<br>
                    @endif
                    @if ($invoice->client->email)
                        {{ $invoice->client->email }}<br>
                    @endif
                    @if ($invoice->client->phone)
                        Tél : {{ $invoice->client->phone }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Objet --}}
        @if ($invoice->quote)
            <div class="mb-10">
                <strong>Objet :</strong> {{ $invoice->quote->service_label }}
            </div>
        @endif

        {{-- Échéance --}}
        @if ($invoice->due_date && $invoice->status !== 'payee')
            <div class="validity-box">
                ⚠️ Date d'échéance : {{ $invoice->due_date->format('d/m/Y') }}
            </div>
        @endif

        {{-- Tableau des prestations --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 10%;" class="text-center">Qté</th>
                    <th style="width: 15%;" class="text-right">Prix Unit. HT</th>
                    <th style="width: 10%;" class="text-center">TVA</th>
                    <th style="width: 15%;" class="text-right">Total TTC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                        <td class="text-center">{{ $item->tva_rate }}%</td>
                        <td class="text-right">{{ $item->formatted_total_ttc }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totaux --}}
        <div class="totals">
            <div class="total-row">
                <div class="total-label">Total HT :</div>
                <div class="total-value">{{ $invoice->formatted_total_ht }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">Total TVA :</div>
                <div class="total-value">{{ number_format($invoice->total_tva, 2, ',', ' ') }} €</div>
            </div>
            <div class="total-row total-final">
                <div class="total-label">Total TTC :</div>
                <div class="total-value">{{ $invoice->formatted_total_ttc }}</div>
            </div>
            @if ($invoice->payments->count() > 0)
                <div class="total-row" style="background-color: #D1FAE5; padding: 8px 0; margin-top: 8px;">
                    <div class="total-label" style="color: #065F46;">Montant payé :</div>
                    <div class="total-value" style="color: #065F46;">
                        {{ number_format($invoice->payments->sum('amount'), 2, ',', ' ') }} €</div>
                </div>
                @if ($invoice->remaining_amount > 0)
                    <div class="total-row" style="background-color: #FEE2E2; padding: 8px 0;">
                        <div class="total-label" style="color: #991B1B; font-weight: bold;">Restant dû :</div>
                        <div class="total-value" style="color: #991B1B; font-weight: bold;">
                            {{ $invoice->formatted_remaining_amount }}</div>
                    </div>
                @endif
            @endif
        </div>

        {{-- Historique des paiements --}}
        @if ($invoice->payments->count() > 0)
            <div style="margin-top: 30px; padding: 15px; background-color: #F0FDF4; border-left: 4px solid #10B981;">
                <div style="font-weight: bold; font-size: 10pt; margin-bottom: 8px; color: #065F46;">
                    💳 Paiements reçus
                </div>
                <table style="width: 100%; font-size: 9pt;">
                    @foreach ($invoice->payments()->orderBy('payment_date')->get() as $payment)
                        <tr>
                            <td style="padding: 4px 0; color: #059669;">{{ $payment->payment_date->format('d/m/Y') }}
                            </td>
                            <td style="padding: 4px 0; color: #059669;">{{ $payment->payment_method_label }}</td>
                            <td style="padding: 4px 0; text-align: right; font-weight: bold; color: #065F46;">
                                {{ $payment->formatted_amount }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        {{-- Notes internes (ne pas afficher) --}}

        {{-- Conditions de paiement --}}
        @if ($invoice->payment_terms)
            <div class="conditions-section">
                <div class="conditions-title">💰 Conditions de paiement</div>
                <div class="conditions-content">{{ $invoice->payment_terms }}</div>
            </div>
        @endif

        {{-- Signatures uniquement pour factures émises non payées --}}
        @if ($invoice->status === 'emise' || $invoice->status === 'en_retard')
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-label">Le prestataire</div>
                    <div class="signature-line">Signature et cachet</div>
                </div>
                <div class="signature-box">
                    <div class="signature-label">Le client<br>(Pour réception et règlement)</div>
                    <div class="signature-line">Signature</div>
                </div>
            </div>
        @endif

        {{-- Pied de page avec mentions légales --}}
        <div class="footer">
            <p>
                <span class="footer-highlight">VOTRE ENTREPRISE</span> - SARL au capital de 10 000 € - RCS Paris 123 456
                789<br>
                Siège social : 123 Rue de l'Exemple, 75001 PARIS<br>
                TVA intracommunautaire : FR12345678900 - APE : 6202A<br>
                N° de déclaration d'activité : 11 75 12345 75 (ne vaut pas agrément de l'État)<br>
            </p>
            <p style="margin-top: 10px; font-size: 7pt;">
                Conformément à la loi "Informatique et Libertés", vous disposez d'un droit d'accès et de rectification
                des données vous concernant.<br>
                Encas de litige, seuls les tribunaux de Paris seront compétents.
            </p>
        </div>
    </div>
</body>

</html>

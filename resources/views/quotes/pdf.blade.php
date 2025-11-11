<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis {{ $quote->quote_number }}</title>
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
                        SIRET : 99087741700016<br>
                        TVA : FR42990877417
                    </div>
                </div>
                <div class="header-right">
                    <div class="document-title">DEVIS</div>
                    <div class="document-number">{{ $quote->quote_number }}</div>
                    <div class="document-date">
                        Date : {{ $quote->created_at->format('d/m/Y') }}
                        @if ($quote->validity_date)
                            <br>Valable jusqu'au : {{ $quote->validity_date->format('d/m/Y') }}
                        @endif
                    </div>
                    <span class="status-badge status-{{ $quote->status }}">
                        {{ strtoupper($quote->status_label) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Informations parties --}}
        <div class="parties">
            <div class="party party-left">
                <div class="party-title">Émetteur</div>
                <div class="party-content">
                    <strong>{{ $quote->user->full_name }}</strong>
                    {{ $quote->user->email }}<br>
                    @if ($quote->user->phone)
                        Tél : {{ $quote->user->phone }}
                    @endif
                </div>
            </div>
            <div class="party party-right">
                <div class="party-title">Client</div>
                <div class="party-content">
                    <strong>{{ $quote->client->display_name }}</strong>
                    @if ($quote->client->type === 'professionnel' && $quote->client->company_name)
                        {{ $quote->client->company_name }}<br>
                    @endif
                    @if ($quote->client->address)
                        {{ $quote->client->address }}<br>
                    @endif
                    @if ($quote->client->postal_code || $quote->client->city)
                        {{ $quote->client->postal_code }} {{ $quote->client->city }}<br>
                    @endif
                    @if ($quote->client->email)
                        {{ $quote->client->email }}<br>
                    @endif
                    @if ($quote->client->phone)
                        Tél : {{ $quote->client->phone }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Objet --}}
        <div class="mb-10">
            <strong>Objet :</strong> {{ $quote->service_label }}
        </div>

        {{-- Validité --}}
        @if ($quote->validity_date && $quote->status === 'envoye')
            <div class="validity-box">
                ⚠️ Ce devis est valable jusqu'au {{ $quote->validity_date->format('d/m/Y') }}
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
                @foreach ($quote->items as $item)
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
                <div class="total-value">{{ $quote->formatted_total_ht }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">Total TVA :</div>
                <div class="total-value">{{ $quote->formatted_total_tva }}</div>
            </div>
            <div class="total-row total-final">
                <div class="total-label">Total TTC :</div>
                <div class="total-value">{{ $quote->formatted_total_ttc }}</div>
            </div>
        </div>

        {{-- Notes client --}}
        @if ($quote->client_notes)
            <div class="notes-section">
                <div class="notes-title">📝 Notes</div>
                <div class="notes-content">{{ $quote->client_notes }}</div>
            </div>
        @endif

        {{-- Conditions de paiement --}}
        @if ($quote->payment_terms)
            <div class="conditions-section">
                <div class="conditions-title">💰 Conditions de paiement</div>
                <div class="conditions-content">{{ $quote->payment_terms }}</div>
            </div>
        @endif

        {{-- Conditions de livraison --}}
        @if ($quote->delivery_terms)
            <div class="conditions-section">
                <div class="conditions-title">🚚 Conditions de livraison</div>
                <div class="conditions-content">{{ $quote->delivery_terms }}</div>
            </div>
        @endif

        {{-- Signatures --}}
        @if ($quote->status === 'envoye')
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-label">Le prestataire</div>
                    <div class="signature-line">Signature et cachet</div>
                </div>
                <div class="signature-box">
                    <div class="signature-label">Le client<br>(Bon pour accord)</div>
                    <div class="signature-line">Signature précédée de "Bon pour accord"</div>
                </div>
            </div>
        @endif

        {{-- Pied de page avec mentions légales --}}
        <div class="footer">
            <p>
                <span class="footer-highlight">GEST'IMMO</span> - Micro-entreprise<br>
                35 Rue Aliénor d'Aquitaine, 19360 Malemort<br>
                SIRET : 99087741700016 - TVA : FR42990877417<br>
            </p>
            <p style="margin-top: 10px; font-size: 7pt;">
                Conformément à la loi "Informatique et Libertés", vous disposez d'un droit d'accès et de rectification
                des données vous concernant.<br>
                En cas de litige, seuls les tribunaux compétents de Tulle seront saisis.
            </p>
        </div>
    </div>
</body>

</html>

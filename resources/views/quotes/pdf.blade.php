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

        /* Signature */
        .signature-section {
            margin-top: 40px;
            text-align: right;
        }

        .signature-image {
            max-width: 200px;
            max-height: 80px;
            margin-bottom: 10px;
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
                    {{-- Logo GEST'IMMO --}}
                    @php
                        $logoPath = public_path('images/logo3d.png');
                        $logoExists = file_exists($logoPath);
                    @endphp

                    @if ($logoExists)
                        <div style="margin-bottom: 15px;">
                            <img src="{{ $logoPath }}" alt="GEST'IMMO" style="max-width: 150px; max-height: 60px;">
                        </div>
                    @endif
                    <div class="company-name">{{ $userInfo['full_name'] }}</div>
                    <div class="company-info">
                        @if ($userInfo['professional_address'])
                            {{ $userInfo['professional_address'] }}<br>
                        @endif
                        @if ($userInfo['professional_postal_code'] || $userInfo['professional_city'])
                            {{ $userInfo['professional_postal_code'] }} {{ $userInfo['professional_city'] }}<br>
                        @endif
                        @if ($userInfo['phone'])
                            Tél : {{ $userInfo['phone'] }}<br>
                        @endif
                        @if ($userInfo['email'])
                            Email : {{ $userInfo['email'] }}<br>
                        @endif
                        @if ($userInfo['rsac_number'])
                            RSAC : {{ $userInfo['rsac_number'] }}<br>
                        @endif
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
                </div>
            </div>
        </div>

        {{-- Informations parties --}}
        <div class="parties">
            <div class="party party-left">
                <div class="party-title">Émetteur</div>
                <div class="party-content">
                    <strong>{{ $userInfo['full_name'] }}</strong>
                    @if ($userInfo['email'])
                        {{ $userInfo['email'] }}<br>
                    @endif
                    @if ($userInfo['phone'])
                        Tél : {{ $userInfo['phone'] }}
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
                        <td>
                            @if (strpos($item->description, "\n") !== false)
                                {!! nl2br(e($item->description)) !!}
                            @else
                                {{ $item->description }}
                            @endif
                        </td>
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

        {{-- Signature --}}
        @if ($userInfo['has_signature'] && $userInfo['signature_url'])
            <div class="signature-section">
                <img src="{{ $userInfo['signature_url'] }}" class="signature-image" alt="Signature">
                <div style="font-size: 9pt; color: #666;">{{ $userInfo['full_name'] }}</div>
            </div>
        @endif

        {{-- Texte de pied de page personnalisé --}}
        @if ($userInfo['footer_text'])
            <div
                style="margin-top: 30px; padding: 15px; background-color: #EEF2FF; text-align: center; font-style: italic; font-size: 9pt; color: #4F46E5;">
                {{ $userInfo['footer_text'] }}
            </div>
        @endif

        {{-- Pied de page avec mentions légales --}}
        <div class="footer">
            @if ($userInfo['legal_mentions'])
                <p style="white-space: pre-line;">{{ $userInfo['legal_mentions'] }}</p>
            @else
                <p>
                    <span class="footer-highlight">{{ $userInfo['full_name'] }}</span><br>
                    @if ($userInfo['professional_address'])
                        {{ $userInfo['professional_address'] }},
                        {{ $userInfo['professional_postal_code'] }} {{ $userInfo['professional_city'] }}<br>
                    @endif
                    @if ($userInfo['rsac_number'])
                        RSAC : {{ $userInfo['rsac_number'] }}<br>
                    @endif
                </p>
            @endif
            <p style="margin-top: 10px; font-size: 7pt;">
                Conformément à la loi "Informatique et Libertés", vous disposez d'un droit d'accès et de rectification
                des données vous concernant.
            </p>
        </div>
    </div>
</body>

</html>

@component('mail::message')
# Nouvelle facture

Bonjour **{{ $invoice->client->display_name }}**,

Nous vous adressons ci-joint notre facture **{{ $invoice->invoice_number }}** pour les prestations suivantes :

@if($invoice->quote)
## 📋 {{ $invoice->quote->service_label }}
@endif

### Détails financiers

<table style="width: 100%; margin: 20px 0;">
<tr>
    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">Montant HT</td>
    <td style="padding: 8px; text-align: right; border-bottom: 1px solid #e5e7eb; font-weight: bold;">{{ $invoice->formatted_total_ht }}</td>
</tr>
<tr>
    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">TVA</td>
    <td style="padding: 8px; text-align: right; border-bottom: 1px solid #e5e7eb; font-weight: bold;">{{ number_format($invoice->total_tva, 2, ',', ' ') }} €</td>
</tr>
<tr style="background-color: #f3f4f6;">
    <td style="padding: 8px; font-weight: bold; font-size: 16px;">Montant TTC</td>
    <td style="padding: 8px; text-align: right; font-weight: bold; font-size: 18px; color: #4F46E5;">{{ $invoice->formatted_total_ttc }}</td>
</tr>
</table>

@if($invoice->due_date)
@component('mail::panel')
⚠️ **Date d'échéance : {{ $invoice->due_date->format('d/m/Y') }}**
@endcomponent
@endif

@component('mail::button', ['url' => route('invoices.show', $invoice), 'color' => 'success'])
💰 Voir la facture en ligne
@endcomponent

Le PDF de la facture est joint à cet email pour votre commodité.

@if($invoice->payment_terms)
**Conditions de paiement :** {{ $invoice->payment_terms }}
@endif

---

### Coordonnées bancaires pour le règlement

**IBAN :** FR76 XXXX XXXX XXXX XXXX XXXX XXX  
**BIC :** XXXXXXXXX  
**Référence à indiquer :** {{ $invoice->invoice_number }}

---

Pour toute question concernant cette facture, n'hésitez pas à nous contacter.

Cordialement,

**{{ $invoice->user->full_name }}**  
{{ $invoice->user->email }}  
@if($invoice->user->phone)
{{ $invoice->user->phone }}
@endif

---

<small style="color: #6b7280;">
*Cet email a été envoyé automatiquement par notre système de gestion. Pour toute réponse, merci de contacter directement {{ $invoice->user->full_name }}.*
</small>

Merci,<br>
{{ config('app.name') }}
@endcomponent
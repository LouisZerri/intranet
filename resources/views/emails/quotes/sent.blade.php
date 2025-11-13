@component('mail::message')
# Nouveau devis

Bonjour **{{ $quote->client->display_name }}**,

Nous vous adressons ci-joint notre devis **{{ $quote->quote_number }}** pour la prestation suivante :

## 📋 {{ $quote->service_label }}

### Détails financiers

<table style="width: 100%; margin: 20px 0;">
<tr>
    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">Montant HT</td>
    <td style="padding: 8px; text-align: right; border-bottom: 1px solid #e5e7eb; font-weight: bold;">{{ $quote->formatted_total_ht }}</td>
</tr>
<tr>
    <td style="padding: 8px; border-bottom: 1px solid #e5e7eb;">TVA</td>
    <td style="padding: 8px; text-align: right; border-bottom: 1px solid #e5e7eb; font-weight: bold;">{{ $quote->formatted_total_tva }}</td>
</tr>
<tr style="background-color: #f3f4f6;">
    <td style="padding: 8px; font-weight: bold; font-size: 16px;">Montant TTC</td>
    <td style="padding: 8px; text-align: right; font-weight: bold; font-size: 18px; color: #4F46E5;">{{ $quote->formatted_total_ttc }}</td>
</tr>
</table>

@if($quote->validity_date)
@component('mail::panel')
⚠️ **Ce devis est valable jusqu'au {{ $quote->validity_date->format('d/m/Y') }}**
@endcomponent
@endif

Le PDF du devis est joint à cet email pour votre commodité.

@if($quote->client_notes)
---

**Notes :**

{{ $quote->client_notes }}
@endif

@if($quote->payment_terms)
**Conditions de paiement :** {{ $quote->payment_terms }}
@endif

@if($quote->delivery_terms)
**Conditions de livraison :** {{ $quote->delivery_terms }}
@endif

---

Pour toute question ou précision concernant ce devis, n'hésitez pas à nous contacter.

Merci pour votre confiance, l’équipe GEST’IMMO.

---

<small style="color: #6b7280;">
*Cet email a été envoyé automatiquement par notre système de gestion. Pour toute réponse, merci de contacter directement {{ $quote->user->full_name }}.*
</small>

@endcomponent
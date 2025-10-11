<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle commande de communication</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .order-info table {
            width: 100%;
        }
        .order-info td {
            padding: 8px 0;
        }
        .order-info td:first-child {
            font-weight: 600;
            color: #555;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .products-table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .products-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        .products-table tr:last-child td {
            border-bottom: none;
        }
        .total-row {
            background: #f8f9fa;
            font-weight: bold;
            font-size: 16px;
        }
        .total-row td {
            padding: 15px 12px !important;
        }
        .notes {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Nouvelle Commande de Communication</h1>
        </div>

        <div class="content">
            <p>Bonjour,</p>
            <p>Une nouvelle commande de produits de communication a été passée.</p>

            <div class="order-info">
                <table>
                    <tr>
                        <td>Numéro de commande:</td>
                        <td><strong>{{ $order->order_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>Date de commande:</td>
                        <td>{{ $order->ordered_at->format('d/m/Y à H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Commandé par:</td>
                        <td>{{ $order->user->full_name }}</td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td>{{ $order->user->email }}</td>
                    </tr>
                    <tr>
                        <td>Département:</td>
                        <td>{{ $order->user->department }}</td>
                    </tr>
                    <tr>
                        <td>Téléphone:</td>
                        <td>{{ $order->user->phone ?? 'Non renseigné' }}</td>
                    </tr>
                </table>
            </div>

            <h2 style="color: #667eea; margin-top: 30px;">Détail de la commande</h2>

            <table class="products-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th style="text-align: center;">Qté</th>
                        <th style="text-align: right;">Prix unitaire</th>
                        <th style="text-align: right;">Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                            @if($item->product->reference)
                                <br><small style="color: #666;">Réf: {{ $item->product->reference }}</small>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                        <td style="text-align: right;">{{ number_format($item->subtotal, 2, ',', ' ') }} €</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">TOTAL</td>
                        <td style="text-align: right; color: #667eea;">{{ number_format($order->total_amount, 2, ',', ' ') }} €</td>
                    </tr>
                </tbody>
            </table>

            @if($order->notes)
            <div class="notes">
                <strong>📝 Commentaires:</strong><br>
                {{ $order->notes }}
            </div>
            @endif

            <p style="margin-top: 30px;">
                Merci de traiter cette commande dans les meilleurs délais.
            </p>
        </div>

        <div class="footer">
            <p>Cet email a été généré automatiquement par l'intranet.</p>
            <p>Pour toute question, contactez {{ $order->user->full_name }} à {{ $order->user->email }}</p>
        </div>
    </div>
</body>
</html>
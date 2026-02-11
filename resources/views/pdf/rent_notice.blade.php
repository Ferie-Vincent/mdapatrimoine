<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Avis d'Échéance</title>
    <style>
        @page {
            margin: 20mm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        .sci-info {
            font-size: 11px;
        }
        .sci-name {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin: 25px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .recipient {
            margin: 20px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #2c3e50;
        }
        .body-text {
            margin: 20px 0;
            text-align: justify;
            line-height: 1.8;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .detail-table th,
        .detail-table td {
            border: 1px solid #bdc3c7;
            padding: 8px 12px;
            text-align: left;
        }
        .detail-table th {
            background-color: #2c3e50;
            color: #fff;
            font-weight: bold;
        }
        .detail-table .amount {
            text-align: right;
        }
        .detail-table .total-row {
            background-color: #ecf0f1;
            font-weight: bold;
        }
        .payment-instructions {
            margin: 25px 0;
            padding: 15px;
            background-color: #eaf2f8;
            border: 1px solid #aed6f1;
            border-radius: 3px;
        }
        .payment-instructions strong {
            color: #2c3e50;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            padding-top: 5px;
            float: right;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .company-brand {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e0e0e0;
        }
        .company-brand img {
            height: 36px;
            vertical-align: middle;
            margin-right: 8px;
        }
        .company-brand-name {
            font-size: 15px;
            font-weight: bold;
            color: #555;
            vertical-align: middle;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 60%;">
                    <div class="company-brand">
                        <img src="{{ public_path('assets/img/logo-2.jpg') }}" alt="MDA">
                        <span class="company-brand-name">MDA Patrimoine</span>
                    </div>
                    <div class="sci-name">{{ $sci->name }}</div>
                    <div class="sci-info">
                        @if($sci->address){{ $sci->address }}<br>@endif
                        @if($sci->rccm)RCCM : {{ $sci->rccm }}<br>@endif
                        @if($sci->ifu)IFU : {{ $sci->ifu }}<br>@endif
                        @if($sci->phone)Tel : {{ $sci->phone }}<br>@endif
                        @if($sci->email)Email : {{ $sci->email }}@endif
                    </div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <div style="font-size: 11px; color: #666;">
                        Date : {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="title">Avis d'&Eacute;ch&eacute;ance</div>

    <div class="recipient">
        <strong>Destinataire :</strong><br>
        {{ $monthly->lease->tenant->full_name }}<br>
        @if($monthly->lease->tenant->address){{ $monthly->lease->tenant->address }}@endif
    </div>

    @php
        $months_fr = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
        $monthParts = explode('-', $monthly->month);
        $monthFormatted = $months_fr[(int)$monthParts[1]] . ' ' . $monthParts[0];
        $dueDateFormatted = \Carbon\Carbon::parse($monthly->due_date)->format('d/m/Y');
    @endphp

    <div class="body-text">
        Madame, Monsieur,<br><br>
        Nous vous informons que le loyer du mois de <strong>{{ $monthFormatted }}</strong>
        est d&ucirc; le <strong>{{ $dueDateFormatted }}</strong>.
    </div>

    <table class="detail-table">
        <thead>
            <tr>
                <th>D&eacute;signation</th>
                <th style="text-align: right;">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Loyer</td>
                <td class="amount">{{ number_format((float)$monthly->rent_due, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>Frais d'agence</td>
                <td class="amount">{{ number_format((float)$monthly->charges_due, 0, ',', ' ') }}</td>
            </tr>
            @if((float)$monthly->penalty_due > 0)
            <tr>
                <td>P&eacute;nalit&eacute;s</td>
                <td class="amount">{{ number_format((float)$monthly->penalty_due, 0, ',', ' ') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td><strong>Total d&ucirc;</strong></td>
                <td class="amount"><strong>{{ number_format((float)$monthly->total_due, 0, ',', ' ') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="payment-instructions">
        <strong>Modalit&eacute;s de paiement :</strong><br><br>
        Le paiement peut &ecirc;tre effectu&eacute; par :
        <ul>
            <li>Esp&egrave;ces aupr&egrave;s de notre bureau</li>
            <li>Virement bancaire @if($sci->bank_name)&agrave; {{ $sci->bank_name }}@endif @if($sci->bank_iban)(IBAN : {{ $sci->bank_iban }})@endif</li>
            <li>Ch&egrave;que &agrave; l'ordre de {{ $sci->name }}</li>
        </ul>
        Veuillez mentionner la r&eacute;f&eacute;rence du bien <strong>{{ $monthly->lease->property->reference }}</strong> lors de votre paiement.
    </div>

    <div class="signature-section">
        <p>Nous vous prions d'agr&eacute;er, Madame, Monsieur, l'expression de nos salutations distingu&eacute;es.</p>
        <div class="signature-line">
            La Direction
        </div>
    </div>

    <div class="footer">
        MDA Patrimoine — {{ $sci->name }} @if($sci->rccm)| RCCM : {{ $sci->rccm }}@endif @if($sci->ifu)| IFU : {{ $sci->ifu }}@endif
    </div>
</body>
</html>

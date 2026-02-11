<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quittance de Loyer</title>
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
        .doc-ref {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
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
        .property-info {
            margin: 20px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #2c3e50;
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
                <td style="width: 40%;">
                    <div class="doc-ref">Quittance N&deg; {{ $monthly->id }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="title">Quittance de Loyer</div>

    <div class="body-text">
        @php
            $months_fr = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
            ];
            $monthParts = explode('-', $monthly->month);
            $monthFormatted = $months_fr[(int)$monthParts[1]] . ' ' . $monthParts[0];
        @endphp

        Je soussign&eacute;, <strong>{{ $sci->name }}</strong>, d&eacute;clare avoir re&ccedil;u de
        <strong>{{ $monthly->lease->tenant->full_name }}</strong>,
        la somme de <strong>{{ number_format((float)$monthly->paid_amount, 0, ',', ' ') }} FCFA</strong>
        au titre du loyer du mois de <strong>{{ $monthFormatted }}</strong>.
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
            <tr class="total-row">
                <td>Total d&ucirc;</td>
                <td class="amount">{{ number_format((float)$monthly->total_due, 0, ',', ' ') }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Pay&eacute;</strong></td>
                <td class="amount"><strong>{{ number_format((float)$monthly->paid_amount, 0, ',', ' ') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="property-info">
        <strong>Bien concern&eacute; :</strong>
        {{ $monthly->lease->property->reference }} &mdash; {{ $monthly->lease->property->address }}
    </div>

    <div class="signature-section">
        <p>Fait &agrave; {{ $sci->address ? explode(',', $sci->address)[0] : '_______________' }}, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        <div class="signature-line">
            Signature
        </div>
    </div>

    <div class="footer">
        MDA Patrimoine — {{ $sci->name }} @if($sci->rccm)| RCCM : {{ $sci->rccm }}@endif @if($sci->ifu)| IFU : {{ $sci->ifu }}@endif
    </div>
</body>
</html>

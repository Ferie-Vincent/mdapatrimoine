<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>QUITTANCE DE LOYER</title>
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
        .sci-name {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .sci-info {
            font-size: 10px;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin: 25px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .info-block {
            margin: 15px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #2c3e50;
        }
        .info-block p {
            margin: 3px 0;
        }
        .info-label {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            color: #666;
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
            text-transform: uppercase;
            font-size: 10px;
        }
        .detail-table .amount {
            text-align: right;
        }
        .detail-table .total-row {
            background-color: #ecf0f1;
            font-weight: bold;
        }
        .modes-section {
            margin-top: 30px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #27ae60;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
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
                    <div style="text-align: right; font-size: 13px; font-weight: bold; color: #2c3e50;">
                        QUITTANCE N&deg; {{ $monthly->id }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="title">QUITTANCE DE LOYER</div>

    @php
        $months_fr = [
            1 => 'Janvier', 2 => 'F&eacute;vrier', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Ao&ucirc;t',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'D&eacute;cembre',
        ];
        $monthParts = explode('-', $monthly->month);
        $monthNum = (int)$monthParts[1];
        $yearNum = $monthParts[0];
        $monthFormatted = $months_fr[$monthNum] . ' ' . $yearNum;
    @endphp

    {{-- En-tête --}}
    <div class="info-block">
        <p><span class="info-label">MOIS :</span> {!! $months_fr[$monthNum] !!}</p>
        <p><span class="info-label">ANNEE :</span> {{ $yearNum }}</p>
        <p><span class="info-label">LOCATAIRE :</span> {{ $tenant->full_name ?? (($tenant->last_name ?? '') . ' ' . ($tenant->first_name ?? '')) }}</p>
        <p><span class="info-label">REFERENCE :</span> {{ $property->reference ?? '-' }}</p>
        <p><span class="info-label">N° APPARTEMENT :</span> {{ $property->numero_porte ?? $lease->dossier_number ?? '-' }}</p>
        <p><span class="info-label">ADRESSE :</span> {{ $property->address ?? '-' }}</p>
        <p><span class="info-label">PERIODE :</span> {!! $monthFormatted !!}</p>
    </div>

    {{-- Table DETAIL DU REGLEMENT / MONTANTS --}}
    <table class="detail-table">
        <thead>
            <tr>
                <th>DETAIL DU REGLEMENT</th>
                <th style="text-align: right;">MONTANTS (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>LOYER MENSUEL</td>
                <td class="amount">{{ number_format((float)$monthly->rent_due, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>ARRIERES DE LOYERS</td>
                <td class="amount">{{ (float)$monthly->remaining_amount > 0 ? number_format((float)$monthly->remaining_amount, 0, ',', ' ') : '0' }}</td>
            </tr>
            <tr class="total-row">
                <td>SOMME TOTALE DUE</td>
                <td class="amount">{{ number_format((float)$monthly->total_due, 0, ',', ' ') }}</td>
            </tr>
            <tr class="total-row">
                <td>SOMME TOTALE RECUE</td>
                <td class="amount">{{ number_format((float)$monthly->paid_amount, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <td>SOLDE</td>
                <td class="amount" style="{{ (float)$monthly->remaining_amount > 0 ? 'color: #c0392b;' : 'color: #27ae60;' }}">
                    {{ number_format((float)$monthly->remaining_amount, 0, ',', ' ') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Pied --}}
    <div class="modes-section">
        <p><strong>MODES DE REGLEMENTS :</strong> {{ $monthly->payments->pluck('method')->map(fn($m) => ucfirst(str_replace('_', ' ', $m)))->unique()->implode(', ') ?: '-' }}</p>
    </div>

    <div class="footer">
        MDA Patrimoine — {{ $sci->name }} @if($sci->rccm)| RCCM : {{ $sci->rccm }}@endif @if($sci->ifu)| IFU : {{ $sci->ifu }}@endif
    </div>
</body>
</html>

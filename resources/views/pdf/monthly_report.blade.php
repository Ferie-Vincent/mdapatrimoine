<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État Récapitulatif Mensuel</title>
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
        .month-label {
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        .summary-table {
            width: 60%;
            margin: 0 auto 25px auto;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
        }
        .summary-table .label {
            background-color: #2c3e50;
            color: #fff;
            font-weight: bold;
            width: 50%;
        }
        .summary-table .value {
            text-align: right;
            font-weight: bold;
            font-size: 13px;
        }
        .recovery-rate {
            text-align: center;
            margin: 15px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .rate-good { color: #27ae60; }
        .rate-warning { color: #f39c12; }
        .rate-bad { color: #e74c3c; }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .detail-table th,
        .detail-table td {
            border: 1px solid #bdc3c7;
            padding: 5px 8px;
            text-align: left;
            font-size: 10px;
        }
        .detail-table th {
            background-color: #2c3e50;
            color: #fff;
            font-weight: bold;
            font-size: 10px;
        }
        .detail-table .amount {
            text-align: right;
        }
        .detail-table .total-row {
            background-color: #ecf0f1;
            font-weight: bold;
        }
        .status-paye { color: #27ae60; font-weight: bold; }
        .status-impaye { color: #e74c3c; font-weight: bold; }
        .status-partiel { color: #f39c12; font-weight: bold; }
        .status-en_retard { color: #c0392b; font-weight: bold; }
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
    @php
        $months_fr = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
        $monthParts = explode('-', $month);
        $monthFormatted = $months_fr[(int)$monthParts[1]] . ' ' . $monthParts[0];
        $status_labels = [
            'paye' => 'Payé',
            'impaye' => 'Impayé',
            'partiel' => 'Partiel',
            'en_retard' => 'En retard',
        ];

        $totalExpectedVal = (float)$total_expected;
        $totalCollectedVal = (float)$total_collected;
        $totalRemainingVal = (float)$total_remaining;
        $recoveryRate = $totalExpectedVal > 0 ? round(($totalCollectedVal / $totalExpectedVal) * 100, 1) : 0;
    @endphp

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

    <div class="title">&Eacute;tat R&eacute;capitulatif Mensuel</div>
    <div class="month-label">Mois : {{ $monthFormatted }}</div>

    <table class="summary-table">
        <tr>
            <td class="label">Total attendu</td>
            <td class="value">{{ number_format($totalExpectedVal, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td class="label">Total encaiss&eacute;</td>
            <td class="value">{{ number_format($totalCollectedVal, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td class="label">Total impay&eacute;</td>
            <td class="value">{{ number_format($totalRemainingVal, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td class="label">Taux de recouvrement</td>
            <td class="value">
                <span class="{{ $recoveryRate >= 80 ? 'rate-good' : ($recoveryRate >= 50 ? 'rate-warning' : 'rate-bad') }}">
                    {{ $recoveryRate }}%
                </span>
            </td>
        </tr>
    </table>

    <table class="detail-table">
        <thead>
            <tr>
                <th>Bien</th>
                <th>Locataire</th>
                <th style="text-align: right;">Loyer D&ucirc;</th>
                <th style="text-align: right;">Frais agence</th>
                <th style="text-align: right;">Total</th>
                <th style="text-align: right;">Pay&eacute;</th>
                <th style="text-align: right;">Reste</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumRent = 0; $sumCharges = 0; $sumTotal = 0; $sumPaid = 0; $sumRemaining = 0;
            @endphp
            @foreach($monthlies as $m)
                @php
                    $sumRent += (float)$m->rent_due;
                    $sumCharges += (float)$m->charges_due;
                    $sumTotal += (float)$m->total_due;
                    $sumPaid += (float)$m->paid_amount;
                    $sumRemaining += (float)$m->remaining_amount;
                @endphp
                <tr>
                    <td>{{ $m->lease->property->reference ?? '&mdash;' }}</td>
                    <td>{{ $m->lease->tenant->full_name ?? '&mdash;' }}</td>
                    <td class="amount">{{ number_format((float)$m->rent_due, 0, ',', ' ') }}</td>
                    <td class="amount">{{ number_format((float)$m->charges_due, 0, ',', ' ') }}</td>
                    <td class="amount">{{ number_format((float)$m->total_due, 0, ',', ' ') }}</td>
                    <td class="amount">{{ number_format((float)$m->paid_amount, 0, ',', ' ') }}</td>
                    <td class="amount">{{ number_format((float)$m->remaining_amount, 0, ',', ' ') }}</td>
                    <td>
                        <span class="status-{{ $m->status }}">
                            {{ $status_labels[$m->status] ?? ucfirst($m->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2"><strong>TOTAUX</strong></td>
                <td class="amount">{{ number_format($sumRent, 0, ',', ' ') }}</td>
                <td class="amount">{{ number_format($sumCharges, 0, ',', ' ') }}</td>
                <td class="amount">{{ number_format($sumTotal, 0, ',', ' ') }}</td>
                <td class="amount">{{ number_format($sumPaid, 0, ',', ' ') }}</td>
                <td class="amount">{{ number_format($sumRemaining, 0, ',', ' ') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 11px; color: #666;">
        Document g&eacute;n&eacute;r&eacute; le {{ \Carbon\Carbon::now()->format('d/m/Y &\a\g\r\a\v\e; H:i') }}
    </div>

    <div class="footer">
        MDA Patrimoine — {{ $sci->name }} @if($sci->rccm)| RCCM : {{ $sci->rccm }}@endif @if($sci->ifu)| IFU : {{ $sci->ifu }}@endif
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relevé de Compte Locataire</title>
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
        .info-section {
            margin: 15px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #2c3e50;
        }
        .info-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-section td {
            padding: 3px 5px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
            color: #2c3e50;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .detail-table th,
        .detail-table td {
            border: 1px solid #bdc3c7;
            padding: 6px 10px;
            text-align: left;
            font-size: 11px;
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
            background-color: #2c3e50;
            color: #fff;
            font-weight: bold;
        }
        .status-paye {
            color: #27ae60;
            font-weight: bold;
        }
        .status-impaye {
            color: #e74c3c;
            font-weight: bold;
        }
        .status-partiel {
            color: #f39c12;
            font-weight: bold;
        }
        .status-en_retard {
            color: #c0392b;
            font-weight: bold;
        }
        .status-a_venir {
            color: #2980b9;
            font-weight: bold;
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
    @php
        $months_fr = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
        $status_labels = [
            'paye' => 'Payé',
            'impaye' => 'Impayé',
            'partiel' => 'Partiel',
            'en_retard' => 'En retard',
            'a_venir' => 'A venir',
        ];
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

    <div class="title">Relev&eacute; de Compte Locataire</div>

    <div class="info-section">
        <table>
            <tr>
                <td class="info-label">Locataire :</td>
                <td>{{ $lease->tenant->full_name }}</td>
                <td class="info-label">Bien :</td>
                <td>{{ $lease->property->reference }} &mdash; {{ $lease->property->address }}</td>
            </tr>
            <tr>
                <td class="info-label">P&eacute;riode :</td>
                <td colspan="3">
                    @php
                        $fromParts = explode('-', $from_month);
                        $toParts = explode('-', $to_month);
                        $fromFormatted = $months_fr[(int)$fromParts[1]] . ' ' . $fromParts[0];
                        $toFormatted = $months_fr[(int)$toParts[1]] . ' ' . $toParts[0];
                    @endphp
                    {{ $fromFormatted }} &agrave; {{ $toFormatted }}
                </td>
            </tr>
        </table>
    </div>

    <table class="detail-table">
        <thead>
            <tr>
                <th>Mois</th>
                <th style="text-align: right;">Total D&ucirc; (FCFA)</th>
                <th style="text-align: right;">Pay&eacute; (FCFA)</th>
                <th style="text-align: right;">Reste (FCFA)</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlies as $m)
                @php
                    $mParts = explode('-', $m->month);
                    $mFormatted = $months_fr[(int)$mParts[1]] . ' ' . $mParts[0];
                @endphp
                <tr>
                    <td>{{ $mFormatted }}</td>
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
                <td><strong>TOTAUX</strong></td>
                <td class="amount">{{ number_format((float)$total_due, 0, ',', ' ') }}</td>
                <td class="amount">{{ number_format((float)$total_paid, 0, ',', ' ') }}</td>
                <td class="amount">{{ number_format((float)$total_remaining, 0, ',', ' ') }}</td>
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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>RECU D'ENTREE — CAUTION ET AVANCES</title>
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
        .loyer-reference {
            margin: 10px 0;
            font-size: 11px;
            font-style: italic;
            color: #555;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            vertical-align: top;
            padding: 10px 0;
            width: 50%;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            padding-top: 5px;
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
                <td style="width: 40%; text-align: right;">
                    <div style="font-size: 10px; color: #666;">
                        Date : {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="title">RECU D'ENTREE — CAUTION ET AVANCES</div>

    {{-- En-tete locataire --}}
    <div class="info-block">
        <p><span class="info-label">N° DOSSIER :</span> {{ $lease->dossier_number ?? '-' }}</p>
        <p><span class="info-label">LOCATAIRE :</span> {{ $tenant->full_name ?? '-' }}</p>
        <p><span class="info-label">N° D'APPARTEMENT :</span> {{ $property->apartment_number ?? $property->reference ?? '-' }}</p>
        <p><span class="info-label">ADRESSE :</span> {{ $property->address ?? '-' }}</p>
        <p><span class="info-label">DATE D'ENTREE :</span> {{ $lease->start_date ? \Carbon\Carbon::parse($lease->start_date)->format('d/m/Y') : '-' }}</p>
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
                <td>CAUTION (2 mois de loyer)</td>
                <td class="amount">{{ $lease->caution_2_mois ? number_format((float)$lease->caution_2_mois, 0, ',', ' ') : '-' }}</td>
            </tr>
            <tr>
                <td>AVANCES SUR LOYERS (2 mois)</td>
                <td class="amount">{{ $lease->loyers_avances_2_mois ? number_format((float)$lease->loyers_avances_2_mois, 0, ',', ' ') : '-' }}</td>
            </tr>
            <tr class="total-row">
                <td>SOMME TOTALE</td>
                <td class="amount">{{ number_format((float)(($lease->caution_2_mois ?? 0) + ($lease->loyers_avances_2_mois ?? 0)), 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="loyer-reference">
        Loyer mensuel de reference : {{ $lease->rent_amount ? number_format((float)$lease->rent_amount, 0, ',', ' ') : '-' }} FCFA
    </div>

    {{-- Pied --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <strong>SIGNATURE DU LOCATAIRE /PO ET DATE</strong>
                    <div class="signature-line">&nbsp;</div>
                </td>
                <td>
                    <strong>CACHET ET SIGNATURE DE L'AGENCE</strong>
                    <div class="signature-line">&nbsp;</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        MDA Patrimoine — {{ $sci->name }} @if($sci->rccm)| RCCM : {{ $sci->rccm }}@endif @if($sci->ifu)| IFU : {{ $sci->ifu }}@endif
    </div>
</body>
</html>

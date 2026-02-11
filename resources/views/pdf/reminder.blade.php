<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relance - Loyer Impayé</title>
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
            color: #c0392b;
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
            font-size: 13px;
        }
        .amount-highlight {
            margin: 20px 0;
            padding: 15px;
            background-color: #fdedec;
            border: 2px solid #e74c3c;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #c0392b;
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
            width: 40%;
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
    @php
        $months_fr = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
        $monthParts = explode('-', $monthly->month);
        $monthFormatted = $months_fr[(int)$monthParts[1]] . ' ' . $monthParts[0];
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

    <div class="title">Relance &mdash; Loyer Impay&eacute;</div>

    <div class="recipient">
        <strong>Destinataire :</strong><br>
        {{ $monthly->lease->tenant->full_name }}<br>
        @if($monthly->lease->tenant->address){{ $monthly->lease->tenant->address }}@endif
    </div>

    <div class="body-text">
        Madame, Monsieur,<br><br>
        Nous vous informons que le loyer du mois de <strong>{{ $monthFormatted }}</strong>
        d'un montant de <strong>{{ number_format((float)$monthly->total_due, 0, ',', ' ') }} FCFA</strong>
        reste impay&eacute; &agrave; ce jour.
    </div>

    <div class="amount-highlight">
        Montant restant d&ucirc; : {{ number_format((float)$monthly->remaining_amount, 0, ',', ' ') }} FCFA
    </div>

    <table class="detail-table">
        <tbody>
            <tr>
                <th>Bien concern&eacute;</th>
                <td>{{ $monthly->lease->property->reference }} &mdash; {{ $monthly->lease->property->address }}</td>
            </tr>
            <tr>
                <th>Mois concern&eacute;</th>
                <td>{{ $monthFormatted }}</td>
            </tr>
            <tr>
                <th>Total d&ucirc;</th>
                <td>{{ number_format((float)$monthly->total_due, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <th>D&eacute;j&agrave; pay&eacute;</th>
                <td>{{ number_format((float)$monthly->paid_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <th>Reste &agrave; payer</th>
                <td><strong>{{ number_format((float)$monthly->remaining_amount, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="body-text">
        Nous vous prions de bien vouloir proc&eacute;der au r&egrave;glement dans les plus brefs d&eacute;lais.<br><br>
        En cas de difficult&eacute;, nous vous invitons &agrave; prendre contact avec notre service de gestion
        afin de convenir d'un arrangement.
        <br><br>
        &Agrave; d&eacute;faut de r&eacute;gularisation, nous nous verrons dans l'obligation d'engager les proc&eacute;dures
        pr&eacute;vues par les dispositions contractuelles et l&eacute;gales en vigueur.
    </div>

    <div class="signature-section">
        <p>Veuillez agr&eacute;er, Madame, Monsieur, l'expression de nos salutations distingu&eacute;es.</p>
        <div class="signature-line">
            La Direction
        </div>
    </div>

    <div class="footer">
        MDA Patrimoine — {{ $sci->name }} @if($sci->rccm)| RCCM : {{ $sci->rccm }}@endif @if($sci->ifu)| IFU : {{ $sci->ifu }}@endif
    </div>
</body>
</html>

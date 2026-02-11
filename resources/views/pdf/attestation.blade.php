<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation</title>
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
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .body-text {
            margin: 25px 0;
            text-align: justify;
            line-height: 2;
            font-size: 13px;
        }
        .signature-section {
            margin-top: 60px;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            padding-top: 5px;
            float: right;
        }
        .stamp-area {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
            color: #666;
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
        $titles = [
            'attestation_location' => 'Attestation de Domicile',
            'attestation_reception_fonds' => 'Attestation de Réception de Fonds',
            'attestation_bail' => 'Attestation de Réception de Bail',
            'attestation_sortie' => 'Attestation de Sortie',
        ];
        $title = $titles[$type] ?? 'Attestation';
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

    <div class="title">{{ $title }}</div>

    <div class="body-text">
        Je soussign&eacute;, <strong>{{ $sci->name }}</strong>,
        @if($sci->rccm)immatricul&eacute;e au RCCM sous le num&eacute;ro <strong>{{ $sci->rccm }}</strong>,@endif

        @if($type === 'attestation_location')
            atteste que <strong>{{ $lease->tenant->full_name }}</strong>
            occupe le bien <strong>{{ $lease->property->reference }} &mdash; {{ $lease->property->address }}</strong>
            depuis le <strong>{{ \Carbon\Carbon::parse($lease->start_date)->format('d/m/Y') }}</strong>.
            <br><br>
            Cette attestation est d&eacute;livr&eacute;e &agrave; l'int&eacute;ress&eacute;(e)
            pour servir et valoir ce que de droit.

        @elseif($type === 'attestation_reception_fonds')
            atteste avoir re&ccedil;u de <strong>{{ $lease->tenant->full_name }}</strong>
            la somme de <strong>{{ number_format((float)($extra['amount'] ?? 0), 0, ',', ' ') }} FCFA</strong>
            au titre de <strong>{{ $extra['reason'] ?? 'paiement' }}</strong>.
            <br><br>
            Cette attestation est d&eacute;livr&eacute;e &agrave; l'int&eacute;ress&eacute;(e)
            pour servir et valoir ce que de droit.

        @elseif($type === 'attestation_bail')
            atteste avoir re&ccedil;u le contrat de bail sign&eacute; par
            <strong>{{ $lease->tenant->full_name }}</strong>
            pour le bien <strong>{{ $lease->property->reference }} &mdash; {{ $lease->property->address }}</strong>.
            <br><br>
            Le bail prend effet &agrave; compter du <strong>{{ \Carbon\Carbon::parse($lease->start_date)->format('d/m/Y') }}</strong>
            @if($lease->end_date)
                et prendra fin le <strong>{{ \Carbon\Carbon::parse($lease->end_date)->format('d/m/Y') }}</strong>.
            @else
                pour une dur&eacute;e ind&eacute;termin&eacute;e.
            @endif
            <br><br>
            Cette attestation est d&eacute;livr&eacute;e &agrave; l'int&eacute;ress&eacute;(e)
            pour servir et valoir ce que de droit.

        @elseif($type === 'attestation_sortie')
            atteste que <strong>{{ $lease->tenant->full_name }}</strong>
            a quitt&eacute; le bien <strong>{{ $lease->property->reference }} &mdash; {{ $lease->property->address }}</strong>
            en date du <strong>{{ isset($extra['date']) ? \Carbon\Carbon::parse($extra['date'])->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}</strong>.
            <br><br>
            @if($lease->termination_reason)
                Motif : {{ $lease->termination_reason }}.<br><br>
            @endif
            Cette attestation est d&eacute;livr&eacute;e &agrave; l'int&eacute;ress&eacute;(e)
            pour servir et valoir ce que de droit.
        @endif
    </div>

    <div class="signature-section">
        <p>Fait &agrave; {{ $sci->address ? explode(',', $sci->address)[0] : '_______________' }}, le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        <div class="stamp-area">Cachet et signature</div>
        <div class="signature-line">
            Le G&eacute;rant
        </div>
    </div>

    <div class="footer">
        MDA Patrimoine — {{ $sci->name }} @if($sci->rccm)| RCCM : {{ $sci->rccm }}@endif @if($sci->ifu)| IFU : {{ $sci->ifu }}@endif
    </div>
</body>
</html>

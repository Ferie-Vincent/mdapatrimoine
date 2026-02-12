<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>FICHE LOCATAIRE</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 5px;
        }
        .header .sci-name {
            font-size: 13px;
            color: #555;
        }
        .section-title {
            background-color: #2c3e50;
            color: #fff;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 12px 0 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            border: 1px solid #bdc3c7;
            padding: 5px 8px;
            vertical-align: top;
        }
        .info-table .label {
            background-color: #ecf0f1;
            font-weight: bold;
            width: 40%;
            font-size: 10px;
            text-transform: uppercase;
        }
        .info-table .value {
            width: 60%;
        }
        .amount {
            text-align: right;
            font-weight: bold;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="company-brand" style="text-align: center; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid #e0e0e0;">
            <img src="{{ public_path('assets/img/logo-2.jpg') }}" alt="MDA" style="height: 36px; vertical-align: middle; margin-right: 8px;">
            <span style="font-size: 15px; font-weight: bold; color: #555; vertical-align: middle; letter-spacing: 1px;">MDA Patrimoine</span>
        </div>
        <h1>FICHE LOCATAIRE POUR CONTRAT DE BAIL A USAGE D'HABITATION</h1>
        <div class="sci-name">{{ $sci->name }}</div>
    </div>

    {{-- En-tête --}}
    <table class="info-table" style="margin-bottom: 10px;">
        <tr>
            <td class="label">Type d'appartement</td>
            <td class="value">{{ $property->apartment_type_label ?? ucfirst($property->type ?? '') }}</td>
        </tr>
        <tr>
            <td class="label">N° Appartement</td>
            <td class="value">{{ $lease->dossier_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Etage</td>
            <td class="value">{{ $property->floor_label ?? '-' }}</td>
        </tr>
    </table>

    {{-- BAILLEUR / PO --}}
    <div class="section-title">BAILLEUR / PO</div>
    <table class="info-table">
        <tr>
            <td class="label">Nom</td>
            <td class="value">{{ $sci->name ?? '-' }}</td>
        </tr>
    </table>

    {{-- LOCATAIRE --}}
    <div class="section-title">LOCATAIRE</div>
    <table class="info-table">
        <tr>
            <td class="label">Nom</td>
            <td class="value">{{ ($tenant->last_name ?? '') . ' ' . ($tenant->first_name ?? '') }}</td>
        </tr>
    </table>

    {{-- INFORMATIONS D'ENTREE --}}
    <div class="section-title">INFORMATIONS D'ENTREE</div>
    <table class="info-table">
        <tr>
            <td class="label">Date d'état des lieux d'entrée</td>
            <td class="value">{{ $lease->entry_inventory_date?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Caution versée - Date</td>
            <td class="value">{{ $lease->caution_date?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Caution versée - Montant</td>
            <td class="value amount">{{ $lease->caution_2_mois ? number_format((float)$lease->caution_2_mois, 0, ',', ' ') . ' FCFA' : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Loyers d'avance - Date</td>
            <td class="value">{{ $lease->advance_date?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Loyers d'avance - Montant</td>
            <td class="value amount">{{ $lease->loyers_avances_2_mois ? number_format((float)$lease->loyers_avances_2_mois, 0, ',', ' ') . ' FCFA' : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Honoraires versés - Date</td>
            <td class="value">{{ $lease->agency_fee_date?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Honoraires versés - Montant</td>
            <td class="value amount">{{ $lease->frais_agence ? number_format((float)$lease->frais_agence, 0, ',', ' ') . ' FCFA' : '-' }}</td>
        </tr>
        <tr>
            <td class="label">N° et type de pièce d'identité</td>
            <td class="value">{{ $tenant->id_number ?? '-' }}</td>
        </tr>
    </table>

    {{-- CAUTIONNAIRE / GARANT --}}
    <div class="section-title">CAUTIONNAIRE / GARANT</div>
    <table class="info-table">
        <tr>
            <td class="label">Nom et Prénom</td>
            <td class="value">{{ $tenant->guarantor_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Adresse</td>
            <td class="value">{{ $tenant->guarantor_address ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">N° et type de pièce d'identité</td>
            <td class="value">{{ $tenant->guarantor_id_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Profession</td>
            <td class="value">{{ $tenant->guarantor_profession ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tel</td>
            <td class="value">{{ $tenant->guarantor_phone ?? '-' }}</td>
        </tr>
    </table>

    {{-- INFORMATIONS DE SORTIE --}}
    <div class="section-title">INFORMATIONS DE SORTIE</div>
    <table class="info-table">
        <tr>
            <td class="label">Etat des lieux de sortie - Date</td>
            <td class="value">{{ $lease->exit_inventory_date?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Etat des lieux de sortie - Montant</td>
            <td class="value amount">{{ $lease->charges_due_amount ? number_format((float)$lease->charges_due_amount, 0, ',', ' ') . ' FCFA' : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Caution rendue - Date</td>
            <td class="value">{{ $lease->deposit_returned_date?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Caution rendue - Montant</td>
            <td class="value amount">{{ $lease->deposit_returned_amount ? number_format((float)$lease->deposit_returned_amount, 0, ',', ' ') . ' FCFA' : '-' }}</td>
        </tr>
    </table>

    <div class="footer">
        MDA Patrimoine — {{ $sci->name }} @if($sci->rccm)| RCCM : {{ $sci->rccm }}@endif @if($sci->ifu)| IFU : {{ $sci->ifu }}@endif
        | G&eacute;n&eacute;r&eacute; le {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relance de loyer</title>
</head>
<body style="margin:0;padding:0;background-color:#eef2fa;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef2fa;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(30,60,140,0.08);">

                    {{-- Logo bar --}}
                    <tr>
                        <td style="background-color:#ffffff;padding:28px 32px 0;text-align:center;">
                            <img src="{{ asset('assets/img/logo.jpg') }}" alt="Madoud's Art" style="height:60px;max-width:280px;">
                        </td>
                    </tr>

                    {{-- Header with level color --}}
                    <tr>
                        <td style="padding:20px 32px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background-color:{{ $level >= 3 ? '#dc2626' : ($level >= 2 ? '#ea580c' : '#1e3c8c') }};padding:16px 24px;border-radius:12px;text-align:center;">
                                        @if($level >= 3)
                                            <h1 style="margin:0;color:#ffffff;font-size:18px;font-weight:700;letter-spacing:1px;">MISE EN DEMEURE</h1>
                                        @elseif($level >= 2)
                                            <h1 style="margin:0;color:#ffffff;font-size:18px;font-weight:700;">Relance de loyer</h1>
                                        @else
                                            <h1 style="margin:0;color:#ffffff;font-size:18px;font-weight:700;">Rappel de loyer</h1>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:28px 32px 32px;">
                            <p style="margin:0 0 20px;color:#374151;font-size:15px;line-height:1.6;">
                                Cher(e) <strong>{{ $tenantName }}</strong>,
                            </p>

                            {{-- Info card --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef2fa;border:1px solid #d5ddf2;border-radius:12px;overflow:hidden;margin:0 0 24px;">
                                <tr>
                                    <td style="padding:20px 24px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0;color:#5471c0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Bien</td>
                                                <td style="padding:6px 0;color:#12224f;font-size:14px;font-weight:600;text-align:right;">{{ $propertyRef }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;color:#5471c0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Mois</td>
                                                <td style="padding:6px 0;color:#12224f;font-size:14px;font-weight:600;text-align:right;">{{ $month }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding:8px 0 0;border-top:1px solid #d5ddf2;"></td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;color:#12224f;font-size:15px;font-weight:700;">Montant restant</td>
                                                <td style="padding:6px 0;color:#dc2626;font-size:16px;font-weight:700;text-align:right;">{{ number_format($remainingAmount, 0, ',', ' ') }} FCFA</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Message --}}
                            <div style="margin:0 0 24px;color:#374151;font-size:14px;line-height:1.7;white-space:pre-line;">{{ $reminder->message }}</div>

                            @if($level >= 3)
                                <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
                                    <tr>
                                        <td style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px 20px;">
                                            <p style="margin:0;color:#991b1b;font-size:13px;font-weight:600;line-height:1.6;">
                                                Sans regularisation dans un delai de 72 heures, nous nous reservons le droit d'engager des poursuites conformement a la legislation en vigueur.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#12224f;padding:24px 32px;text-align:center;">
                            <p style="margin:0 0 4px;color:#aebde6;font-size:13px;font-weight:600;">Madoud's Art</p>
                            <p style="margin:0 0 12px;color:#8199d6;font-size:11px;">Architecture d'interieur &bull; Gestion Immobiliere et de patrimoine</p>
                            <p style="margin:0;color:#5471c0;font-size:11px;">
                                Ce message est genere automatiquement. Merci de ne pas y repondre directement.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>

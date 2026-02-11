<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; margin: 20px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #1e3a8a; padding-bottom: 15px; }
        .header h1 { font-size: 18px; color: #1e3a8a; margin: 0 0 4px; }
        .header p { font-size: 10px; color: #6b7280; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th { background: #1e3a8a; color: #fff; padding: 8px 6px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody td { padding: 7px 6px; border-bottom: 1px solid #e5e7eb; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="header">
        <div style="margin-bottom: 8px;">
            <img src="{{ public_path('assets/img/logo-2.jpg') }}" alt="MDA" style="height: 30px; vertical-align: middle; margin-right: 8px;">
            <span style="font-size: 14px; font-weight: bold; color: #555; vertical-align: middle; letter-spacing: 1px;">MDA Patrimoine</span>
        </div>
        <h1>{{ $title }}</h1>
        <p>Export du {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        MDA-Patrimoine &mdash; {{ $title }} &mdash; {{ now()->format('d/m/Y') }}
    </div>
</body>
</html>

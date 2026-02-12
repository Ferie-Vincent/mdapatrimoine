<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attestation de Réception de Fonds - {{ $sci->name }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Nunito', sans-serif; }

        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            .attestation-page {
                padding: 15mm 20mm;
                max-width: 100%;
                box-shadow: none;
                border: none;
            }
            @page {
                margin: 10mm;
                size: A4 portrait;
            }
        }

        @media screen {
            .print-only { display: none; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"></div>

    {{-- Toolbar --}}
    <div class="no-print bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-6 py-3 flex items-center justify-between">
            <a href="{{ route('financial-current.index', ['month' => $model->month, 'year' => $model->year]) }}"
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour au Point Financier
            </a>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimer
                </button>
            </div>
        </div>
    </div>

    {{-- Attestation Document --}}
    <div class="max-w-4xl mx-auto my-8 no-print:px-4">
        <div class="attestation-page bg-white rounded-2xl shadow-sm border border-gray-100 p-10 sm:p-14">

            {{-- Header --}}
            <div class="border-b-2 border-gray-700 pb-4 mb-8">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-3 pb-3 border-b border-gray-200">
                            <img src="{{ asset('assets/img/logo-2.jpg') }}" alt="MDA" class="h-9">
                            <span class="text-base font-bold text-gray-500 tracking-wide">MDA Patrimoine</span>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800 mb-1">{{ $sci->name }}</h2>
                        <div class="text-sm text-gray-500 leading-relaxed">
                            @if($sci->address){{ $sci->address }}<br>@endif
                            @if($sci->rccm)RCCM : {{ $sci->rccm }}<br>@endif
                            @if($sci->ifu)IFU : {{ $sci->ifu }}<br>@endif
                            @if($sci->phone)Tel : {{ $sci->phone }}<br>@endif
                            @if($sci->email)Email : {{ $sci->email }}@endif
                        </div>
                    </div>
                    <div class="text-right text-sm text-gray-500">
                        Date : {{ now()->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            {{-- Title --}}
            <div class="text-center my-10">
                <h1 class="text-2xl font-extrabold text-gray-800 uppercase tracking-widest">
                    Attestation de Réception de Fonds
                </h1>
                <div class="mt-2 mx-auto w-24 h-1 bg-brand-600 rounded-full"></div>
            </div>

            {{-- Body --}}
            <div class="text-base text-gray-700 leading-[2] text-justify my-10 px-2">
                Je soussign&eacute;(e), <strong class="text-gray-900">{{ $beneficiary }}</strong>,
                atteste avoir re&ccedil;u de <strong class="text-gray-900">{{ $sci->name }}</strong>
                @if($sci->rccm)(RCCM : {{ $sci->rccm }})@endif
                la somme de :
                <br>
                <div class="text-center my-4">
                    <span class="inline-block bg-gray-50 border-2 border-gray-300 rounded-xl px-8 py-3 text-xl font-extrabold text-gray-900 tracking-wide">
                        {{ number_format($amount, 0, ',', ' ') }} FCFA
                    </span>
                </div>

                @if($paymentMethod)
                    <p class="mt-4">
                        Mode de paiement :
                        <strong class="text-gray-900">
                            @switch($paymentMethod)
                                @case('especes') Espèces @break
                                @case('virement') Virement bancaire @break
                                @case('cheque') Chèque @break
                                @case('mobile_money') Mobile Money @break
                                @case('versement_especes') Versement espèces sur compte @break
                                @case('depot_bancaire') Dépôt bancaire @break
                                @default {{ $paymentMethod }}
                            @endswitch
                        </strong>
                    </p>
                @endif

                <p class="mt-2">
                    Au titre de : <strong class="text-gray-900">{{ $description }}</strong>
                </p>

                @if($date)
                    <p class="mt-2">
                        En date du : <strong class="text-gray-900">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong>
                    </p>
                @endif

                <p class="mt-6">
                    La pr&eacute;sente attestation est d&eacute;livr&eacute;e pour servir et valoir ce que de droit.
                </p>
            </div>

            {{-- Place & Date --}}
            <div class="mt-10 text-sm text-gray-600">
                Fait &agrave; {{ $sci->address ? explode(',', $sci->address)[0] : '_______________' }}, le {{ now()->format('d/m/Y') }}
            </div>

            {{-- Signatures --}}
            <div class="mt-10 grid grid-cols-2 gap-8">
                {{-- Beneficiary Signature --}}
                <div>
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Signature du bénéficiaire</p>
                    <div class="h-28 border border-gray-200 rounded-lg flex items-center justify-center bg-gray-50/50">
                        @if($model->signature_data)
                            <img id="signature-print-img" src="{{ $model->signature_data }}" alt="Signature" class="max-h-24 max-w-full">
                        @else
                            <img id="signature-print-img" src="" alt="Signature" class="max-h-24 max-w-full" style="display: none;">
                            <span class="print-only text-xs text-gray-400 italic">Non signé</span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm text-gray-700 font-medium">{{ $beneficiary }}</p>
                </div>

                {{-- Manager Signature --}}
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Cachet et signature</p>
                    <div class="h-28 border border-gray-200 rounded-lg bg-gray-50/50"></div>
                    <p class="mt-2 text-sm text-gray-700 font-medium">Le Gérant</p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-12 pt-4 border-t border-gray-200 text-center text-xs text-gray-400">
                MDA Patrimoine — {{ $sci->name }}
                @if($sci->rccm) | RCCM : {{ $sci->rccm }}@endif
                @if($sci->ifu) | IFU : {{ $sci->ifu }}@endif
            </div>
        </div>

        {{-- Signature Pad Section --}}
        <div class="no-print mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-8"
             x-data="signaturePad('{{ route('financial-current.save-signature', ['type' => $type, 'id' => $model->id]) }}', '{{ $model->signature_data ?? '' }}')">

            <h3 class="text-lg font-bold text-gray-800 mb-1">Signature du bénéficiaire</h3>
            <p class="text-sm text-gray-500 mb-4">Demandez au bénéficiaire de signer ci-dessous avec le doigt ou la souris.</p>

            {{-- Canvas --}}
            <div class="relative border-2 border-dashed border-gray-300 rounded-xl bg-gray-50/50 overflow-hidden"
                 style="touch-action: none;">
                <canvas x-ref="canvas"
                        class="w-full cursor-crosshair"
                        style="height: 200px;"
                        @mousedown="startDraw($event)"
                        @mousemove="draw($event)"
                        @mouseup="stopDraw()"
                        @mouseleave="stopDraw()"
                        @touchstart="startDraw($event)"
                        @touchmove="draw($event)"
                        @touchend="stopDraw()">
                </canvas>
                <div x-show="!signed" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <span class="text-gray-300 text-sm font-medium">Signez ici</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-4 flex items-center justify-between">
                <button @click="clear()" type="button"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Effacer
                </button>

                <button @click="saveAndPrint()" type="button"
                        :disabled="saving"
                        class="inline-flex items-center px-6 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="!saving">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Enregistrer & Imprimer
                        </span>
                    </template>
                    <template x-if="saving">
                        <span class="flex items-center">
                            <svg class="animate-spin w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Enregistrement...
                        </span>
                    </template>
                </button>
            </div>
        </div>
    </div>
</body>
</html>

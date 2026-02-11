@extends('layouts.app')

@section('title', 'Analytique — Comparaison des SCIs')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Comparaison des SCIs</h2>
    </div>

    {{-- Comparison Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SCI</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Revenus attendus</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Encaisses</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Impayes</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Taux recouvrement</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Biens</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Taux occupation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($comparison as $sci)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sci['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">{{ number_format($sci['expected'], 0, ',', ' ') }} F</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium text-right">{{ number_format($sci['collected'], 0, ',', ' ') }} F</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium text-right">{{ number_format($sci['unpaid'], 0, ',', ' ') }} F</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold ring-1 ring-inset
                                    {{ $sci['recovery_rate'] >= 80 ? 'bg-green-50 text-green-700 ring-green-200' : ($sci['recovery_rate'] >= 50 ? 'bg-amber-50 text-amber-700 ring-amber-200' : 'bg-red-50 text-red-700 ring-red-200') }}">
                                    {{ number_format($sci['recovery_rate'], 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">{{ $sci['properties'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold ring-1 ring-inset
                                    {{ $sci['occupancy_rate'] >= 80 ? 'bg-green-50 text-green-700 ring-green-200' : ($sci['occupancy_rate'] >= 50 ? 'bg-amber-50 text-amber-700 ring-amber-200' : 'bg-red-50 text-red-700 ring-red-200') }}">
                                    {{ number_format($sci['occupancy_rate'], 1) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- Bar chart: Revenue comparison --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Revenus par SCI</h3>
            <div id="chart-sci-revenue" class="w-full" style="min-height: 360px;"></div>
        </div>

        {{-- Horizontal bar: Recovery rates --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Taux de recouvrement par SCI</h3>
            <div id="chart-sci-recovery" class="w-full" style="min-height: 360px;"></div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const comparison = @json($comparison);
    const names = comparison.map(s => s.name);

    // Revenue comparison bar chart
    new ApexCharts(document.querySelector('#chart-sci-revenue'), {
        chart: { type: 'bar', height: 360, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
        series: [
            { name: 'Attendu', data: comparison.map(s => s.expected) },
            { name: 'Encaisse', data: comparison.map(s => s.collected) },
            { name: 'Impaye', data: comparison.map(s => s.unpaid) },
        ],
        xaxis: {
            categories: names,
            labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        },
        yaxis: {
            labels: {
                formatter: v => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v),
                style: { fontSize: '11px', colors: '#9ca3af' },
            },
        },
        colors: ['#6366f1', '#22c55e', '#ef4444'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '65%' } },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: v => new Intl.NumberFormat('fr-FR').format(v) + ' F' } },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
    }).render();

    // Recovery rate horizontal bar
    new ApexCharts(document.querySelector('#chart-sci-recovery'), {
        chart: { type: 'bar', height: 360, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
        series: [{
            name: 'Taux de recouvrement',
            data: comparison.map(s => s.recovery_rate),
        }],
        xaxis: {
            categories: names,
            labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
        },
        yaxis: {
            max: 100, min: 0,
            labels: { formatter: v => v + '%', style: { fontSize: '11px', colors: '#9ca3af' } },
        },
        plotOptions: {
            bar: {
                borderRadius: 6, horizontal: true, barHeight: '50%',
                colors: {
                    ranges: [
                        { from: 0, to: 49, color: '#ef4444' },
                        { from: 50, to: 79, color: '#f59e0b' },
                        { from: 80, to: 100, color: '#22c55e' },
                    ],
                },
            },
        },
        dataLabels: { enabled: true, formatter: v => v.toFixed(1) + '%', style: { fontSize: '12px' } },
        tooltip: { y: { formatter: v => v.toFixed(1) + ' %' } },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
    }).render();
});
</script>
@endpush

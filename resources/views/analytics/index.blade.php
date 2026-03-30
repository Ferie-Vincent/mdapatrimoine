@extends('layouts.app')

@section('title', 'Analytique — Comparaison des SCIs')

@section('content')

    {{-- Recovery KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            title="Taux recouvrement global"
            :value="number_format($recoveryStats['global_rate'], 1, ',', ' ') . ' %'"
            color="orange"
            trend="{{ $recoveryStats['global_rate'] >= 80 ? 'up' : 'down' }}"
            :trendValue="number_format($recoveryStats['global_rate'], 1) . '%'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'
        />
        <x-stat-card
            title="Taux du mois ({{ $recoveryStats['month_label'] }})"
            :value="number_format($recoveryStats['month_rate'], 1, ',', ' ') . ' %'"
            color="{{ $recoveryStats['month_rate'] >= 80 ? 'green' : ($recoveryStats['month_rate'] >= 50 ? 'orange' : 'red') }}"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'
        />
        <x-stat-card
            title="Encaisse / Attendu"
            :value="number_format($recoveryStats['total_collected'], 0, ',', ' ') . ' F'"
            color="green"
            :subtitle="'sur ' . number_format($recoveryStats['total_expected'], 0, ',', ' ') . ' F attendus'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        />
        <x-stat-card
            title="Reste a recouvrer"
            :value="number_format($recoveryStats['total_unpaid'], 0, ',', ' ') . ' F'"
            color="red"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>'
        />
    </div>

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-on-surface">Comparaison des SCIs</h2>
    </div>

    {{-- Comparison Table --}}
    <div class="bg-surface rounded-2xl border border-theme-subtle overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-subtle">
                <thead class="bg-surface-hover/50">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-on-surface-muted uppercase tracking-wider">SCI</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-on-surface-muted uppercase tracking-wider">Revenus attendus</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-on-surface-muted uppercase tracking-wider">Encaisses</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-on-surface-muted uppercase tracking-wider">Impayes</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-on-surface-muted uppercase tracking-wider">Taux recouvrement</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-on-surface-muted uppercase tracking-wider">Biens</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-on-surface-muted uppercase tracking-wider">Taux occupation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-theme-subtle">
                    @foreach($comparison as $sci)
                        <tr class="hover:bg-surface-hover/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-on-surface">{{ $sci['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-on-surface-secondary text-right">{{ number_format($sci['expected'], 0, ',', ' ') }} F</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium text-right">{{ number_format($sci['collected'], 0, ',', ' ') }} F</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium text-right">{{ number_format($sci['unpaid'], 0, ',', ' ') }} F</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold ring-1 ring-inset
                                    {{ $sci['recovery_rate'] >= 80 ? 'bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300 ring-green-200 dark:ring-green-800' : ($sci['recovery_rate'] >= 50 ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 ring-amber-200 dark:ring-amber-800' : 'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300 ring-red-200 dark:ring-red-800') }}">
                                    {{ number_format($sci['recovery_rate'], 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-on-surface-secondary text-right">{{ $sci['properties'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold ring-1 ring-inset
                                    {{ $sci['occupancy_rate'] >= 80 ? 'bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300 ring-green-200 dark:ring-green-800' : ($sci['occupancy_rate'] >= 50 ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 ring-amber-200 dark:ring-amber-800' : 'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300 ring-red-200 dark:ring-red-800') }}">
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
        <div class="bg-surface rounded-2xl border border-theme-subtle p-5">
            <h3 class="text-base font-semibold text-on-surface mb-4">Revenus par SCI</h3>
            <div id="chart-sci-revenue" class="w-full" style="min-height: 360px;"></div>
        </div>

        {{-- Horizontal bar: Recovery rates --}}
        <div class="bg-surface rounded-2xl border border-theme-subtle p-5">
            <h3 class="text-base font-semibold text-on-surface mb-4">Taux de recouvrement par SCI</h3>
            <div id="chart-sci-recovery" class="w-full" style="min-height: 360px;"></div>
        </div>
    </div>

@endsection

@pushOnce('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endPushOnce

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const comparison = @json($comparison);
    const names = comparison.map(s => s.name);

    function getChartTheme() {
        const s = getComputedStyle(document.documentElement);
        const isDark = document.documentElement.classList.contains('dark');
        return {
            grid: s.getPropertyValue('--color-chart-grid').trim() || '#f3f4f6',
            label: s.getPropertyValue('--color-chart-label').trim() || '#9ca3af',
            tooltipTheme: isDark ? 'dark' : 'light',
        };
    }
    let ct = getChartTheme();
    const allCharts = [];

    // Revenue comparison bar chart
    const cRevenue = new ApexCharts(document.querySelector('#chart-sci-revenue'), {
        chart: { type: 'bar', height: 360, toolbar: { show: false }, fontFamily: 'Nunito, sans-serif', background: 'transparent' },
        series: [
            { name: 'Attendu', data: comparison.map(s => s.expected) },
            { name: 'Encaisse', data: comparison.map(s => s.collected) },
            { name: 'Impaye', data: comparison.map(s => s.unpaid) },
        ],
        xaxis: {
            categories: names,
            labels: { style: { fontSize: '11px', colors: ct.label } },
        },
        yaxis: {
            labels: {
                formatter: v => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v),
                style: { fontSize: '11px', colors: ct.label },
            },
        },
        colors: ['#6366f1', '#22c55e', '#ef4444'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '65%' } },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: v => new Intl.NumberFormat('fr-FR').format(v) + ' F' }, theme: ct.tooltipTheme },
        grid: { borderColor: ct.grid, strokeDashArray: 4 },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px', labels: { colors: ct.label } },
    });
    cRevenue.render();
    allCharts.push(cRevenue);

    // Recovery rate horizontal bar
    const cRecovery = new ApexCharts(document.querySelector('#chart-sci-recovery'), {
        chart: { type: 'bar', height: 360, toolbar: { show: false }, fontFamily: 'Nunito, sans-serif', background: 'transparent' },
        series: [{
            name: 'Taux de recouvrement',
            data: comparison.map(s => s.recovery_rate),
        }],
        xaxis: {
            categories: names,
            labels: { style: { fontSize: '11px', colors: ct.label } },
        },
        yaxis: {
            max: 100, min: 0,
            labels: { formatter: v => v + '%', style: { fontSize: '11px', colors: ct.label } },
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
        tooltip: { y: { formatter: v => v.toFixed(1) + ' %' }, theme: ct.tooltipTheme },
        grid: { borderColor: ct.grid, strokeDashArray: 4 },
    });
    cRecovery.render();
    allCharts.push(cRecovery);

    // Listen for theme changes
    window.addEventListener('theme-changed', () => {
        ct = getChartTheme();
        allCharts.forEach(chart => {
            chart.updateOptions({
                xaxis: { labels: { style: { colors: ct.label } } },
                yaxis: { labels: { style: { colors: ct.label } } },
                grid: { borderColor: ct.grid },
                legend: { labels: { colors: ct.label } },
                tooltip: { theme: ct.tooltipTheme },
            }, false, false);
        });
    });
});
</script>
@endpush

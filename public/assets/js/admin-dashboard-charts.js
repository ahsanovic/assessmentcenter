'use strict';

(function () {
    if (window.__adminDashboardChartsInstalled) {
        return;
    }
    window.__adminDashboardChartsInstalled = true;

    const BULAN_LABELS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    const CHART_HOST_IDS = [
        'chart-event',
        'chart-metode',
        'chart-penyelesaian',
        'chart-skor',
        'chart-jpm-potensi',
        'chart-kategori-capaian',
    ];

    const state = {
        charts: {},
        bootGeneration: 0,
        bootTimer: null,
    };

    function getDashboardRoot() {
        return document.querySelector('[data-admin-dashboard]');
    }

    function readConfigFromDom() {
        const root = getDashboardRoot();
        if (!root) {
            return null;
        }

        const raw = root.getAttribute('data-dashboard-config');
        if (!raw) {
            return null;
        }

        try {
            return JSON.parse(raw);
        } catch (e) {
            return null;
        }
    }

    function getDashboardWire() {
        const root = getDashboardRoot();
        if (!root || typeof Livewire === 'undefined') {
            return null;
        }

        let el = root;
        while (el && !el.hasAttribute('wire:id')) {
            el = el.parentElement;
        }

        if (!el) {
            return null;
        }

        return Livewire.find(el.getAttribute('wire:id'));
    }

    function clearChartHost(id) {
        const el = document.getElementById(id);
        if (el) {
            el.innerHTML = '';
        }

        return el;
    }

    function destroyAllCharts() {
        const charts = { ...state.charts };
        state.charts = {};

        Object.values(charts).forEach((chart) => {
            try {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            } catch (e) {
                // instance DOM sudah diganti Livewire / bfcache
            }
        });

        CHART_HOST_IDS.forEach((id) => clearChartHost(id));
    }

    function destroyEventSelect2() {
        if (typeof jQuery === 'undefined') {
            return;
        }

        try {
            const $event = jQuery('#dashboard-filter-event');
            if (!$event.length) {
                return;
            }

            if ($event.hasClass('select2-hidden-accessible')) {
                $event.off('change.dashboardEvent');
                $event.select2('destroy');
            }
        } catch (e) {
            // select2 / DOM tidak sinkron setelah navigasi
        }
    }

    function initEventChart(series) {
        const el = clearChartHost('chart-event');
        if (!el || typeof ApexCharts === 'undefined') {
            return;
        }

        state.charts.eventChart = new ApexCharts(el, {
            series: series || [],
            chart: { height: 360, type: 'bar', stacked: false, toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: { categories: BULAN_LABELS },
            yaxis: {
                title: { text: 'Jumlah event' },
                labels: { formatter: (v) => Math.floor(v) },
            },
            legend: { position: 'top', horizontalAlign: 'left' },
            fill: { opacity: 1 },
        });
        state.charts.eventChart.render();
    }

    function initMetodeChart(labels, values) {
        const el = clearChartHost('chart-metode');
        if (!el || typeof ApexCharts === 'undefined') {
            return;
        }

        state.charts.metodeChart = new ApexCharts(el, {
            series: values || [],
            chart: { type: 'donut', height: 320 },
            labels: labels || [],
            legend: { position: 'bottom', fontSize: '11px' },
            dataLabels: { enabled: true, formatter: (val) => Math.round(val) + '%' },
            noData: { text: 'Tidak ada event pada tahun ini' },
        });
        state.charts.metodeChart.render();
    }

    function initPenyelesaianChart(labels, values) {
        const el = clearChartHost('chart-penyelesaian');
        if (!el || typeof ApexCharts === 'undefined') {
            return;
        }

        state.charts.penyelesaianChart = new ApexCharts(el, {
            series: [{ name: 'Selesai', data: values || [] }],
            chart: { type: 'bar', height: 420, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '70%' } },
            dataLabels: { enabled: true, formatter: (val) => Math.round(val) },
            xaxis: { categories: labels || [], title: { text: 'Jumlah peserta' } },
            noData: { text: 'Belum ada data penyelesaian' },
        });
        state.charts.penyelesaianChart.render();
    }

    function initSkorChart(labels, values) {
        const el = clearChartHost('chart-skor');
        if (!el || typeof ApexCharts === 'undefined') {
            return;
        }

        state.charts.skorChart = new ApexCharts(el, {
            series: [{ name: 'Rata-rata', data: values || [] }],
            chart: { type: 'bar', height: 420, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '70%' } },
            dataLabels: {
                enabled: true,
                formatter: (val) => (val != null ? Number(val).toFixed(2) : ''),
            },
            xaxis: { categories: labels || [], title: { text: 'Nilai rata-rata' } },
            tooltip: { y: { formatter: (val) => (val != null ? Number(val).toFixed(2) : '-') } },
            noData: { text: 'Belum ada data skor' },
        });
        state.charts.skorChart.render();
    }

    function initJpmPotensiChart(labels, values) {
        const el = clearChartHost('chart-jpm-potensi');
        if (!el || typeof ApexCharts === 'undefined') {
            return;
        }

        state.charts.jpmPotensiChart = new ApexCharts(el, {
            series: [{ name: 'Rata-rata JPM (%)', data: values || [] }],
            chart: { type: 'bar', height: 380, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '65%' } },
            dataLabels: {
                enabled: true,
                formatter: (val) => (val != null ? Number(val).toFixed(2) + '%' : ''),
            },
            xaxis: { categories: labels || [], title: { text: 'JPM (%)' }, max: 100 },
            tooltip: { y: { formatter: (val) => (val != null ? Number(val).toFixed(2) + '%' : '-') } },
            noData: { text: 'Belum ada data JPM tes potensi' },
        });
        state.charts.jpmPotensiChart.render();
    }

    function initKategoriCapaianChart(labels, potensi, pspk) {
        const el = clearChartHost('chart-kategori-capaian');
        if (!el || typeof ApexCharts === 'undefined') {
            return;
        }

        state.charts.kategoriCapaianChart = new ApexCharts(el, {
            series: [
                { name: 'Tes Potensi', data: potensi || [] },
                { name: 'PSPK', data: pspk || [] },
            ],
            chart: { type: 'bar', height: 380, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
            dataLabels: { enabled: true, formatter: (val) => Math.round(val) },
            xaxis: { categories: labels || [] },
            yaxis: {
                title: { text: 'Jumlah peserta' },
                labels: { formatter: (v) => Math.floor(v) },
            },
            legend: { position: 'top' },
            colors: ['#6571ff', '#05a34a'],
            noData: { text: 'Belum ada data kategori capaian' },
        });
        state.charts.kategoriCapaianChart.render();
    }

    function initDashboardEventSelect2(generation, config) {
        if (typeof jQuery === 'undefined') {
            return;
        }

        const wire = getDashboardWire();
        const $event = jQuery('#dashboard-filter-event');
        if (!$event.length || !wire) {
            return;
        }

        destroyEventSelect2();

        $event.select2({
            placeholder: 'Semua event (filter tahun)',
            allowClear: true,
            width: '100%',
        });

        $event.on('change.dashboardEvent', function () {
            if (generation !== state.bootGeneration) {
                return;
            }

            const value = jQuery(this).val();
            wire.set('event', value ? value : null);
        });

        const current = config?.filterEvent ?? wire.get?.('event');
        if (current) {
            $event.val(String(current)).trigger('change.select2');
        } else {
            $event.val(null).trigger('change.select2');
        }
    }

    function bootAdminDashboardCharts() {
        if (!getDashboardRoot()) {
            return;
        }

        const config = readConfigFromDom();
        if (!config || typeof ApexCharts === 'undefined') {
            return;
        }

        state.bootGeneration += 1;
        const generation = state.bootGeneration;

        destroyAllCharts();
        destroyEventSelect2();

        initEventChart(config.eventSeries);
        initMetodeChart(config.metodeLabels, config.metodeValues);
        initPenyelesaianChart(config.penyelesaianLabels, config.penyelesaianValues);
        initSkorChart(config.skorLabels, config.skorValues);
        initJpmPotensiChart(config.jpmPotensiLabels, config.jpmPotensiValues);
        initKategoriCapaianChart(
            config.kategoriCapaianLabels,
            config.kategoriCapaianPotensi,
            config.kategoriCapaianPspk
        );
        initDashboardEventSelect2(generation, config);
    }

    function scheduleBoot(attempt = 0) {
        clearTimeout(state.bootTimer);

        state.bootTimer = setTimeout(() => {
            if (!getDashboardRoot()) {
                return;
            }

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    try {
                        bootAdminDashboardCharts();
                    } catch (e) {
                        if (attempt < 15) {
                            scheduleBoot(attempt + 1);
                        }
                    }

                    const hasRenderedChart =
                        document.querySelector('#chart-event .apexcharts-canvas') !== null;

                    if (!hasRenderedChart && attempt < 15) {
                        scheduleBoot(attempt + 1);
                    }
                });
            });
        }, attempt === 0 ? 30 : 100);
    }

    window.bootAdminDashboardCharts = scheduleBoot;

    document.addEventListener('livewire:navigated', () => {
        if (getDashboardRoot()) {
            scheduleBoot();
        }
    });

    window.addEventListener('pageshow', (event) => {
        if (!getDashboardRoot()) {
            return;
        }

        if (event.persisted) {
            window.location.reload();
            return;
        }

        scheduleBoot();
    });

    window.addEventListener('popstate', () => {
        setTimeout(() => {
            if (getDashboardRoot()) {
                scheduleBoot();
            }
        }, 150);
    });

    let commitBootTimer = null;
    document.addEventListener('livewire:init', () => {
        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                if (!getDashboardRoot()) {
                    return;
                }
                clearTimeout(commitBootTimer);
                commitBootTimer = setTimeout(() => scheduleBoot(), 150);
            });
        });
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => scheduleBoot());
    } else if (getDashboardRoot()) {
        scheduleBoot();
    }
})();

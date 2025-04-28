<div>
    <div class="mb-4">
        <h4 class="mb-3 mb-md-0">Dashboard</h4>
    </div>

    {{-- Statistik --}}
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('admin.event') }}" wire:navigate style="text-decoration: none; color: inherit;">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-2">Total Event</h6>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h1 class="mb-2">{{ $total_event }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.peserta') }}" wire:navigate style="text-decoration: none; color: inherit;">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-2">Total Peserta</h6>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h1 class="mb-2">{{ $total_peserta }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.assessor') }}" wire:navigate
                        style="text-decoration: none; color: inherit;">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-2">Total Assessor</h6>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h1 class="mb-2">{{ $total_assessor }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.tes-berlangsung') }}" wire:navigate
                        style="text-decoration: none; color: inherit;">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-2">Tes Hari ini</h6>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h1 class="mb-2">{{ $tes_hari_ini }}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="row">
        <div class="col-lg-7 col-xl-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline mb-0">
                        <h6 class="card-title mb-0">Grafik Event</h6>
                        <select wire:model.live="tahun" id="tahun" class="form-select w-auto">
                            @foreach ($list_tahun as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-secondary mb-3">Jumlah Event Tes Potensi per Bulan Tahun {{ $tahun }}</p>
                    <div id="chart" wire:ignore style="min-height: 318px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-xl-4 grid-margin grid-margin-xl-2 stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="mb-2">
                        <h6 class="card-title mb-2">Jumlah Peserta Tes Potensi</h6>
                        <p class="text-secondary mb-3">Tahun {{ $tahun }}</p>
                    </div>
                    <div class="card-body text-center">
                        <h1 class="mb-0" style="font-size: 9em">{{ $jumlah_peserta_per_tahun ?? 0 }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7 col-xl-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-baseline mb-0">
                        <h6 class="card-title mb-0">Rata-rata skor per Kompetensi Berdasarkan Event</h6>
                    </div>
                    <div class="mt-3 d-flex">
                        <div wire:ignore class="me-2">
                            <select wire:model.live="event" id="event" class="form-select" style="width: 400px">
                                <option value="">Pilih Event</option>
                                @foreach ($list_event as $key => $event)
                                    <option value="{{ $key }}">{{ $event }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button wire:click="resetFilterEvent" class="btn btn-sm btn-inverse-danger">Reset</button>
                    </div>
                    <div id="radar-chart" wire:ignore></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-xl-4 grid-margin grid-margin-xl-0">
            <div class="card">
                <div class="card-body">
                    <div class="mb-2">
                        <h6 class="card-title mb-2">Ranking JPM Berdasarkan Event</h6>
                        <p class="text-secondary mb-3">{{ $event_name }}</p>
                    </div>
                    <div class="d-flex flex-column">
                        @if (count($jpm))
                            @foreach ($jpm as $index => $item)
                            <a href="javascript:;" class="d-flex align-items-center border-bottom py-2">
                                <div class="me-3">
                                    <img src="{{ asset('storage/' . $item->peserta->foto) }}" class="rounded-circle" width="40" height="40"
                                        alt="user">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="text-body mb-2">{{ $item->peserta->nama }}</h6>
                                        <p class="text-secondary fs-12px">#{{ $index + 1 }}</p>
                                    </div>
                                    <p class="text-secondary fs-13px">{{ $item->jpm . '% - ' . $item->kategori }}</p>
                                </div>
                            </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@once
    @push('js')
        {{-- bar chart --}}
        <script>
            let chart;

            function initChart(data) {
                if (chart) {
                    chart.destroy();
                }

                chart = new ApexCharts(document.querySelector("#chart"), {
                    series: [{
                        name: 'Jumlah Event',
                        data: data
                    }],
                    chart: {
                        height: 350,
                        type: 'bar'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 10,
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#304758"]
                        }
                    },
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov",
                            "Des"],
                        position: 'top',
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        tooltip: {
                            enabled: true
                        },
                        crosshairs: {
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    colorFrom: '#D8E3F0',
                                    colorTo: '#BED1E6',
                                    stops: [0, 100],
                                    opacityFrom: 0.4,
                                    opacityTo: 0.5,
                                }
                            }
                        }
                    },
                    yaxis: {
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            show: false
                        }
                    },
                    title: {
                        text: 'Jumlah Event Tes Potensi per Bulan',
                        floating: true,
                        offsetY: 330,
                        align: 'center',
                        style: {
                            color: '#444'
                        }
                    }
                });

                chart.render();
            }

            document.addEventListener('livewire:init', () => {
                initChart(@json($data_chart));
            });

            window.addEventListener('update-chart', event => {
                const newData = event.detail.data;
                if (chart) {
                    chart.destroy();
                }
                initChart(newData);
            });
        </script>

        {{-- radar chart --}}
        <script>
            var options = {
                series: [{
                    name: 'Series 1',
                    data: {!! json_encode(array_map(fn($v) => round($v ?? 0, 2), array_values($avg_skor))) !!},
                }],
                chart: {
                    height: 600,
                    width: 900,
                    type: 'radar',
                },
                title: {
                    align: 'center',
                    margin: 100,
                    text: '',
                    style: {
                        fontSize: '14px',
                        color: '#666',
                    }
                },
                yaxis: {
                    stepSize: 20
                },
                xaxis: {
                    categories: [
                        'Kemampuan Interpersonal',
                        'Kesadaran Diri',
                        'Problem Solving',
                        'Berpikir Kritis dan Strategis',
                        'Motivasi dan Komitmen',
                        'Kecerdasan Emosi',
                        'Belajar Cepat dan Pengembangan Diri',
                    ]
                },
                plotOptions: {
                    radar: {
                        size: 220,
                        polygons: {
                            strokeColors: '#e9e9e9',
                            fill: {
                                colors: ['#f8f8f8', '#fff']
                            }
                        }
                    }
                },
                dataLabels: {
                    show: true,
                    style: {
                        fontSize: '14px',
                        colors: ['#000']
                    }
                },
            };

            var radarChart = new ApexCharts(document.querySelector("#radar-chart"), options);
            radarChart.render();

            window.addEventListener('update-radar-chart', (event) => {
                const newData = event.detail.data;
                const eventName = event.detail.eventName;

                radarChart.updateOptions({
                    title: {
                        text: eventName || '',
                    }
                });

                radarChart.updateSeries([{
                    name: '',
                    data: newData,
                    eventName: eventName || '',
                }]);
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#event').select2()
                    .on('change', function(e) {
                        @this.set('event', $(this).val());
                    });

                Livewire.on('reset-select2', () => {
                    $('#event').val(null).trigger('change');
                });
            })
        </script>
    @endpush
@endonce

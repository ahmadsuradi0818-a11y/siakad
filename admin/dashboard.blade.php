@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">

    @php
        $hour = now()->hour;
        if ($hour < 10) $greeting = 'Selamat Pagi';
        elseif ($hour < 15) $greeting = 'Selamat Siang';
        elseif ($hour < 18) $greeting = 'Selamat Sore';
        else $greeting = 'Selamat Malam';
        $avatarColors = ['#667eea','#f5576c','#4facfe','#43e97b','#fa709a','#a18cd1'];
        $avatarColor = $avatarColors[crc32(auth()->user()->name) % count($avatarColors)];
    @endphp

    {{-- Greeting Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.1rem; background: {{ $avatarColor }};">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <h4 class="fw-bold mb-0" style="color: #1e293b;">{{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}!</h4>
                <p class="text-muted small mb-0">
                    <i class="fas fa-calendar-alt me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
                    <span class="mx-2">&middot;</span>
                    <i class="fas fa-user-shield me-1"></i>Administrator
                </p>
            </div>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-normal small">
            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
        </span>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 rounded-3 h-100 overflow-hidden" style="background: linear-gradient(135deg, #667eea, #764ba2); box-shadow: 0 4px 15px rgba(102,126,234,0.25);">
                <div class="card-body p-3 p-md-4 position-relative">
                    <div class="position-absolute end-0 bottom-0 opacity-10" style="font-size: 4.5rem; transform: translate(15%, 15%);"><i class="fas fa-user-graduate"></i></div>
                    <div class="position-relative">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;"><i class="fas fa-user-graduate text-white"></i></div>
                        </div>
                        <h3 class="fw-bold text-white mb-0 fs-2">{{ $totalSiswa }}</h3>
                        <p class="text-white-50 small mb-0">Total Siswa</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 rounded-3 h-100 overflow-hidden" style="background: linear-gradient(135deg, #43e97b, #38f9d7); box-shadow: 0 4px 15px rgba(67,233,123,0.25);">
                <div class="card-body p-3 p-md-4 position-relative">
                    <div class="position-absolute end-0 bottom-0 opacity-10" style="font-size: 4.5rem; transform: translate(15%, 15%);"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="position-relative">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;"><i class="fas fa-chalkboard-teacher text-white"></i></div>
                        </div>
                        <h3 class="fw-bold text-white mb-0 fs-2">{{ $totalGuru }}</h3>
                        <p class="text-white-50 small mb-0">Total Guru</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 rounded-3 h-100 overflow-hidden" style="background: linear-gradient(135deg, #4facfe, #00f2fe); box-shadow: 0 4px 15px rgba(79,172,254,0.25);">
                <div class="card-body p-3 p-md-4 position-relative">
                    <div class="position-absolute end-0 bottom-0 opacity-10" style="font-size: 4.5rem; transform: translate(15%, 15%);"><i class="fas fa-school"></i></div>
                    <div class="position-relative">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;"><i class="fas fa-school text-white"></i></div>
                        </div>
                        <h3 class="fw-bold text-white mb-0 fs-2">{{ $totalKelas }}</h3>
                        <p class="text-white-50 small mb-0">Total Kelas</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 rounded-3 h-100 overflow-hidden" style="background: linear-gradient(135deg, #f093fb, #f5576c); box-shadow: 0 4px 15px rgba(245,87,108,0.25);">
                <div class="card-body p-3 p-md-4 position-relative">
                    <div class="position-absolute end-0 bottom-0 opacity-10" style="font-size: 4.5rem; transform: translate(15%, 15%);"><i class="fas fa-clipboard-check"></i></div>
                    <div class="position-relative">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;"><i class="fas fa-clipboard-check text-white"></i></div>
                        </div>
                        <h3 class="fw-bold text-white mb-0 fs-2">{{ $totalHadir }}/{{ $presensiHariIni }}</h3>
                        <p class="text-white-50 small mb-0">Hadir Hari Ini</p>
                        @if($presensiHariIni > 0)
                        <div class="progress mt-2" style="height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px;">
                            <div class="progress-bar bg-white rounded-pill" style="width: {{ ($totalHadir / max($presensiHariIni, 1)) * 100 }}%;"></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts + Calendar Row --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-3 px-3 px-md-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.95rem;"><i class="fas fa-chart-pie me-2 text-danger"></i>Presensi Hari Ini</h5>
                    <span class="small text-muted">{{ array_sum($chartPresensiHariIni) }} total</span>
                </div>
                <div class="card-body pt-2 px-3 px-md-4 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                    <div class="position-relative d-inline-block">
                        <canvas id="chartPresensiToday" width="200" height="200"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <span class="fw-bold d-block" style="color: #1e293b; font-size: 1.4rem;">{{ $presensiHariIni > 0 ? round(($totalHadir / $presensiHariIni) * 100) : 0 }}%</span>
                            <span class="text-muted" style="font-size: 0.6rem;">hadir</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 px-3 px-md-4 pb-3 pt-0">
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <span class="small d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #22c55e;"></span> Hadir {{ $chartPresensiHariIni['hadir'] }}</span>
                        <span class="small d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #f59e0b;"></span> Sakit {{ $chartPresensiHariIni['sakit'] }}</span>
                        <span class="small d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #3b82f6;"></span> Izin {{ $chartPresensiHariIni['izin'] }}</span>
                        <span class="small d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background: #ef4444;"></span> Alpha {{ $chartPresensiHariIni['alpha'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-3 px-3 px-md-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.95rem;"><i class="fas fa-chart-bar me-2 text-success"></i>Presensi Per Bulan ({{ now()->year }})</h5>
                </div>
                <div class="card-body pt-2 px-3 px-md-4">
                    @if($presensiPerBulan->isNotEmpty())
                    <canvas id="chartPresensiBulan" height="220"></canvas>
                    @else
                    <div class="d-flex flex-column align-items-center justify-content-center text-muted" style="min-height: 200px;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background: #f1f5f9;"><i class="fas fa-chart-bar" style="font-size: 1.5rem; color: #cbd5e1;"></i></div>
                        <p class="small mb-0">Belum ada data presensi.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-3 px-3 px-md-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.95rem;"><i class="fas fa-calendar-alt me-2 text-warning"></i>Kalender Ujian</h5>
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill small fw-normal">{{ $jadwalUjian->count() }} ujian</span>
                </div>
                <div class="card-body px-3 px-md-4">
                    @include('components.calendar', ['examDates' => $examDates, 'month' => $month, 'year' => $year, 'route' => 'admin.dashboard'])
                </div>
                @if($jadwalUjian->isNotEmpty())
                <div class="card-footer bg-white border-top-0 px-3 px-md-4 pb-3">
                    <h6 class="fw-bold small text-muted mb-2"><i class="fas fa-arrow-right me-1"></i>Ujian Mendatang</h6>
                    @foreach($jadwalUjian->take(4) as $ujian)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light">
                        <div class="d-flex align-items-center gap-2 min-width-0">
                            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px; background: #fef3c7;">
                                <i class="fas fa-file-alt text-warning" style="font-size: 0.85rem;"></i>
                            </div>
                            <div class="min-width-0">
                                <span class="fw-semibold small d-block text-truncate" style="color: #1e293b; max-width: 220px;">{{ $ujian->name }}</span>
                                <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.65rem;">
                                    <i class="fas fa-school"></i>{{ $ujian->class?->name }}
                                    <span class="mx-1">&middot;</span>
                                    <i class="fas fa-chalkboard"></i>{{ $ujian->teacher?->name }}
                                </small>
                            </div>
                        </div>
                        <span class="badge bg-light text-muted rounded-pill fw-normal small flex-shrink-0">{{ $ujian->date->format('d/m') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-3 px-3 px-md-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.95rem;"><i class="fas fa-table me-2 text-info"></i>Detail Presensi Per Bulan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th class="small text-muted fw-semibold px-4 py-3 border-bottom-0">Bulan</th>
                                    <th class="small text-muted fw-semibold px-3 py-3 border-bottom-0 text-center">Hadir</th>
                                    <th class="small text-muted fw-semibold px-3 py-3 border-bottom-0 text-center">Sakit</th>
                                    <th class="small text-muted fw-semibold px-3 py-3 border-bottom-0 text-center">Izin</th>
                                    <th class="small text-muted fw-semibold px-3 py-3 border-bottom-0 text-center">Alpha</th>
                                    <th class="small text-muted fw-semibold px-4 py-3 border-bottom-0 text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presensiPerBulan as $data)
                                @php
                                    $monthColors = ['#667eea','#43e97b','#4facfe','#f59e0b','#f5576c','#a18cd1','#06b6d4','#f97316','#6366f1','#10b981','#ec4899','#eab308'];
                                @endphp
                                <tr class="border-bottom border-light">
                                    <td class="px-4 py-3">
                                        <span class="fw-semibold d-flex align-items-center gap-2" style="color: #0f172a; font-size: 0.88rem;">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $monthColors[$loop->index % 12] }}; display: inline-block;"></span>
                                            {{ \Carbon\Carbon::create()->month($data->bulan)->format('F') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-center"><span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-semibold px-3">{{ $data->hadir }}</span></td>
                                    <td class="px-3 py-3 text-center"><span class="badge bg-warning bg-opacity-10 text-warning rounded-pill fw-semibold px-3">{{ $data->sakit }}</span></td>
                                    <td class="px-3 py-3 text-center"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill fw-semibold px-3">{{ $data->izin }}</span></td>
                                    <td class="px-3 py-3 text-center"><span class="badge bg-danger bg-opacity-10 text-danger rounded-pill fw-semibold px-3">{{ $data->alpha }}</span></td>
                                    <td class="px-4 py-3 text-center"><span class="fw-bold" style="color: #0f172a;">{{ $data->total }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted small">Belum ada data presensi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var presensiTotal = {{ array_sum($chartPresensiHariIni) }};
    var ctx = document.getElementById('chartPresensiToday');
    if (ctx && presensiTotal > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Sakit', 'Izin', 'Alpha'],
                datasets: [{
                    data: [{{ $chartPresensiHariIni['hadir'] }}, {{ $chartPresensiHariIni['sakit'] }}, {{ $chartPresensiHariIni['izin'] }}, {{ $chartPresensiHariIni['alpha'] }}],
                    backgroundColor: ['#22c55e', '#f59e0b', '#3b82f6', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        cornerRadius: 8,
                        padding: 10,
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.label + ': ' + ctx.parsed + ' (' + Math.round(ctx.parsed / presensiTotal * 100) + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    @if($presensiPerBulan->isNotEmpty())
    var bulanCtx = document.getElementById('chartPresensiBulan');
    if (bulanCtx) {
        var bulanLabels = @json($presensiPerBulan->map(fn($d) => \Carbon\Carbon::create()->month($d->bulan)->format('M')));
        var bulanHadir = @json($presensiPerBulan->pluck('hadir'));
        var bulanSakit = @json($presensiPerBulan->pluck('sakit'));
        var bulanIzin = @json($presensiPerBulan->pluck('izin'));
        var bulanAlpha = @json($presensiPerBulan->pluck('alpha'));
        var maks = Math.max(...bulanHadir.concat(bulanSakit).concat(bulanIzin).concat(bulanAlpha)) + 2;

        var ctx2 = bulanCtx.getContext('2d');
        function makeGrad(color1, color2) {
            var g = ctx2.createLinearGradient(0, 0, 0, 220);
            g.addColorStop(0, color1);
            g.addColorStop(1, color2);
            return g;
        }

        new Chart(bulanCtx, {
            type: 'bar',
            data: {
                labels: bulanLabels,
                datasets: [
                    { label: 'Hadir', data: bulanHadir, backgroundColor: makeGrad('rgba(34,197,94,0.85)', 'rgba(34,197,94,0.25)'), borderColor: '#22c55e', borderWidth: 1, borderRadius: 4 },
                    { label: 'Sakit', data: bulanSakit, backgroundColor: makeGrad('rgba(245,158,11,0.85)', 'rgba(245,158,11,0.25)'), borderColor: '#f59e0b', borderWidth: 1, borderRadius: 4 },
                    { label: 'Izin', data: bulanIzin, backgroundColor: makeGrad('rgba(59,130,246,0.85)', 'rgba(59,130,246,0.25)'), borderColor: '#3b82f6', borderWidth: 1, borderRadius: 4 },
                    { label: 'Alpha', data: bulanAlpha, backgroundColor: makeGrad('rgba(239,68,68,0.85)', 'rgba(239,68,68,0.25)'), borderColor: '#ef4444', borderWidth: 1, borderRadius: 4 },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyle: 'circle', font: { size: 11 }, color: '#64748b' } },
                    tooltip: { backgroundColor: '#1e293b', cornerRadius: 8, padding: 12, titleFont: { size: 13 }, bodyFont: { size: 12 } }
                },
                scales: {
                    y: { beginAtZero: true, max: maks, ticks: { stepSize: 1, font: { size: 11, weight: '600' }, color: '#94a3b8' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { ticks: { font: { size: 10, weight: '600' }, color: '#64748b' }, grid: { display: false } },
                },
                interaction: { intersect: false, mode: 'index' },
            }
        });
    }
    @endif
});
</script>
@endpush
@endsection

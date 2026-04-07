<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — LG Electronics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background: linear-gradient(135deg, #a50034, #7b0028);
            color: #fff;
            padding: 1.5rem 0;
        }

        .header .subtitle {
            opacity: 0.85;
            font-size: 0.95rem;
        }

        .card-stat {
            border: none;
            border-radius: 0.75rem;
            color: #fff;
            transition: transform 0.2s;
        }

        .card-stat:hover {
            transform: translateY(-4px);
        }

        .card-stat .icon {
            font-size: 2rem;
            opacity: 0.7;
        }

        .card-stat .value {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .card-stat .label {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .bg-linhas   { background: linear-gradient(135deg, #1a73e8, #1557b0); }
        .bg-produzido { background: linear-gradient(135deg, #0097a7, #00796b); }
        .bg-defeitos  { background: linear-gradient(135deg, #f9a825, #f57f17); }
        .bg-eficiencia { background: linear-gradient(135deg, #2e7d32, #1b5e20); }

        .chart-container {
            background: #fff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .table-container {
            background: #fff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .table thead th {
            border-top: none;
            background-color: #f8f9fa;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555;
        }

        .table tbody tr:hover {
            background-color: #f0f4ff;
        }

        .badge-eficiencia {
            font-size: 0.85rem;
            padding: 0.35em 0.75em;
            border-radius: 0.5rem;
        }

        footer {
            color: #999;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <header class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-industry mr-2"></i>LG Electronics — Planta A
                    </h4>
                    <span class="subtitle">Dashboard de Eficiência de Produção</span>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="mt-2 mt-md-0">
                    <select name="linha_produto" class="custom-select" onchange="this.form.submit()">
                        <option value="">Todas as Linhas</option>
                        @foreach($linhas as $linha)
                            <option value="{{ $linha }}" {{ $linhaSelecionada === $linha ? 'selected' : '' }}>
                                {{ $linha }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </header>

    <div class="container mt-4">

        {{-- Cards de Resumo --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card card-stat bg-linhas p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label">Linhas Ativas</div>
                            <div class="value">{{ $produtividades->count() }}</div>
                        </div>
                        <div class="icon"><i class="fas fa-industry"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-stat bg-produzido p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label">Total Produzido</div>
                            <div class="value">{{ number_format($produtividades->sum('total_produzido'), 0, ',', '.') }}</div>
                        </div>
                        <div class="icon"><i class="fas fa-box"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-stat bg-defeitos p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label">Total Defeitos</div>
                            <div class="value">{{ number_format($produtividades->sum('total_defeitos'), 0, ',', '.') }}</div>
                        </div>
                        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                @php
                    $totalProd = $produtividades->sum('total_produzido');
                    $totalDef  = $produtividades->sum('total_defeitos');
                    $eficienciaGeral = $totalProd > 0 ? round((($totalProd - $totalDef) / $totalProd) * 100, 2) : 0;
                @endphp
                <div class="card card-stat bg-eficiencia p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="label">Eficiência Geral</div>
                            <div class="value">{{ $eficienciaGeral }}%</div>
                        </div>
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráficos --}}
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <h6 class="text-muted mb-3"><i class="fas fa-chart-bar mr-1"></i> Produção vs Defeitos</h6>
                    <canvas id="chartBarras"></canvas>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <h6 class="text-muted mb-3"><i class="fas fa-percentage mr-1"></i> Eficiência por Linha (%)</h6>
                    <canvas id="chartEficiencia"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="table-container mb-5">
            <h6 class="text-muted mb-1"><i class="fas fa-table mr-1"></i> Detalhamento por Linha de Produção</h6>
            <small class="text-muted d-block mb-3">Janeiro 2026 — Planta A</small>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Linha do Produto</th>
                            <th class="text-right">Qtd. Produzida</th>
                            <th class="text-right">Qtd. Defeitos</th>
                            <th class="text-right">Eficiência (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produtividades as $item)
                            <tr>
                                <td><i class="fas fa-cog mr-1 text-muted"></i> {{ $item->linha_produto }}</td>
                                <td class="text-right">{{ number_format($item->total_produzido, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->total_defeitos, 0, ',', '.') }}</td>
                                <td class="text-right">
                                    @php
                                        $ef = $item->eficiencia;
                                        if ($ef >= 97) $badgeClass = 'badge-success';
                                        elseif ($ef >= 95) $badgeClass = 'badge-primary';
                                        elseif ($ef >= 93) $badgeClass = 'badge-warning';
                                        else $badgeClass = 'badge-danger';
                                    @endphp
                                    <span class="badge badge-eficiencia {{ $badgeClass }}">{{ $ef }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        const labels      = @json($produtividades->pluck('linha_produto'));
        const produzidos  = @json($produtividades->pluck('total_produzido'));
        const defeitos    = @json($produtividades->pluck('total_defeitos'));
        const eficiencias = @json($produtividades->pluck('eficiencia'));

        new Chart(document.getElementById('chartBarras'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Produzida',
                        data: produzidos,
                        backgroundColor: 'rgba(26, 115, 232, 0.85)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Defeitos',
                        data: defeitos,
                        backgroundColor: 'rgba(220, 53, 69, 0.85)',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: { mode: 'index', intersect: false },
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#eee' } },
                    x: { grid: { display: false } }
                }
            }
        });

        const cores = eficiencias.map(function(ef) {
            if (ef >= 97) return 'rgba(46, 125, 50, 0.85)';
            if (ef >= 95) return 'rgba(26, 115, 232, 0.85)';
            if (ef >= 93) return 'rgba(249, 168, 37, 0.85)';
            return 'rgba(220, 53, 69, 0.85)';
        });

        new Chart(document.getElementById('chartEficiencia'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Eficiência (%)',
                    data: eficiencias,
                    backgroundColor: cores,
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) { return ctx.raw + '%'; }
                        }
                    }
                },
                scales: {
                    x: { min: 90, max: 100, grid: { color: '#eee' } },
                    y: { grid: { display: false } }
                }
            }
        });
    </script>

</body>
</html>

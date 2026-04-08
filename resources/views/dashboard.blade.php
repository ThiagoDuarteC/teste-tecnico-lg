<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — LG Electronics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>
<body>

    <header class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center">
                    <span class="lg-badge mr-3">LG</span>
                    <div>
                        <h1 class="title">Planta A — Eficiência de Produção</h1>
                        <p class="subtitle mb-0">Dashboard de acompanhamento — Janeiro 2026</p>
                    </div>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="mt-2 mt-md-0">
                    <select name="linha_produto" class="filter-select custom-select" onchange="this.form.submit()">
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

    <main class="container mt-4">

        <section class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card-stat">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Linhas Ativas</div>
                            <div class="value">{{ $produtividades->count() }}</div>
                        </div>
                        <div class="icon icon-linhas"><i class="fas fa-industry"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card-stat">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Total Produzido</div>
                            <div class="value">{{ number_format($totalProduzido, 0, ',', '.') }}</div>
                        </div>
                        <div class="icon icon-produzido"><i class="fas fa-box"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card-stat">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Total Defeitos</div>
                            <div class="value">{{ number_format($totalDefeitos, 0, ',', '.') }}</div>
                        </div>
                        <div class="icon icon-defeitos"><i class="fas fa-exclamation-triangle"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card-stat">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Eficiência Geral</div>
                            <div class="value">{{ $eficienciaGeral }}%</div>
                        </div>
                        <div class="icon icon-eficiencia"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="row mb-4">
            <div class="col-md-6 mb-3">
                <article class="section-card">
                    <h6 class="section-title mb-3">Produção vs Defeitos</h6>
                    <canvas id="chartBarras"></canvas>
                </article>
            </div>
            <div class="col-md-6 mb-3">
                <article class="section-card">
                    <h6 class="section-title mb-3">Eficiência por Linha (%)</h6>
                    <canvas id="chartEficiencia"></canvas>
                </article>
            </div>
        </section>

        <section class="section-card mb-5">
            <h6 class="section-title">Detalhamento por Linha de Produção</h6>
            <small class="text-muted d-block mb-3">Janeiro 2026 — Planta A</small>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            @php
                                $columns = [
                                    'linha_produto'   => ['label' => 'Linha do Produto', 'align' => ''],
                                    'total_produzido' => ['label' => 'Qtd. Produzida',   'align' => 'text-right'],
                                    'total_defeitos'  => ['label' => 'Qtd. Defeitos',    'align' => 'text-right'],
                                    'eficiencia'      => ['label' => 'Eficiência',       'align' => 'text-right'],
                                ];
                            @endphp
                            @foreach($columns as $field => $col)
                                @php
                                    $nextOrder = ($sortBy === $field && $order === 'asc') ? 'desc' : 'asc';
                                    $icon = $sortBy === $field
                                        ? ($order === 'asc' ? 'fa-sort-up' : 'fa-sort-down')
                                        : 'fa-sort';
                                @endphp
                                <th class="{{ $col['align'] }}">
                                    <a href="{{ route('dashboard', array_merge(request()->query(), ['sort_by' => $field, 'order' => $nextOrder])) }}" class="sortable-link {{ $sortBy === $field ? 'active' : '' }}">
                                        {{ $col['label'] }}
                                        <i class="fas {{ $icon }} ml-1"></i>
                                    </a>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produtividades as $item)
                            <tr>
                                <td>{{ $item->linha_produto }}</td>
                                <td class="text-right">{{ number_format($item->total_produzido, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item->total_defeitos, 0, ',', '.') }}</td>
                                <td class="text-right">
                                    @php
                                        $ef = $item->eficiencia;
                                        if ($ef >= 97) $badgeClass = 'badge-green';
                                        elseif ($ef >= 95) $badgeClass = 'badge-blue';
                                        elseif ($ef >= 93) $badgeClass = 'badge-yellow';
                                        else $badgeClass = 'badge-red';
                                    @endphp
                                    <span class="badge badge-ef {{ $badgeClass }}">{{ $ef }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="chart-data"
        data-labels='@json($produtividades->pluck('linha_produto'))'
        data-produzidos='@json($produtividades->pluck('total_produzido'))'
        data-defeitos='@json($produtividades->pluck('total_defeitos'))'
        data-eficiencias='@json($produtividades->pluck('eficiencia'))'
    ></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

</body>
</html>

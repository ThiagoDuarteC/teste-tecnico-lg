document.addEventListener('DOMContentLoaded', function () {
    var dataEl = document.getElementById('chart-data');
    var labels      = JSON.parse(dataEl.dataset.labels);
    var produzidos  = JSON.parse(dataEl.dataset.produzidos);
    var defeitos    = JSON.parse(dataEl.dataset.defeitos);
    var eficiencias = JSON.parse(dataEl.dataset.eficiencias);

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#888';

    // Produção vs Defeitos
    new Chart(document.getElementById('chartBarras'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Produzida',
                    data: produzidos,
                    backgroundColor: '#1a73e8',
                    borderRadius: 6,
                    barPercentage: 0.6,
                },
                {
                    label: 'Defeitos',
                    data: defeitos,
                    backgroundColor: '#e57373',
                    borderRadius: 6,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: { mode: 'index', intersect: false },
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, pointStyle: 'circle', padding: 20 }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f0f0f0', drawBorder: false }, ticks: { padding: 8 } },
                x: { grid: { display: false } }
            }
        }
    });

    // Eficiência por Linha
    var cores = eficiencias.map(function (ef) {
        if (ef >= 97) return '#2e7d32';
        if (ef >= 95) return '#1a73e8';
        if (ef >= 93) return '#f9a825';
        return '#c62828';
    });

    new Chart(document.getElementById('chartEficiencia'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Eficiência (%)',
                data: eficiencias,
                backgroundColor: cores,
                borderRadius: 6,
                barPercentage: 0.5,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (ctx) { return ctx.raw + '%'; }
                    }
                }
            },
            scales: {
                x: {
                    min: Math.max(0, Math.floor(Math.min.apply(null, eficiencias)) - 2),
                    max: 100,
                    grid: { color: '#f0f0f0', drawBorder: false },
                    ticks: { callback: function (v) { return v + '%'; }, padding: 8 }
                },
                y: { grid: { display: false } }
            }
        }
    });
});

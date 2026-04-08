<?php

namespace App\Services;

use App\Produtividade;

class ProdutividadeService
{
    public function getDadosDashboard(?string $linhaProduto = null, string $sortBy = 'linha_produto', string $order = 'asc', int $mes = 1, int $ano = 2026): array
    {
        $resumo = Produtividade::selectRaw('
                linha_produto,
                SUM(quantidade_produzida) as total_produzido,
                SUM(quantidade_defeitos) as total_defeitos,
                CASE WHEN SUM(quantidade_produzida) > 0
                    THEN ROUND(((SUM(quantidade_produzida) - SUM(quantidade_defeitos)) / SUM(quantidade_produzida)) * 100, 2)
                    ELSE 0
                END as eficiencia
            ')
            ->whereMonth('data_producao', $mes)
            ->whereYear('data_producao', $ano)
            ->when($linhaProduto, function ($query, $linha) {
                return $query->where('linha_produto', $linha);
            })
            ->groupBy('linha_produto')
            ->orderBy($sortBy, $order)
            ->get();

        $totalProduzido = $resumo->sum('total_produzido');
        $totalDefeitos  = $resumo->sum('total_defeitos');
        $eficienciaGeral = $totalProduzido > 0
            ? round((($totalProduzido - $totalDefeitos) / $totalProduzido) * 100, 2)
            : 0;

        return [
            'resumo'          => $resumo,
            'linhas'          => Produtividade::distinct()->pluck('linha_produto'),
            'totalProduzido'  => $totalProduzido,
            'totalDefeitos'   => $totalDefeitos,
            'eficienciaGeral' => $eficienciaGeral,
        ];
    }
}

<?php

namespace App\Services;

use App\Produtividade;
use Illuminate\Support\Collection;

class ProdutividadeService
{
    public function getDadosDashboard(?string $linhaProduto = null): array
    {
        $produtividades = Produtividade::whereMonth('data_producao', 1)
            ->whereYear('data_producao', 2026)
            ->when($linhaProduto, function ($query, $linha) {
                return $query->where('linha_produto', $linha);
            })
            ->get();

        $resumo = $this->calcularResumoPorLinha($produtividades);

        $totalProduzido = $resumo->sum('total_produzido');
        $totalDefeitos  = $resumo->sum('total_defeitos');

        return [
            'resumo'          => $resumo,
            'linhas'          => Produtividade::distinct()->pluck('linha_produto'),
            'totalProduzido'  => $totalProduzido,
            'totalDefeitos'   => $totalDefeitos,
            'eficienciaGeral' => $this->calcularEficiencia($totalProduzido, $totalDefeitos),
        ];
    }

    private function calcularResumoPorLinha(Collection $produtividades): Collection
    {
        return $produtividades->groupBy('linha_produto')->map(function ($itens, $linha) {
            $totalProduzido = $itens->sum('quantidade_produzida');
            $totalDefeitos = $itens->sum('quantidade_defeitos');

            return (object) [
                'linha_produto'   => $linha,
                'total_produzido' => $totalProduzido,
                'total_defeitos'  => $totalDefeitos,
                'eficiencia'      => $this->calcularEficiencia($totalProduzido, $totalDefeitos),
            ];
        })->values();
    }

    private function calcularEficiencia(int $totalProduzido, int $totalDefeitos): float
    {
        if ($totalProduzido === 0) {
            return 0;
        }

        return round((($totalProduzido - $totalDefeitos) / $totalProduzido) * 100, 2);
    }
}

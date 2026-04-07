<?php

use App\Produtividade;
use Illuminate\Database\Seeder;

class ProdutividadeSeeder extends Seeder
{
    public function run()
    {
        $linhas = ['Geladeira', 'Máquina de Lavar', 'TV', 'Ar-Condicionado'];

        foreach ($linhas as $linha) {
            for ($day = 1; $day <= 31; $day++) {
                Produtividade::create([
                    'linha_produto'        => $linha,
                    'quantidade_produzida' => rand(100, 1000),
                    'quantidade_defeitos'  => rand(0, 50),
                    'data_producao'        => "2026-01-" . str_pad($day, 2, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }
}

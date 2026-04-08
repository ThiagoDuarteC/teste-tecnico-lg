<?php

namespace App\Http\Controllers;

use App\Services\ProdutividadeService;
use Illuminate\Http\Request;

class ProdutividadeController extends Controller
{
    private $service;

    public function __construct(ProdutividadeService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $request->validate([
            'linha_produto' => ['nullable', 'string', 'in:Geladeira,Máquina de Lavar,TV,Ar-Condicionado'],
        ]);

        $linhaSelecionada = $request->query('linha_produto');

        $dados = $this->service->getDadosDashboard($linhaSelecionada);

        return view('dashboard', [
            'produtividades'   => $dados['resumo'],
            'linhas'           => $dados['linhas'],
            'linhaSelecionada' => $linhaSelecionada,
            'totalProduzido'   => $dados['totalProduzido'],
            'totalDefeitos'    => $dados['totalDefeitos'],
            'eficienciaGeral'  => $dados['eficienciaGeral'],
        ]);
    }
}

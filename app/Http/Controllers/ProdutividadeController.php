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
            'sort_by'       => ['nullable', 'string', 'in:linha_produto,total_produzido,total_defeitos,eficiencia'],
            'order'         => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $linhaSelecionada = $request->query('linha_produto');
        $sortBy = $request->query('sort_by', 'linha_produto');
        $order  = $request->query('order', 'asc');

        $dados = $this->service->getDadosDashboard($linhaSelecionada, $sortBy, $order);

        return view('dashboard', [
            'produtividades'   => $dados['resumo'],
            'linhas'           => $dados['linhas'],
            'linhaSelecionada' => $linhaSelecionada,
            'totalProduzido'   => $dados['totalProduzido'],
            'totalDefeitos'    => $dados['totalDefeitos'],
            'eficienciaGeral'  => $dados['eficienciaGeral'],
            'sortBy'           => $sortBy,
            'order'            => $order,
        ]);
    }

    public function detalhes(Request $request)
    {
        $request->validate([
            'linha_produto' => ['required', 'string', 'in:Geladeira,Máquina de Lavar,TV,Ar-Condicionado'],
        ]);

        $detalhes = $this->service->getDetalhesPorLinha($request->query('linha_produto'));

        return response()->json($detalhes);
    }
}

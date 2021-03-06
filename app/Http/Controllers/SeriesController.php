<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeriesFormRequest;
use Illuminate\Http\Request;
use App\Serie;
use App\Services\{CriadorDeSerie, RemovedorDeSerie};
use Illuminate\Support\Facades\DB;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        $series = Serie::query()
            ->orderBy('nome')
            ->get();

        $mensagem = $request->session()->get('mensagem');


        return view('series.index', compact('series', 'mensagem'));
    }

    public function create()
    {
        return view('series.create');
    }

    public function store(SeriesFormRequest $request, CriadorDeSerie $criadorDeSerie)
    {
        $serie = $criadorDeSerie->criarSerie(
            $request->nome,
            $request->qtd_temporadas,
            $request->ep_por_temporada
        );

        $request->session()->flash(
            'mensagem',
            "Série {$serie->nome} adicionada com sucesso."
        );

        return redirect()->route('listar_series');
    }

    public function destroy(Request $request, RemovedorDeSerie $removedorDeSerie)
    {
        $nome = $removedorDeSerie->removerSerie($request->id);

        $request->session()->flash(
            'mensagem',
            "Série $nome removida com sucesso."
        );
        return redirect()->route('listar_series');
    }

    public function editaNome(Request $request)
    {
        $novoNome = $request->nome;
        DB::beginTransaction();
        $serie = Serie::find($request->serieId);
        if ($serie->nome != $novoNome) {
            $serie->nome = $novoNome;
            $serie->save();
        }
        DB::commit();
    }
}

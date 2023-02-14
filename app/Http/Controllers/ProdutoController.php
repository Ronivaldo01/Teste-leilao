<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Produto;
use Illuminate\Http\Request;
use App\Repositories\ProdutoRepository;

class ProdutoController extends Controller
{
    protected $produto;
    public function __construct(Produto $produto) {
        $this->produto = $produto;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produtoRepository = new ProdutoRepository($this->produto);

        

        return response()->json( $produtoRepository->getResultadoPaginado(2), 200 );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->produto->rules());

        

        $produto = $this->produto->create([
            'categoria_id' => $request->categoria_id,
            'nome' => $request->nome
            
        ]);

        return response()->json($produto, 201);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produto = $this->produto->find($id);

        if($produto === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        //remove o arquivo antigo
        Storage::disk('public')->delete($produto->imagem);        

        $produto->delete();
        return response()->json(['msg' => 'O produto foi removida com sucesso!'], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use App\Repositories\CategoriaRepository;

class CategoriaController extends Controller
{
    protected $categoria;

    public function __construct(Categoria $categoria) {
        $this->categoria = $categoria;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $categoriaRepository = new CategoriaRepository($this->categoria);

      /*  if($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $categoriaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $categoriaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if($request->has('filtro')) {
            $categoriaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $categoriaRepository->selectAtributos($request->atributos);
        } 
        */
        return response()->json($categoriaRepository->getResultadoPaginado(3), 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->categoria->rules(), $this->categoria->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $categoria = $this->categoria->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($categoria, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $categoria = $this->categoria->with('produto')->find($id);
        if($categoria === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404) ;
        } 

        return response()->json($categoria, 200);
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
        $categoria = $this->categoria->find($id);

        if($categoria === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            //percorrendo todas as regras definidas no Model
            foreach($categoria->rules() as $input => $regra) {
                
                //coletar apenas as regras aplicáveis aos parâmetros parciais da requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            
            $request->validate($regrasDinamicas, $categoria->feedback());

        }else {
            $request->validate($categoria->rules(), $categoria->feedback());
        }
        
        //remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if($request->file('imagem')) {
            Storage::disk('public')->delete($categoria->imagem);
        }
        
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        //preencher o objeto $categoria com os dados do request
        $categoria->fill($request->all());
        $categoria->imagem = $imagem_urn;
        //dd($marca->getAttributes());
        $categoria->save();
        /*
        $marca->update([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        */

        return response()->json($categoria, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $categoria = $this->categoria->find($id);

        if($categoria === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe'], 404);
        }

        //remove o arquivo antigo
        Storage::disk('public')->delete($categoria->imagem);        

        $categoria->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}

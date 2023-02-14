<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];
    public $table = 'categorias';

    public function rules() {
        return [
            'nome' => 'required|unique:categorias,nome,'.$this->id.'|min:3',
            'imagem' => 'required|file|mimes:png'
        ];

        /*
            1) tabela
            2) nome da coluna que serÃ¡ pesquisada na tabela3
            3) id do registro que serÃ¡ desconsiderado na pesquisa
        */
    }

    public function feedback() {
        return [
            'required' => 'O campo :attribute Ã© obrigatÃ³rio',
            'imagem.mimes' => 'O arquivo deve ser uma imagem do tipo PNG',
            'nome.unique' => 'O nome da marca jÃ¡ existe',
            'nome.min' => 'O nome deve ter no mÃ­nimo 3 caracteres'
        ];
    }

    public function produto() {
        //UMA categoria POSSUI MUITOS produtos
      return $this->hasMany('App\Models\Produto');
    }
}

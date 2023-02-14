<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = ['categoria_id', 'nome'];
    //public $table = "produtos";

    public function rules() {
        return [
            'categoria_id' => 'exists:categorias,id',
            'nome' => 'required|unique:produtos,nome,'.$this->id.'|min:3',
            
        ];
    }

    public function categoria() {
        //UM modelo PERTENCE a UMA marca
        return $this->belongsTo('App\Models\Categoria');
    }
}

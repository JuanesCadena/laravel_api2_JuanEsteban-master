<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;

class Peticione extends Model
{
    use HasFactory;
    protected $fillable = ['titulo', 'descripcion', 'destinatario', 'firmantes', 'estado'   ];

    public function categoria(){
        return $this->belongsTo('App\Models\Categoria');
    }

    public function firmas(){
        //ha sido firmada
        return $this->belongsToMany('App\Models\User', 'peticione_user');
    }

    public function user(){
        //ha sido creada por un usuario
        return $this->belongsTo('App\Models\User');
    }

    public function files(){
        return $this->hasMany(File::class);

    }
}


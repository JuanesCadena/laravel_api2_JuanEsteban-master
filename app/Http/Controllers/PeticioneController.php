<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Peticione;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class PeticioneController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }


    public function index(Request $request)
    {
        $peticiones = Peticione::all();
        return $peticiones;
    }

    public function listMine(Request $request)
    {
        $user = Auth::user();
        $peticiones = Peticione::all()->where('user_id', $user->id);
        return $peticiones;
    }

    public function show(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        return $peticion;
    }

    public function update(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        $res =$peticion->update($request->all());
        if ($res){
            return response()->json(['message' => 'Petición actualizada satisfactoriamente', 'peticion' => $peticion, 201]);
        }
        return response()->json(['message' => 'Error actualizando la petición', 500]);

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required|max:255',
            'descripcion' => 'required',
            'destinatario' => 'required',
            'categoria_id' => 'required',

        ]);
        $input = $request->all();
        $category = Categoria::findOrFail($request->input('categoria_id'));
        $user = Auth::user();
        $user = User::findOrFail($user->id);
        $peticion = new Peticione($input);
        $peticion->user()->associate($user);
        $peticion->categoria()->associate($category);
        $peticion->firmantes = 0;
        $peticion->estado = 'pendiente';
        $res = $peticion->save();
        if ($res) {
            return response()->json(['message' => 'Peticion creada satisfactoriamente', 'peticion' =>$peticion, 201 ]);
        }
        return response()->json(['message' => 'Error creando la petición'], 500 );


        return $peticion;
    }

    public function firmar(Request $request, $id)
    {

        try{
            $peticion = Peticione::findOrFail($id);
            $user= Auth::user();
            $user_id = [$user->id];
            $peticion->firmas()->attach($user_id);
            $peticion->firmantes = $peticion->firmantes + 1;
        }catch (\throwable$th){
            return response()->json(['message' => 'La peticion no se ha podido firmar'], 500);
        }
        if ($peticion->firmas()) {
            return response()->json(['message' => 'Peticion firmada satisfactoriamente', 'peticion' =>$peticion, 201 ]);
        }
        return response()->json(['message' => 'La peticion no se ha podido firmar'], 500);





        $peticion->save();
        return $peticion;
    }

    public function cambiarEstado(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        $peticion->estado = 'aceptada';
        $peticion->save();
        return $peticion;
    }

    public function delete(Request $request, $id)
    {
        $peticion = Peticione::findOrFail($id);
        $res = $peticion->delete();

        if ($res) {
            return response()->json(['message' => 'Peticion creada satisfactoriamente'], 201);
        }
        return response()->json(['message' => 'Error eliminando la peticion'], 500);

    }

}

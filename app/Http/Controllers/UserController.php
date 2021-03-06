<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function config(){
        return view('user.config');
    }
    public function update(Request $request){
        //conseguir usuario del formulario
        $user = \Auth::user();
        $id = $user->id;

        // Validacion del formulario
        $validate = $this->validate($request, [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'nick' => 'required|string|max:255|unique:users,nick,'.$id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$id
        ]);

        // Recoger datos del formulario
        $name = $request->input('name');
        $surname = $request->input('surname');
        $nick = $request->input('nick');
        $email = $request->input('email');

        //Asignar nuevos valores al objeto del usuario

        $user->name=$name;
        $user->surname=$surname;
        $user->nick=$nick;
        $user->email=$email;

        //subir la imagen

        $image_path=$request->file('image_path');
        if($image_path){

            $image_path_full = time().$image_path->getClientOriginalName();

            Storage::disk('users')->put($image_path_full, File::get($image_path));

            $user->image=$image_path_full;
        }

        $user->update();

        return redirect()->route('config')
						 ->with(['message'=>'Usuario actualizado correctamente']);
    }

    public function getImage($filename){
        $file = Storage::disk('users')->get($filename);
        return new Response($file,200);
    }
}

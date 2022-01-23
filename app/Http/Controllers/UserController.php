<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();

        return response()->json([
            "msg" => "¡Registro exitoso!",
        ]);
    }
    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where("email", "=", $request->email)->first();

        if ( isset($user->id) ){
            if(Hash::check($request->password, $user->password)){
                //Se crea el token
                $token = $user->createToken("auth_token")->plainTextToken;
                //Si todo OK
                return response()->json([
                    "msg" => "¡Inicio de sesión exitoso!",
                    "access_token" => $token
                ]);
            }else{
                return response()->json([
                    "msg" => "Contraseña incorrecta",
                ]);
            }
        }else{
            return response()->json([
                "msg" => "Usuario no registrado",
            ], 404);
        }
    }

    public function profile(){
        return response()->json([
            "msg" => "Perfil de usuario: ",
            "data" => auth()->user()
        ]);
    }
    
    public function logout(){

        auth()->user()->tokens()->delete();
        return response()->json([
            "msg" => "Cierre de sesión"
        ]);
    }
}

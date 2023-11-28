<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class PerfilController extends Controller
{   
    public function __construct() {
        $this->middleware('auth');
    }
    public function index(User $user){
        return view('perfil.index', [
            'user' => $user
        ]);
    }

    public function store(Request $request) {

        // Modificar el request
        $request->request->add(['username' => Str::slug($request->username)]);

        $this->validate($request, [
            'username' => ['required','string','min:3','max:20','unique:users,username,' . auth()->user()->id, 'not_in:editar-perfil'],
            'email' => ['required','unique:users,email,' . auth()->user()->id, 'email','max:60']
        ]);

        if($request->imagen) {
            $imagen = $request->file('imagen');

            $nombreImagen = Str::uuid() . "." . $imagen->extension();

            $imagenServidor = Image::make($imagen);
            $imagenServidor->fit(1000, 1000);

            $imagenPath = public_path('profiles') . '/' . $nombreImagen;

            $imagenServidor->save($imagenPath);
        }

        // Guardar cambios
        $usuario = User::find(auth()->user()->id);
        $usuario->username = $request->username;
        $usuario->email = $request->email;

        // Obtener la imagen anterior y eliminarla si existe
        $imagenAnterior = $usuario->imagen;
        if ($imagenAnterior && file_exists(public_path('profiles') . '/' . $imagenAnterior)) {
            unlink(public_path('profiles') . '/' . $imagenAnterior);
        }

        // Si no hay una imagen, busca en la BD, si no hay en la BD asigna null
        $usuario->imagen = $nombreImagen ?? auth()->user()->imagen ?? null;
        $usuario->save();

        // Valida nueva contraseña
        if($request->oldpassword || $request->password) {
            $this->validate($request, [
                'password' => 'required|confirmed',
            ]);
            
            if(Hash::check($request->oldpassword, auth()->user()->password)) {
                $usuario->password = Hash::make($request->password) ?? auth()->user()->password;
                $usuario->save();
            } else {
                return back()->with('mensaje', 'La contraseña actual no coincide');
            }
        }

        // Redireccionar usuario, con el username nuevo
        return redirect()->route('posts.index', $usuario->username);
        
    }
}

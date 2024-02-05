<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //
    public function login(LoginUserRequest $request)
    {
        $date = $request->input("data.attributes");
        $email = $date['email'];
        $user = User::whereEmail($email)->first();
        info("buscando usuario " . $user);
        if (!$user) {
            // Si no se encontró un usuario, lanzar una excepción de validación
            info("Usuario no encontrado");
            return response()->json([
                'errors' => [
                    [
                        'status' => '422',
                        'title' => 'Invalid Credentials',
                        'detail' => 'The provided credentials do not match our records.',
                    ]
                ]
            ], 422);
        }
        $password = $date['password'];
        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '422',
                        'title' => 'Invalid Credentials',
                        'detail' => 'The provided credentials do not match our records.',
                    ]
                ]
            ], 422);
        }
        $device_token = $date['device_name'];
        $token = $user->createToken($device_token)->plainTextToken;
        return (new UserResource($user))->additional([
            "meta" => [
                "token" => $token
            ]
        ]);
    }

    public function register(UserRequest $request)
    {
        $user = new User($request->input("data.attributes"));
//        $user->password = Hash::make($user->password);
        $user->save();
        $device_name = $request->input("data.attributes.device_name");
        $token = $user->createToken($device_name, [])->plainTextToken;
        info("Se ha creado el token " . $token);
        $retorno = ((new UserResource($user))
            ->additional(["meta" => ["token" => $token]])
            ->response()
            ->setStatusCode(201));

        info("Se va a devolver " . $retorno);
        return $retorno;
    }
    public function getUser(Request $request)
    {
        info ("solicitando usuario ");
        return response()->json($request->user());
    }

}

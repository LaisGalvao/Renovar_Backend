<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function regist(Request $request){
        $request-> validate([
            //
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'password' =>  ['required', 'string', 'max:255']
        ]);
        $user = User::create([
            'name'=> $request->name,
            'email'=>$request->email,
            'password'=> bcrypt($request->password)
        ]);

        $token = $user->createToken($request->email)->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response()->json($response, 200);
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Digite as credenciais corretas']
            ]);
        }
        $token = $user->createToken($request->email)->plainTextToken;
         $response = [
            'user' => $user,
            'token' => $token
        ];
        return response()->json($response, 200);

    }

    public function user(){
        $user = auth()->user();
        return response($user, 200);
    }

    public function logout(){
       // auth()->user()->tokens()->delete();
        return response([], 200);
    }
}

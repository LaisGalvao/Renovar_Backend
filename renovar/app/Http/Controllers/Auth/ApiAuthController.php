<?php

namespace App\Http\Controllers\Auth;

use DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SendNewUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Logger\ConsoleLogger;

class ApiAuthController extends Controller
{
    public function edit(Request $request)
    {
        $user = User::find($request['user_id']);

        if ($user == null) {
            return response(['errors' => 'User not found'], 422);
        }

        return $this->saveEdit($request, $user);
    }

    public function update(Request $request, User $user)
    {
        if ($user->id != Auth::id()) {
            return response(['errors' => 'You do not have permission to edit this user'], 422);
        }

        return $this->saveEdit($request, $user);
    }

    private function saveEdit(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255',
            'password' => 'string|min:6|confirmed',
            'telephone' => 'string|min:11|celular_com_ddd',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }


        $user->name = (isset($request['name'])) ? $request['name'] : $user->name;
        $user->email = (isset($request['email'])) ? $request['email'] : $user->email;
        $user->password = (isset($request['password'])) ? Hash::make($request['password']) : $user->password;
        $user->telephone = (isset($request['telephone'])) ? $request['telephone'] : $user->telephone;
        $this->linkGroup($user, $request);

        $user->save();

        $response = ['user' => $user];

        return response($response, 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'telephone' => 'required|string|min:11|celular_com_ddd',
            'group_key' => 'required|string|max:255',
            'cpf' => 'required_without:document|string|min:11',
            'document' => 'required_without:cpf|string|min:11'
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $cpf = isset($request->cpf) ? $request->cpf : $request->document;
        if (!$cpf) {
            return response(['errors' => "Document is required"], 422);
        } else {
            $request->cpf = $cpf;
        }

        $users = User::where('cpf', $request->cpf)->get();


        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);

        $user = User::create($request->toArray());
        $request['user_id'] =  $user->id;


         //envio de email com os dados do cliente para o comercial da shinier
         //Mail::to('laisgbueno62@gmail.com')->send(
            //new SendNewUser($request->all())
        //);

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token, 'user' => $user];

        return response($response, 200);
    }


    public function registerStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:240',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'telephone' => 'required|string|min:11|celular_com_ddd',
            'cpf' => 'string|min:11',
            'document' => 'string|min:11',
            'type' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $cpf = isset($request->cpf) ? $request->cpf : $request->document;
        if (!$cpf) {
            return response(['errors' => "Document is required"], 422);
        } elseif (User::where('cpf', $cpf)->first()) {
            return response(['errors' => "The document has already been taken"], 422);
        }

        return $this->register($request);
    }


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'telephone' => 'required|string|min:11|celular_com_ddd',
            'cpf' => 'string|min:11',
            'document' => 'string|min:11',
            'group_key' => 'string|max:255',
            'group_keys' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $cpf = isset($request->cpf) ? $request->cpf : $request->document;
        if (!$cpf) {
            return response(['errors' => "Document is required"], 422);
        } else {
            $request->cpf = $cpf;
        }

        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);

        $user = User::create($request->toArray());

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token, 'user' => $user];
        return response($response, 200);
    }


    private function sendEmail(Request $request, $group_key, $efvService)
    {
     /*
        $email = collect([]);

        if ($email->count() > 0) {
            (new EmailController)->sendEmail($email, ['userEmail' => $request->email, 'userName' => $request->name, 'subject' => 'Bem-vindo ao nosso app!', 'name' => $group->name, 'body' => view('emails.welcome', ['key' => $group->key, 'name' => $group->name, 'color' => $color])]);
        } */
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cpf' => 'string|min:11',
            'document' => 'string|min:11',
            'password' => 'required|string|min:6',
            'group_key' => 'required|string|max:40'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $cpf = isset($request->cpf) ? $request->cpf : $request->document;

        if (!$cpf) {
            return response(['errors' => "Document is required"], 422);
        } else {
            $request->cpf = $cpf;
        }

        $user = User::where('cpf', $request->cpf)->whereHas('groups', function ($group) use ($request) {
            $group->where('key', $request->group_key);
        })->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if (isset($request['token'])) {
                    $request['user_id'] =  $user->id;
                }
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token, 'user' => $user];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => "User not found"];
            return response($response, 404);
        }
    }


    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}

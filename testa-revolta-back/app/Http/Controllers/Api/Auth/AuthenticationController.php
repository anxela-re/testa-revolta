<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login(Request $request) {
        $this->validate($request, [
            'email' => 'required|max:255',
            'password' => 'required'
        ]);

        $login = $request->only('email', 'password');

        if(!Auth::attempt($login)) {
            return response(['message' => 'Invalid login credentials'], 401);
        }

        /**
         * @var User $user
         */
        $user = Auth::user();
        $token = $user->createToken($user->name);
        
        return response([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'access_token' => $token->accessToken,
            'token_expires_at' => $token->token->expires_at,

        ], 200);
    }
}

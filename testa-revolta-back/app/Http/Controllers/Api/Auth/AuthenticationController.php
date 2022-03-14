<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|max:255',
            'password' => 'required'
        ]);

        $login = $request->only('email', 'password');

        if (!Auth::attempt($login)) {
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

    public function logout(Request $request)
    {
        $this->validate($request, [
            'allDevices' => 'required|boolean'
        ]);

        $user = Auth::user();

        if ($request->allDevice) {
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response(['message' => 'Logged out from all devices', 200]);
        } else {
            $userToken = $user->token();
            $userToken->delete();
            return response(['message' => 'Logged out ', 200]);
        }
    }
}

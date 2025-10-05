<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        $user = User::where('email',$request->email)->first();


        if(!$user || !Hash::check($request->password, $user->password)){

            throw ValidationException::withMessages([
                'email'=>'The Credential you provided are incorrect.'
            ]);
        }

        $token =  $user->createToken('laravel_api_tester')->plainTextToken;

        return response()->json([
            'token'=>$token,
            'user'=>$user
        ],Response::HTTP_OK);
    }



}

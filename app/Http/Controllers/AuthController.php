<?php
/**
 * user: ted
 * date:12/01/2023
 */

 namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * Class AuthController
 *
 * @author  Zura Sekhniashvili <zurasekhniashvili@gmail.com>
 * @package App\Http\Controllers
 */


class AuthController extends Controller
{
    public function register(request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|string|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                password::min(8)->mixedCase()->numbers()->symbols()
            ]
        ]);

        // for id purposes you can add the following line
        // it basically tells the id that the user is an instance of User model
        /** @var \App\Models\User $user */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
        //create token on the user
        $token = $user->createToken('main')->plainTextToken;
        

        return response([
            'user' => $user,
            'token' => $token
        ]);
    }


    //login
     public function login(Request $request)
     {
         $credentials = $request->validate([
             'email' => 'required|email|string|exists:users,email',
             'password' => [
                 'required',
             ],
             'remember' => 'boolean'
         ]);
         $remember = $credentials['remember'] ?? false;
         unset($credentials['remember']);
         if (!Auth::attempt($credentials, $remember)) {
             return response([
                 'error' => 'The provided credentials are incorrect'
             ], 422);
         }
         $user = Auth::user();
         $token = $user->createToken('main')->plainTextToken;
         return response([
             'user' => $user,
             'token' => $token
         ]);
     }

     public function logout()
     {
        /**@var User $user */
        $user = Auth::user();
        //revoke the token or delete in other words
        $user->currentAccessToken()->delete();

        return response([
            'success' => true
        ]);

     }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserController extends Controller
{
    public function verifyUserInfo(Request $request) {
        // dd($request);
        $user = User::where('email', '=', $request->email)->first();
        if($user == null) {
            //如果沒有client有帳號，要建立
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
        }
        Auth::login($user);
        return redirect()->route('login');
    }
}

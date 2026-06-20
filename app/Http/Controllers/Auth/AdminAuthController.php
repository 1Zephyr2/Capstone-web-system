<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view("auth.admin-login");
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"],
        ]);

        // Specifically check for the 'admin' role
        $authData = array_merge($credentials, ["role" => "admin"]);

        if (Auth::attempt($authData)) {
            $request->session()->regenerate();
            return redirect()->intended(route("admin.dashboard"));
        }

        return back()->withErrors([
            "email" => "Invalid admin credentials.",
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffAuthController extends Controller
{
    public function showLoginForm()
    {
        return view("auth.staff-login");
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"],
        ]);

        if (auth()->attempt($credentials + ["role" => "staff"])) {
            $request->session()->regenerate();
            return redirect()->intended("/dashboard");
        }

        return back()->withErrors(["email" => "Invalid staff credentials."]);
    }
}

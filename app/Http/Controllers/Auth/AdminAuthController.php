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

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (strtolower($user->role) === "admin") {
                $request->session()->regenerate();
                return redirect()->intended(route("admin.dashboard"));
            } else {
                Auth::logout();
                return back()->withErrors([
                    "email" => "Unauthorized access: Not an admin account.",
                ]);
            }
        }

        return back()->withErrors([
            "email" => "Invalid credentials.",
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/");
    }
}

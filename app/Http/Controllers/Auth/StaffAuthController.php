<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Merge credentials with the role check explicitly
        $authData = array_merge($credentials, ["role" => "staff"]);

        if (Auth::attempt($authData)) {
            $request->session()->regenerate();
            return redirect()->intended(route("staff.dashboard"));
        }

        return back()->withErrors([
            "email" => "Invalid staff credentials.",
        ]);
    }
}

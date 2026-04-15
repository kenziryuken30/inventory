<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->withErrors(['username' => 'User tidak ditemukan']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['username' => 'Password salah']);
        }

        Auth::login($user);

        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'User ' . $user->name . ' login',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->last_login = now();
        $user->save();

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
    public function logout(Request $request)
    {
        $userId = Auth::id(); 

        DB::table('activity_logs')->insert([
            'user_id' => $userId,
            'action' => 'logout',
            'description' => 'User logout',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

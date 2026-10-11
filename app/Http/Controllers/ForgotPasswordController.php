<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /**
     * Show the direct password reset form.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Directly reset and update the user's password without email/WA verification tokens.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Email atau Nomor Telepon wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $identifier = trim($request->input('email'));

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Alamat email atau nomor telepon tidak ditemukan dalam sistem.',
            ])->withInput();
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('login')->with('success', 'Password berhasil diperbarui! Silakan masuk dengan password baru Anda.');
    }
}

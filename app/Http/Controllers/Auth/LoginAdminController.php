<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginAdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating admin users for the application
    | and redirecting them to your admin dashboard. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect admins after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware untuk tamu dan logout
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the admin login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login.admin');
    }

    /**
     * Handle an admin login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validasi data form
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Login menggunakan guard default dengan syarat role = admin
        if (auth()->attempt([
            'username' => $request->username,
            'password' => $request->password,
            'role' => 'admin'
        ], $request->remember)) {
            // Jika berhasil, redirect ke dashboard admin
            return redirect()->intended($this->redirectTo);
        }

        // Jika gagal, redirect kembali dengan pesan error
        return redirect()->back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors([
                'username' => 'Username atau password salah, atau Anda bukan admin.',
            ]);
    }

    /**
     * Log the admin out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Logout user
        auth('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin');
    }
}

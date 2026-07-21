<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    // --- VISTA LOGIN ---
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        $barberia = \App\Models\Barberia::where('slug', 'barberia-principal')->first();
        return view('auth.login', compact('barberia'));
    }

    // --- PROCESAR LOGIN ---
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // --- VISTA REGISTRO ---
    public function showRegisterForm()
    {
        $barberia = \App\Models\Barberia::where('slug', 'barberia-principal')->first();
        return view('auth.register', compact('barberia'));
    }

    // --- PROCESAR REGISTRO ---
// En app/Http/Controllers/Auth/LoginController.php

public function register(Request $request)
{
    // 1. Validamos los datos recibidos
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'role' => ['required', 'string', 'in:admin,barbero'],
    ]);

    // 2. Obtenemos el ID de la barbería activa para asegurar la relación
    // Esto garantiza que el nuevo trabajador aparezca en la lista del admin
    $barberiaActiva = \App\Models\Barberia::where('slug', 'barberia-principal')->first();
    $barberiaId = $barberiaActiva ? $barberiaActiva->id : 1;

    // 3. Creamos al usuario con todos los campos necesarios
    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'role' => $request->role,
        'barberia_id' => $barberiaId,
    ]);

    // 4. Iniciamos sesión automáticamente
    \Illuminate\Support\Facades\Auth::login($user);

    // 5. Redireccionamos al dashboard correspondiente
    return $this->redirectBasedOnRole($user);
}
    // --- LOGOUT ---
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }

    // --- REDIRECCIÓN ---
    private function redirectBasedOnRole($user)
    {
        $role = Str::lower($user->role);
        if ($role === 'admin') {
            return redirect()->intended(route('dashboard'));
        } elseif ($role === 'barbero') {
            return redirect()->intended(route('barbero.dashboard'));
        }

        Auth::logout();
        return redirect()->route('login')->withErrors(['email' => 'Acceso no permitido.']);
    }
}
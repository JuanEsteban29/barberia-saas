<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Barber ERP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-slate-900/50 backdrop-blur-md border border-slate-800 p-8 rounded-2xl shadow-2xl">
        <h1 class="text-2xl font-extrabold text-white text-center mb-6">Crear Cuenta</h1>
        
        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div><label class="text-xs text-slate-400 uppercase font-bold">Nombre</label>
                <input type="text" name="name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-4 mt-1 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none"></div>
            <div><label class="text-xs text-slate-400 uppercase font-bold">Correo</label>
                <input type="email" name="email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-4 mt-1 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none"></div>
            <div><label class="text-xs text-slate-400 uppercase font-bold">Contraseña</label>
                <input type="password" name="password" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-4 mt-1 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none"></div>
            <div><label class="text-xs text-slate-400 uppercase font-bold">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-4 mt-1 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none"></div>
            <!-- Campo de Rol -->
<div>
    <label class="text-xs text-slate-400 uppercase font-bold">Rol de Usuario</label>
    <select name="role" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2 px-4 mt-1 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none text-white">
        <option value="barbero">Barbero</option>
        <option value="admin">Administrador</option>
    </select>
</div>
            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl mt-4 transition">Registrarse</button>
        </form>
        <p class="text-center mt-4 text-sm text-slate-500"><a href="{{ route('login') }}" class="text-amber-400 hover:underline">¿Ya tienes cuenta? Inicia sesión</a></p>
    </div>
</body>
</html>
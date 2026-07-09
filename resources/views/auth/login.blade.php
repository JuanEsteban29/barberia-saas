<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Barber ERP</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <div class="w-full max-w-md bg-slate-900/50 backdrop-blur-md border border-slate-800 p-8 rounded-2xl shadow-2xl relative z-10">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-3xl mb-4">💈</div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white">Barber <span class="text-amber-400">ERP</span></h1>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Correo Electrónico</label>
                <input type="email" name="email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Contraseña</label>
                <input type="password" name="password" required class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white">
            </div>
            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-sm transition">
                Ingresar al Sistema
            </button>
        </form>

        <!-- ENLACE NUEVO -->
        <div class="mt-6 text-center">
            <a href="{{ route('register') }}" class="text-xs text-amber-400 hover:underline font-bold uppercase">
                ¿No tienes cuenta? Regístrate aquí
            </a>
        </div>
    </div>
</body>
</html>
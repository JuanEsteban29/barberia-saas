@php
    $barberia = $barberia ?? \App\Models\Barberia::where('slug', 'barberia-principal')->first();
    $nombreNegocio = $barberia->nombre ?? 'Mi Barbería';
    $logoNegocio = $barberia->logo ?? null;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $nombreNegocio }} - Iniciar Sesión</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <div class="w-full max-w-md bg-slate-900/50 backdrop-blur-md border border-slate-800 p-8 rounded-2xl shadow-2xl relative z-10">
        <div class="text-center mb-8">
            @if($logoNegocio)
                <img src="{{ $logoNegocio }}" alt="Logo" class="w-16 h-16 object-contain rounded-2xl border border-slate-800 mx-auto mb-3 shadow-lg">
            @else
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-3xl mb-3 shadow-lg">💈</div>
            @endif
            <h1 class="text-2xl font-extrabold tracking-tight text-white uppercase">{{ $nombreNegocio }}</h1>
            <p class="text-[10px] uppercase tracking-widest font-bold mt-1 text-amber-500">TRIM</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
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
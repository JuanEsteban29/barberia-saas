<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cita - {{ $barberia->nombre }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Fondo de luces decorativas (Glow) -->
    <div class="absolute top-0 -left-40 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 -right-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>

    <div class="w-full max-w-xl bg-slate-900/60 backdrop-blur-md border border-slate-800 p-8 rounded-3xl shadow-2xl relative z-10 my-6">
        
        <!-- Encabezado / Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-3xl mb-4">💈</div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white">{{ $barberia->nombre }}</h1>
            <p class="text-sm text-slate-400 mt-2">Agenda tu cita en segundos de forma rápida y sencilla.</p>
        </div>

        <!-- Mensaje de éxito -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Mensajes de error -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario -->
        <form action="{{ route('reservas.public.store', $barberia->slug) }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Nombre Completo -->
            <div>
                <label for="cliente_nombre" class="block text-xs font-bold uppercase text-slate-400 mb-2">
                    Tu Nombre Completo <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-regular fa-user text-sm"></i>
                    </span>
                    <input type="text" name="cliente_nombre" id="cliente_nombre" required 
                           placeholder="Ingresa tu nombre y apellido" 
                           value="{{ old('cliente_nombre') }}"
                           class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition">
                </div>
            </div>

            <!-- Fila: Teléfono y Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Teléfono -->
                <div>
                    <label for="cliente_telefono" class="block text-xs font-bold uppercase text-slate-400 mb-2">
                        Teléfono Móvil
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-solid fa-phone text-xs"></i>
                        </span>
                        <input type="tel" name="cliente_telefono" id="cliente_telefono" 
                               placeholder="Ej. +34 600 000 000" 
                               value="{{ old('cliente_telefono') }}"
                               class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition">
                    </div>
                </div>

                <!-- Correo Electrónico -->
                <div>
                    <label for="cliente_email" class="block text-xs font-bold uppercase text-slate-400 mb-2">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="cliente_email" id="cliente_email" 
                               placeholder="tu@correo.com" 
                               value="{{ old('cliente_email') }}"
                               class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition">
                    </div>
                </div>
            </div>

            <!-- Fila: Barbero y Servicio -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Seleccionar Barbero -->
                <div>
                    <label for="barbero_id" class="block text-xs font-bold uppercase text-slate-400 mb-2">
                        Elegir Barbero <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-solid fa-user-tie text-sm"></i>
                        </span>
                        <select name="barbero_id" id="barbero_id" required 
                                class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-10 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition appearance-none cursor-pointer">
                            <option value="" class="bg-slate-950">-- Elige un Barbero --</option>
                            @foreach($barberos as $barbero)
                                <option value="{{ $barbero->id }}" class="bg-slate-950" {{ old('barbero_id') == $barbero->id ? 'selected' : '' }}>
                                    {{ $barbero->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Seleccionar Servicio -->
                <div>
                    <label for="servicio_id" class="block text-xs font-bold uppercase text-slate-400 mb-2">
                        Elegir Servicio <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-solid fa-scissors text-sm"></i>
                        </span>
                        <select name="servicio_id" id="servicio_id" required 
                                class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-10 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition appearance-none cursor-pointer">
                            <option value="" class="bg-slate-950">-- Elige un Servicio --</option>
                            @foreach($servicios as $servicio)
                                <option value="{{ $servicio->id }}" class="bg-slate-950" {{ old('servicio_id') == $servicio->id ? 'selected' : '' }}>
                                    {{ $servicio->nombre }} (${{ number_format($servicio->precio, 2) }} USD)
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fecha y Hora -->
            <div>
                <label for="fecha_hora" class="block text-xs font-bold uppercase text-slate-400 mb-2">
                    Elegir Fecha y Hora <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-regular fa-clock text-sm"></i>
                    </span>
                    <input type="datetime-local" name="fecha_hora" id="fecha_hora" required 
                           value="{{ old('fecha_hora') }}"
                           class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition cursor-pointer">
                </div>
            </div>

            <!-- Botón de Envío -->
            <button type="submit" 
                    class="w-full py-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black rounded-xl text-sm transition uppercase tracking-wider cursor-pointer shadow-lg shadow-amber-500/10 flex items-center justify-center gap-2">
                <i class="fa-regular fa-calendar-check text-base"></i>
                <span>Reservar Cita Ahora</span>
            </button>
        </form>
    </div>
</body>
</html>

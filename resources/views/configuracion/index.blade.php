@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up space-y-5">

    <!-- Header -->
    <div>
        <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1 flex items-center gap-2">
            <i class="fa-solid fa-sliders"></i> Panel de Configuración
        </p>
        <h1 class="text-2xl md:text-4xl font-black text-white tracking-tight">Ajustes del Sistema</h1>
        <p class="text-slate-400 mt-1 text-xs md:text-sm">Personaliza el comportamiento de comisiones, tipo de cambio e información del negocio.</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="max-w-3xl">
        <form action="{{ route('barberia.configuracion.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Card: Datos del Negocio -->
            <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-shop text-amber-400 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-white text-sm uppercase tracking-widest">Información de la Barbería</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Nombre del Negocio</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-font text-sm"></i></span>
                                <input type="text" name="nombre" value="{{ old('nombre', $barberia->nombre) }}" required
                                    class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Porcentaje Comisión por Defecto (%)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-percent text-sm"></i></span>
                                <input type="number" name="porcentaje_barbero" min="0" max="100" value="{{ old('porcentaje_barbero', $barberia->porcentaje_barbero) }}" required
                                    class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition">
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Este porcentaje se aplicará a todos los barberos que no tengan configurado un porcentaje individual.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center border-t border-slate-800/60 pt-4 mb-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Logo de la Barbería</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><i class="fa-solid fa-image text-sm"></i></span>
                                <input type="file" name="logo" accept="image/*"
                                    class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-2.5 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition file:mr-4 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[10px] file:font-black file:bg-amber-500 file:text-black hover:file:bg-amber-400 cursor-pointer">
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1">Sube una imagen cuadrada de preferencia (JPG, PNG, máx. 2MB).</p>
                        </div>
                        <div class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/40 min-h-[90px]">
                            <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-2">Logo Actual</p>
                            @if($barberia->logo)
                                <img src="{{ $barberia->logo }}" alt="Logo" class="w-12 h-12 object-contain rounded-lg border border-slate-800">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                                    <i class="fa-solid fa-scissors text-amber-500 text-base"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Link Público de Reservas para Clientes</label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="linkPublico" readonly value="{{ route('reservas.public.create', $barberia->slug) }}" 
                                class="flex-1 bg-slate-950/40 border border-slate-800 rounded-xl py-3 px-4 text-sm text-slate-400 focus:outline-none cursor-default">
                            <button type="button" id="btnCopiar"
                                onclick="copiarLink()"
                                class="bg-slate-800 hover:bg-amber-600 text-slate-300 hover:text-white font-bold px-4 py-3 rounded-xl transition text-sm flex items-center gap-2 cursor-pointer">
                                <i class="fa-regular fa-copy" id="iconoCopiar"></i> <span id="textoCopiar" class="hidden sm:inline">Copiar</span>
                            </button>
                            <a href="{{ route('reservas.public.create', $barberia->slug) }}" target="_blank"
                                class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-4 py-3 rounded-xl transition text-sm flex items-center gap-2">
                                <i class="fa-solid fa-up-right-from-square"></i> <span class="hidden sm:inline">Ver</span>
                            </a>
                        </div>
                        <p class="text-[10px] text-slate-600 mt-1">Comparte este link con tus clientes para que puedan agendar citas en línea.</p>
                    </div>
                </div>
            </div>

            <!-- Card: Tasa de Cambio (BCV) -->
            <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-transfer text-amber-400 text-sm"></i>
                    </div>
                    <h3 class="font-bold text-white text-sm uppercase tracking-widest">Tasa del Dólar (BCV)</h3>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-3 tracking-wider">Modo de Tasa de Cambio</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-start p-4 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-amber-500/40 hover:bg-amber-500/5 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/10 group">
                                <input type="radio" name="tasa_bcv_modo" value="auto" {{ old('tasa_bcv_modo', $barberia->tasa_bcv_modo) === 'auto' ? 'checked' : '' }} onchange="toggleTasaInput(this.value)" class="sr-only">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-800 border border-slate-700 mr-3 group-has-[:checked]:border-amber-500 group-has-[:checked]:text-amber-400 text-slate-500">
                                    <i class="fa-solid fa-robot text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-white group-has-[:checked]:text-amber-400">Tasa Automática (Recomendado)</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Se actualiza en tiempo real utilizando la tasa oficial del Banco Central de Venezuela.</p>
                                </div>
                            </label>

                            <label class="flex items-start p-4 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-amber-500/40 hover:bg-amber-500/5 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/10 group">
                                <input type="radio" name="tasa_bcv_modo" value="manual" {{ old('tasa_bcv_modo', $barberia->tasa_bcv_modo) === 'manual' ? 'checked' : '' }} onchange="toggleTasaInput(this.value)" class="sr-only">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-800 border border-slate-700 mr-3 group-has-[:checked]:border-amber-500 group-has-[:checked]:text-amber-400 text-slate-500">
                                    <i class="fa-solid fa-keyboard text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-white group-has-[:checked]:text-amber-400">Tasa Manual / Fija</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Permite fijar manualmente la tasa en bolívares según las necesidades del día.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Input para Tasa Manual -->
                    <div id="tasa_manual_wrapper" style="display: {{ old('tasa_bcv_modo', $barberia->tasa_bcv_modo) === 'manual' ? 'block' : 'none' }};" class="animate-fade-in">
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Valor de la Tasa Manual (Bs.)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500"><strong class="text-xs">Bs.</strong></span>
                            <input type="number" step="0.01" name="tasa_bcv_manual" id="tasa_bcv_manual_input" 
                                value="{{ old('tasa_bcv_manual', $barberia->tasa_bcv_manual ?? $tasaBcv) }}"
                                class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition"
                                {{ old('tasa_bcv_modo', $barberia->tasa_bcv_modo) === 'manual' ? 'required' : '' }}>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1">Ingresa el valor del dólar en Bolívares (ej: 46.25).</p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('dashboard') }}"
                    class="px-6 py-3 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors cursor-pointer">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-3 text-sm font-black text-black bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 rounded-xl transition-all shadow-lg shadow-amber-500/20 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleTasaInput(val) {
        const wrapper = document.getElementById('tasa_manual_wrapper');
        const input = document.getElementById('tasa_bcv_manual_input');
        if (val === 'manual') {
            wrapper.style.display = 'block';
            input.setAttribute('required', 'required');
        } else {
            wrapper.style.display = 'none';
            input.removeAttribute('required');
        }
    }

    function copiarLink() {
        const link = document.getElementById('linkPublico').value;
        navigator.clipboard.writeText(link).then(() => {
            const btn   = document.getElementById('btnCopiar');
            const icon  = document.getElementById('iconoCopiar');
            const texto = document.getElementById('textoCopiar');
            icon.className  = 'fa-solid fa-circle-check';
            btn.classList.remove('bg-slate-800', 'text-slate-300');
            btn.classList.add('bg-emerald-600', 'text-white');
            if (texto) texto.textContent = '¡Copiado!';
            setTimeout(() => {
                icon.className  = 'fa-regular fa-copy';
                btn.classList.remove('bg-emerald-600', 'text-white');
                btn.classList.add('bg-slate-800', 'text-slate-300');
                if (texto) texto.textContent = 'Copiar';
            }, 2000);
        });
    }
</script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.4s ease-out forwards; }
</style>
@endsection

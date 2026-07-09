@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- COLUMNA IZQUIERDA: Formulario de Registro de Corte -->
    <div class="bg-slate-900/50 backdrop-blur-md p-7 rounded-2xl border border-slate-800/60 shadow-xl h-fit">
        <h2 class="text-white font-black text-lg mb-1 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                <i class="fa-solid fa-plus text-amber-400 text-sm"></i>
            </div>
            Registrar Corte
        </h2>
        <p class="text-xs text-slate-400 mb-6 pl-11">El servicio se integra automáticamente en cajas y comisiones.</p>

        @if(session('success'))
            <div class="mb-5 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('cortes.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Nombre del Cliente</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-regular fa-user text-sm"></i>
                    </span>
                    <input type="text" name="cliente_nombre" placeholder="Cliente General"
                        class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition placeholder-slate-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Barbero Asignado <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-user-tie text-sm"></i>
                    </span>
                    <select name="barbero_id" required
                        class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-10 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition appearance-none cursor-pointer">
                        <option value="" class="bg-slate-950">-- Elige el barbero --</option>
                        @foreach($barberosDisponibles as $barbero)
                            <option value="{{ $barbero->id }}" class="bg-slate-950">{{ $barbero->name }} ({{ $barbero->role }})</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Tipo de Servicio <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-scissors text-sm"></i>
                    </span>
                    <select name="servicio_id" required
                        class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-10 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition appearance-none cursor-pointer">
                        <option value="" class="bg-slate-950">-- Elige el servicio --</option>
                        @foreach($serviciosDisponibles as $servicio)
                            <option value="{{ $servicio->id }}" class="bg-slate-950">
                                {{ $servicio->nombre }} — ${{ number_format($servicio->precio, 2) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Método de Pago -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider">Método de Pago / Estado</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-emerald-500/50 hover:bg-emerald-500/5 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10 group">
                        <input type="radio" name="metodo_pago" value="efectivo" checked class="sr-only">
                        <i class="fa-solid fa-money-bill-wave text-emerald-400 text-base mb-1.5 group-has-[:checked]:text-emerald-300"></i>
                        <span class="text-xs font-bold text-slate-400 group-has-[:checked]:text-emerald-400">Efectivo</span>
                    </label>
                    <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-blue-500/50 hover:bg-blue-500/5 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/10 group">
                        <input type="radio" name="metodo_pago" value="transferencia" class="sr-only">
                        <i class="fa-solid fa-building-columns text-blue-400 text-base mb-1.5"></i>
                        <span class="text-xs font-bold text-slate-400 group-has-[:checked]:text-blue-400">Banco</span>
                    </label>
                    <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-rose-500/50 hover:bg-rose-500/5 transition has-[:checked]:border-rose-500 has-[:checked]:bg-rose-500/10 group">
                        <input type="radio" name="metodo_pago" value="fiado" class="sr-only">
                        <i class="fa-solid fa-handshake text-rose-400 text-base mb-1.5"></i>
                        <span class="text-xs font-bold text-slate-400 group-has-[:checked]:text-rose-400">Fiado</span>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full py-4 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black rounded-xl text-sm transition uppercase tracking-wider cursor-pointer shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 mt-2">
                <i class="fa-solid fa-scissors"></i>
                Guardar Corte
            </button>
        </form>
    </div>

    <!-- COLUMNA DERECHA: Totales e Historial -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Mini Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-slate-900/50 backdrop-blur-md p-5 rounded-2xl border border-slate-800/60 shadow-lg">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-wave text-emerald-400 text-sm"></i>
                    </div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Efectivo</span>
                </div>
                <span class="text-2xl font-black text-emerald-400">${{ number_format($dineroEfectivo, 2) }}</span>
            </div>
            <div class="bg-slate-900/50 backdrop-blur-md p-5 rounded-2xl border border-slate-800/60 shadow-lg">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-building-columns text-blue-400 text-sm"></i>
                    </div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Transferencias</span>
                </div>
                <span class="text-2xl font-black text-blue-400">${{ number_format($dineroTransferencia, 2) }}</span>
            </div>
            <div class="bg-gradient-to-br from-amber-900/30 to-slate-900/60 backdrop-blur-md p-5 rounded-2xl border border-amber-800/30 shadow-lg">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-vault text-amber-400 text-sm"></i>
                    </div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Caja</span>
                </div>
                <span class="text-2xl font-black text-amber-400">${{ number_format($totalRecaudado, 2) }}</span>
            </div>
        </div>

        <!-- Historial de Cortes -->
        <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
            <div class="px-7 py-5 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60">
                <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Historial de Cortes
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                            <th class="px-7 py-3 font-bold">Fecha</th>
                            <th class="px-7 py-3 font-bold">Cliente</th>
                            <th class="px-7 py-3 font-bold">Barbero</th>
                            <th class="px-7 py-3 font-bold">Servicio</th>
                            <th class="px-7 py-3 text-center font-bold">Estado</th>
                            <th class="px-7 py-3 text-right font-bold">Precio</th>
                            <th class="px-7 py-3 text-center font-bold">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($historialCortes as $corte)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-7 py-4 text-xs text-slate-500 font-medium">
                                    {{ $corte->fecha_hora->format('d/m H:i') }}
                                </td>
                                <td class="px-7 py-4 font-bold text-white">
                                    {{ $corte->cliente->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-7 py-4 text-slate-400">
                                    {{ $corte->barbero->name ?? 'N/A' }}
                                </td>
                                <td class="px-7 py-4">
                                    <span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2.5 py-1 rounded-md font-medium">
                                        {{ $corte->servicio->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-7 py-4 text-center">
                                    @if($corte->estado === 'pendiente')
                                        <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold px-2.5 py-1 rounded-md">Reservado</span>
                                    @elseif($corte->estado === 'fiado')
                                        <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold px-2.5 py-1 rounded-md">Fiado</span>
                                    @elseif($corte->metodo_pago === 'transferencia')
                                        <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold px-2.5 py-1 rounded-md">Banco</span>
                                    @else
                                        <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold px-2.5 py-1 rounded-md">Efectivo</span>
                                    @endif
                                </td>
                                <td class="px-7 py-4 text-right font-black text-white">
                                    ${{ number_format($corte->precio, 2) }}
                                </td>
                                <td class="px-7 py-4 text-center">
                                    @if($corte->estado === 'pendiente')
                                        <form action="{{ route('reservas.completar', $corte->id) }}" method="POST" class="inline-flex gap-1.5 justify-center items-center">
                                            @csrf
                                            <select name="metodo_pago" required
                                                class="bg-slate-800 border border-slate-700 rounded-lg text-xs px-2 py-1.5 focus:ring-1 focus:ring-amber-500 focus:outline-none text-slate-300 cursor-pointer">
                                                <option value="efectivo">Efectivo</option>
                                                <option value="transferencia">Banco</option>
                                                <option value="fiado">Fiado</option>
                                            </select>
                                            <button type="submit"
                                                class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-2.5 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1 shadow-sm">
                                                <i class="fa-solid fa-check"></i> Cobrar
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex items-center justify-center">
                                            <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold px-2.5 py-1 rounded-md flex items-center gap-1.5">
                                                <i class="fa-solid fa-circle-check text-xs"></i> Completado
                                            </span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-7 py-14 text-center text-slate-500 text-sm">
                                    <i class="fa-solid fa-inbox text-2xl mb-2 opacity-20 block"></i>
                                    No hay registros de cortes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
</style>
@endsection
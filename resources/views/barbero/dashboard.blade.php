@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-rose-400 text-lg"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-2 flex items-center gap-2">
                <i class="fa-solid fa-scissors"></i> Panel de Barbero
            </p>
            <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">¡Hola, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-slate-400 mt-2 text-sm">Tu rendimiento, clientes y comisiones en tiempo real.</p>
        </div>

        <!-- Split Badge -->
        <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-5 flex items-center gap-4 backdrop-blur-md shadow-xl">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-handshake text-white text-xl"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-bold block uppercase tracking-wider mb-1">Esquema de Split</span>
                <span class="text-lg font-black text-amber-400">60% Tú</span>
                <span class="text-slate-500 font-bold mx-2">/</span>
                <span class="text-lg font-black text-slate-300">40% Local</span>
            </div>
        </div>
    </div>

    <!-- METRIC CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Total Cortes -->
        <div class="bg-slate-900/50 backdrop-blur-md p-6 rounded-2xl border border-slate-800/60 shadow-lg hover:border-slate-600 transition-colors group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-slate-700/40 text-7xl group-hover:text-slate-600/40 transition-colors">
                <i class="fa-solid fa-scissors"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs text-slate-400 uppercase tracking-widest font-bold mb-2">Cortes Realizados</p>
                <p class="text-4xl font-black text-white">{{ $totalCortesContados }}</p>
                <p class="text-xs text-slate-500 mt-3 font-semibold uppercase tracking-wider">Total acumulado en el sistema</p>
            </div>
        </div>

        <!-- Comisiones Cobradas -->
        <div class="bg-slate-900/50 backdrop-blur-md p-6 rounded-2xl border border-emerald-900/40 shadow-lg hover:border-emerald-700/40 transition-colors group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-emerald-900/40 text-7xl group-hover:text-emerald-800/40 transition-colors">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs text-emerald-400/70 uppercase tracking-widest font-bold mb-2">Comisiones Cobradas</p>
                <p class="text-4xl font-black text-emerald-400">${{ number_format($comisionesCobradas, 2) }}</p>
                <div class="flex items-center gap-2 mt-3 text-xs font-bold text-emerald-400 bg-emerald-500/10 inline-flex px-2.5 py-1 rounded-md border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span> Disponibles / Cobradas
                </div>
            </div>
        </div>

        <!-- Comisiones Pendientes -->
        <div class="bg-gradient-to-br from-slate-900/80 to-rose-950/40 backdrop-blur-md p-6 rounded-2xl border border-rose-900/30 shadow-lg hover:border-rose-700/40 transition-colors group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-rose-900/40 text-7xl group-hover:text-rose-800/40 transition-colors">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs text-rose-400/70 uppercase tracking-widest font-bold mb-2">Comisiones Pendientes</p>
                <p class="text-4xl font-black text-rose-400">${{ number_format($comisionesPendientes, 2) }}</p>
                <div class="flex items-center gap-2 mt-3 text-xs font-bold text-rose-400 bg-rose-500/10 inline-flex px-2.5 py-1 rounded-md border border-rose-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse inline-block"></span> Fiados por liquidar
                </div>
            </div>
        </div>
    </div>

    <!-- PRÓXIMAS CITAS RESERVADAS -->
    <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden mb-10">
        <div class="px-7 py-5 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60 flex justify-between items-center">
            <h3 class="font-bold text-white text-sm uppercase tracking-widest flex items-center gap-3">
                <i class="fa-regular fa-calendar-check text-amber-400 text-base"></i>
                Mis Próximas Citas Reservadas
            </h3>
            <span class="bg-amber-500 text-slate-950 text-xs font-black px-3 py-1.5 rounded-lg shadow-lg shadow-amber-500/20 uppercase tracking-wider">
                {{ $reservas->count() }} pendientes
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                        <th class="px-7 py-3 font-bold">Fecha y Hora</th>
                        <th class="px-7 py-3 font-bold">Cliente</th>
                        <th class="px-7 py-3 font-bold">Servicio</th>
                        <th class="px-7 py-3 font-bold">Precio</th>
                        <th class="px-7 py-3 text-center font-bold">Acción / Cobro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @forelse($reservas as $reserva)
                        <tr class="hover:bg-amber-500/5 transition-colors group">
                            <td class="px-7 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 flex-shrink-0">
                                        <i class="fa-regular fa-clock text-xs"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-300">
                                        {{ $reserva->fecha_hora->format('d/m/Y') }}<br>
                                        <span class="text-amber-400">{{ $reserva->fecha_hora->format('h:i A') }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-7 py-4 font-bold text-white">
                                {{ $reserva->cliente->nombre }}
                            </td>
                            <td class="px-7 py-4">
                                <span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2.5 py-1.5 rounded-md font-medium">
                                    {{ $reserva->servicio->nombre }}
                                </span>
                            </td>
                            <td class="px-7 py-4 font-black text-white text-base">
                                ${{ number_format($reserva->precio, 2) }}
                            </td>
                            <td class="px-7 py-4 text-center">
                                <form action="{{ route('reservas.completar', $reserva->id) }}" method="POST" class="inline-flex gap-2 justify-center items-center">
                                    @csrf
                                    <select name="metodo_pago" required
                                        class="bg-slate-800 border border-slate-700 rounded-lg text-xs px-2.5 py-2 focus:ring-1 focus:ring-amber-500 focus:outline-none text-slate-300 cursor-pointer">
                                        <option value="efectivo">💵 Efectivo</option>
                                        <option value="transferencia">🏦 Banco</option>
                                        <option value="fiado">🤝 Fiado</option>
                                    </select>
                                    <button type="submit"
                                        class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5 shadow-lg shadow-emerald-500/20">
                                        <i class="fa-solid fa-check"></i> Completar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-7 py-14 text-center text-slate-500 text-sm">
                                <i class="fa-regular fa-calendar-xmark text-3xl mb-3 opacity-20 block"></i>
                                No tienes citas programadas por el momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CORTES Y CLIENTES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Historial de Cortes -->
        <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-lg overflow-hidden">
            <div class="px-7 py-5 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60">
                <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-cut text-slate-400"></i> Mis Cortes Recientes
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                            <th class="px-6 py-3 font-bold">Fecha</th>
                            <th class="px-6 py-3 font-bold">Cliente</th>
                            <th class="px-6 py-3 font-bold">Servicio</th>
                            <th class="px-6 py-3 text-right font-bold">Mi Comisión</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($cortes as $corte)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                    {{ $corte->fecha_hora->format('d/m H:i') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-white">
                                    {{ $corte->cliente->nombre }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2 py-1 rounded-md font-medium">
                                        {{ $corte->servicio->nombre }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($corte->estado === 'fiado')
                                        <span class="text-rose-400 text-sm font-bold">${{ number_format($corte->precio * 0.60, 2) }}</span>
                                        <span class="text-[10px] text-rose-500/60 block font-semibold">Fiado</span>
                                    @else
                                        <span class="text-emerald-400 font-black text-sm">${{ number_format($corte->precio * 0.60, 2) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center text-slate-500 text-sm">
                                    <i class="fa-solid fa-scissors text-2xl mb-2 opacity-20 block"></i>
                                    No has registrado cortes todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Clientes Asociados -->
        <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-lg overflow-hidden">
            <div class="px-7 py-5 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60">
                <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-users text-slate-400"></i> Mis Clientes Atendidos
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                            <th class="px-6 py-3 font-bold">Cliente</th>
                            <th class="px-6 py-3 text-center font-bold">Visitas</th>
                            <th class="px-6 py-3 text-right font-bold">Última Visita</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($clientesAsociados as $cliente)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-500 font-bold text-xs shadow-inner">
                                            {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-white">{{ $cliente->nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-black px-3 py-1 rounded-md">
                                        {{ $cliente->cortes_count }} visitas
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-slate-400 font-medium">
                                    {{ $cliente->ultima_visita ? \Carbon\Carbon::parse($cliente->ultima_visita)->format('d/m/Y') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-14 text-center text-slate-500 text-sm">
                                    <i class="fa-solid fa-user-slash text-2xl mb-2 opacity-20 block"></i>
                                    No hay clientes asociados todavía.
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

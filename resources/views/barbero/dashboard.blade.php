@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-rose-400 text-lg"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1 flex items-center gap-2">
                <i class="fa-solid fa-scissors"></i> Panel del Barbero
            </p>
            <h1 class="text-3xl font-black text-white tracking-tight">¡Hola, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-slate-400 text-xs md:text-sm">Tu agenda diaria, ventas de productos y comisiones acumuladas en tiempo real.</p>
        </div>

        <!-- Split Badge Dynamic -->
        <div class="bg-slate-900/80 border border-slate-800/60 rounded-2xl p-4 flex items-center gap-4 backdrop-blur-md shadow-xl">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-handshake text-white text-base"></i>
            </div>
            <div>
                <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Tu Porcentaje</span>
                <span class="text-base font-black text-amber-400">
                    {{ auth()->user()->porcentaje_comision !== null ? auth()->user()->porcentaje_comision : ($barberia->porcentaje_barbero ?? 60) }}% Cortes
                </span>
                <span class="text-[9px] text-slate-500 font-bold block">10% Recomendación Productos</span>
            </div>
        </div>
    </div>

    <!-- CALCULADORA DE COMISIONES EN TIEMPO REAL (NÓMINA SEMANAL EN VIVO) -->
    <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60">
            <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-2">
                <i class="fa-solid fa-wallet text-amber-400"></i> Mi Calculadora de Comisión (Esta Semana)
            </h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Cortes Realizados -->
            <div class="bg-slate-950/60 border border-slate-800 p-4 rounded-xl flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <i class="fa-solid fa-cut text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] text-slate-500 uppercase tracking-widest font-bold block">Cortes Completados</span>
                    <span class="text-xl font-black text-white">{{ $cortesSemana }}</span>
                    <span class="text-[8px] text-slate-400 block mt-0.5">Semana en curso</span>
                </div>
            </div>

            <!-- Comisiones Acumuladas -->
            <div class="bg-slate-950/60 border border-slate-800 p-4 rounded-xl flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] text-slate-500 uppercase tracking-widest font-bold block">Comisiones Ganadas</span>
                    <span class="text-xl font-black text-emerald-400">${{ number_format($comisionesCortesSemana + $comisionesProductosSemana, 2) }}</span>
                    <span class="text-[8px] text-slate-400 block mt-0.5">
                        Cortes: ${{$comisionesCortesSemana}} | Prod: ${{$comisionesProductosSemana}}
                    </span>
                </div>
            </div>

            <!-- Adelantos Descontados -->
            <div class="bg-slate-950/60 border border-slate-800 p-4 rounded-xl flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400">
                    <i class="fa-solid fa-hand-holding-dollar text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] text-slate-500 uppercase tracking-widest font-bold block">Adelantos (Vales)</span>
                    <span class="text-xl font-black text-rose-400">-${{ number_format($adelantosSemana, 2) }}</span>
                    <span class="text-[8px] text-slate-400 block mt-0.5">Descontables del sábado</span>
                </div>
            </div>

            <!-- Pago Neto Estimado -->
            <div class="bg-amber-500/10 border border-amber-500/30 p-4 rounded-xl flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400">
                    <i class="fa-solid fa-coins text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] text-amber-500/80 uppercase tracking-widest font-bold block">Neto Estimado Sábado</span>
                    <span class="text-xl font-black text-amber-400">${{ number_format(max($pagoNetoSemana, 0), 2) }}</span>
                    <span class="text-[8px] text-amber-500/60 block font-bold mt-0.5">
                        Bs. {{ number_format(max($pagoNetoSemana, 0) * $tasaBcv, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE CITAS (AGENDA DEL DÍA) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Columna 1: Mi Agenda de HOY (2/3 width) -->
        <div class="lg:col-span-2 bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden flex flex-col">
            <div class="px-5 py-4 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60 flex justify-between items-center">
                <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-calendar-day text-amber-400"></i> Mi Agenda para Hoy ({{ date('d/m/Y') }})
                </h3>
                <span class="bg-amber-500 text-slate-950 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider animate-pulse">
                    {{ $citasHoy->count() }} citas hoy
                </span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 uppercase text-[9px] tracking-widest border-b border-slate-800/60">
                            <th class="px-5 py-3 font-bold">Hora</th>
                            <th class="px-5 py-3 font-bold">Cliente</th>
                            <th class="px-5 py-3 font-bold">Servicio</th>
                            <th class="px-5 py-3 text-right font-bold">Precio</th>
                            <th class="px-5 py-3 text-center font-bold">Cobro / Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($citasHoy as $reserva)
                            <tr class="hover:bg-amber-500/5 transition-colors">
                                <td class="px-5 py-3">
                                    <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-black px-2.5 py-1 rounded-md font-mono">
                                        {{ $reserva->fecha_hora->format('h:i A') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-bold text-white">{{ $reserva->cliente->nombre }}</td>
                                <td class="px-5 py-3">
                                    <span class="bg-slate-800 border border-slate-700 text-slate-300 text-[11px] px-2 py-1 rounded">
                                        {{ $reserva->servicio->nombre }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right font-mono font-bold text-slate-300">${{ number_format($reserva->precio, 2) }}</td>
                                <td class="px-5 py-3 text-center">
                                    <form action="{{ route('reservas.completar', $reserva->id) }}" method="POST" class="inline-flex gap-2 justify-center items-center">
                                        @csrf
                                        <select name="metodo_pago" required
                                            class="bg-slate-800 border border-slate-700 rounded-lg text-[10px] px-2 py-1.5 focus:ring-1 focus:ring-amber-500 focus:outline-none text-slate-300 cursor-pointer">
                                            <option value="efectivo_usd">💵 Efectivo $</option>
                                            <option value="efectivo_bs">💵 Efectivo Bs.</option>
                                            <option value="transferencia">🏦 Banco</option>
                                            <option value="fiado">🤝 Fiado</option>
                                        </select>
                                        <button type="submit"
                                            class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-3 py-1.5 rounded-lg text-[10px] transition cursor-pointer flex items-center gap-1 shadow-lg shadow-emerald-500/20">
                                            <i class="fa-solid fa-check"></i> Cobrar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center text-slate-500 text-xs">
                                    <i class="fa-regular fa-calendar-xmark text-3xl mb-3 opacity-20 block"></i>
                                    No tienes citas programadas para hoy.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Columna 2: Citas de Próximos Días (1/3 width) -->
        <div class="lg:col-span-1 bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden flex flex-col max-h-[380px]">
            <div class="px-5 py-4 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60 flex justify-between items-center">
                <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-blue-400"></i> Próximos Días
                </h3>
            </div>

            <div class="p-4 space-y-3 flex-1 overflow-y-auto">
                @forelse($citasFuturas as $reserva)
                    <div class="bg-slate-950/40 border border-slate-850 p-3.5 rounded-xl space-y-2 relative">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-white text-xs">{{ $reserva->cliente->nombre }}</p>
                                <span class="bg-slate-800 border border-slate-700 text-slate-400 text-[9px] px-1.5 py-0.5 rounded inline-block mt-1">
                                    {{ $reserva->servicio->nombre }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black px-2 py-0.5 rounded font-mono block mb-1">
                                    {{ $reserva->fecha_hora->format('d/m') }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold block">{{ $reserva->fecha_hora->format('h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-500 text-xs">
                        <i class="fa-solid fa-calendar-days text-2xl mb-2 opacity-20 block"></i>
                        No hay citas reservadas en los próximos días.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- HISTORIAL DE CORTES REALIZADOS Y CLIENTES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

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
                        <tr class="text-slate-500 uppercase text-[9px] tracking-widest border-b border-slate-800/60">
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
                                    @php
                                        $porcentaje = $corte->barbero->porcentaje_comision !== null ? $corte->barbero->porcentaje_comision : ($barberia->porcentaje_barbero ?? 60);
                                    @endphp
                                    @if($corte->estado === 'fiado')
                                        <span class="text-rose-400 text-sm font-bold">${{ number_format($corte->precio * ($porcentaje / 100), 2) }}</span>
                                        <span class="text-[10px] text-rose-500/60 block font-semibold">Fiado</span>
                                    @else
                                        <span class="text-emerald-400 font-black text-sm">${{ number_format($corte->precio * ($porcentaje / 100), 2) }}</span>
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
                        <tr class="text-slate-500 uppercase text-[9px] tracking-widest border-b border-slate-800/60">
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

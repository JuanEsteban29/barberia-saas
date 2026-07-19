@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up space-y-5" x-data="{ activeTab: 'pendientes' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1 flex items-center gap-2">
                <i class="fa-regular fa-calendar-check"></i> Registro de Reservas
            </p>
            <h1 class="text-2xl md:text-4xl font-black text-white tracking-tight">Citas Recibidas</h1>
            <p class="text-slate-400 mt-1 text-xs md:text-sm">Monitorea y cobra las citas agendadas por tus clientes en el portal público.</p>
        </div>
        
        <!-- Botón para ir al link público de reserva -->
        <a href="{{ route('reservas.public.create', $barberia->slug ?? 'demo') }}" target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-850 text-amber-500 hover:text-amber-400 border border-amber-500/20 rounded-xl font-bold text-xs md:text-sm transition-all shadow-lg cursor-pointer">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver Enlace Público de Reservas
        </a>
    </div>

    {{-- Banner Tasa BCV del Día --}}
    <div class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-400 text-sm font-bold">
        <i class="fa-solid fa-money-bill-transfer text-base"></i>
        <span>Tasa BCV del Día: <strong class="text-amber-300">Bs. {{ number_format($tasaBcv, 2) }} / $1.00 USD</strong></span>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- STATS GRID -->
    <div class="grid grid-cols-3 gap-3 md:gap-5">
        <div class="bg-slate-900/50 backdrop-blur-md p-4 rounded-2xl border border-slate-800/60 shadow-lg cursor-pointer transition-all hover:border-amber-500/30" @click="activeTab = 'pendientes'" :class="{ 'border-amber-500/50 bg-amber-500/5': activeTab === 'pendientes' }">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-hourglass-half text-amber-400 text-xs animate-pulse"></i>
                </div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider hidden sm:inline">Pendientes</span>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider sm:hidden">Pend.</span>
            </div>
            <span class="text-lg md:text-3xl font-black text-amber-400">{{ $citasPendientes->count() }}</span>
        </div>
        
        <div class="bg-slate-900/50 backdrop-blur-md p-4 rounded-2xl border border-slate-800/60 shadow-lg cursor-pointer transition-all hover:border-emerald-500/30" @click="activeTab = 'completadas'" :class="{ 'border-emerald-500/50 bg-emerald-500/5': activeTab === 'completadas' }">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
                </div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider hidden sm:inline">Completadas</span>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider sm:hidden">Comp.</span>
            </div>
            <span class="text-lg md:text-3xl font-black text-emerald-400">{{ $citasCompletadas->count() }}</span>
        </div>

        <div class="bg-slate-900/50 backdrop-blur-md p-4 rounded-2xl border border-slate-800/60 shadow-lg cursor-pointer transition-all hover:border-rose-500/30" @click="activeTab = 'fiadas'" :class="{ 'border-rose-500/50 bg-rose-500/5': activeTab === 'fiadas' }">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-handshake-angle text-rose-400 text-xs"></i>
                </div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider hidden sm:inline">Fiadas</span>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider sm:hidden">Fiado</span>
            </div>
            <span class="text-lg md:text-3xl font-black text-rose-400">{{ $citasFiadas->count() }}</span>
        </div>
    </div>

    <!-- TABS NAVIGATION -->
    <div class="flex border-b border-slate-800/80 gap-6 text-sm font-bold">
        <button @click="activeTab = 'pendientes'" 
            class="pb-3 border-b-2 transition-all cursor-pointer"
            :class="activeTab === 'pendientes' ? 'border-amber-500 text-amber-400' : 'border-transparent text-slate-500 hover:text-slate-300'">
            Pendientes por Cobrar
        </button>
        <button @click="activeTab = 'completadas'" 
            class="pb-3 border-b-2 transition-all cursor-pointer"
            :class="activeTab === 'completadas' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-300'">
            Completadas
        </button>
        <button @click="activeTab = 'fiadas'" 
            class="pb-3 border-b-2 transition-all cursor-pointer"
            :class="activeTab === 'fiadas' ? 'border-rose-500 text-rose-400' : 'border-transparent text-slate-500 hover:text-slate-300'">
            Historial Fiados
        </button>
    </div>

    <!-- TAB PANELS -->
    <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
        
        {{-- PENDIENTES TAB --}}
        <div x-show="activeTab === 'pendientes'">
            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-slate-800/50">
                @forelse($citasPendientes as $cita)
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-white text-sm">{{ $cita->cliente->nombre ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-400">Barbero: {{ $cita->barbero->name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($cita->fecha_hora)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-black text-white text-base block">${{ number_format($cita->precio, 2) }}</span>
                                <span class="text-[9px] text-amber-500/60 font-bold">Bs. {{ number_format($cita->precio * $tasaBcv, 2) }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="bg-slate-800 border border-slate-700 text-slate-300 text-[10px] px-2 py-0.5 rounded font-medium">{{ $cita->servicio->nombre ?? 'N/A' }}</span>
                        </div>
                        
                        <!-- Formulario de Cobro -->
                        <form action="{{ route('reservas.completar', $cita->id) }}" method="POST" class="bg-slate-950/60 border border-slate-800/80 p-3 rounded-xl flex items-center justify-between gap-3">
                            @csrf
                            <div class="flex flex-col gap-1 w-1/2">
                                <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Pago</label>
                                <select name="metodo_pago" required class="bg-slate-950 border border-slate-800 rounded-lg text-xs py-1 px-1.5 text-slate-300 cursor-pointer">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Banco</option>
                                    <option value="fiado">Fiado</option>
                                </select>
                            </div>
                            <button type="submit" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-black text-xs py-2.5 px-4 rounded-lg transition-all flex-1 text-center shadow shadow-amber-500/10 cursor-pointer">
                                <i class="fa-solid fa-circle-check"></i> Registrar Cobro
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-500 text-sm">
                        <i class="fa-regular fa-calendar-times text-2xl mb-2 opacity-20 block"></i>
                        No hay citas pendientes por cobrar.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                            <th class="px-6 py-4 font-bold">Cliente</th>
                            <th class="px-6 py-4 font-bold">Barbero</th>
                            <th class="px-6 py-4 font-bold">Servicio</th>
                            <th class="px-6 py-4 font-bold">Fecha / Hora</th>
                            <th class="px-6 py-4 text-right font-bold">Importe (USD/Bs.)</th>
                            <th class="px-6 py-4 text-center font-bold">Acción de Cobro</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($citasPendientes as $cita)
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-white">{{ $cita->cliente->nombre ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $cita->cliente->telefono ?? 'Sin Tel.' }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-300 font-semibold">{{ $cita->barbero->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2.5 py-1 rounded-md font-medium">
                                        {{ $cita->servicio->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-400 font-medium">
                                    {{ \Carbon\Carbon::parse($cita->fecha_hora)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-black text-white block">${{ number_format($cita->precio, 2) }}</span>
                                    <span class="text-[10px] text-amber-500/60 font-bold">Bs. {{ number_format($cita->precio * $tasaBcv, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('reservas.completar', $cita->id) }}" method="POST" class="flex justify-center items-center gap-2 max-w-xs mx-auto">
                                        @csrf
                                        <select name="metodo_pago" required class="bg-slate-950 border border-slate-800 rounded-lg text-xs px-2 py-1.5 text-slate-300 cursor-pointer">
                                            <option value="efectivo">Efectivo</option>
                                            <option value="transferencia">Banco</option>
                                            <option value="fiado">Fiado</option>
                                        </select>
                                        <button type="submit" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-black text-xs py-1.5 px-3 rounded-lg transition-all cursor-pointer">
                                            Cobrar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 text-sm">
                                    <i class="fa-regular fa-calendar-times text-2xl mb-2 opacity-20 block"></i>
                                    No hay citas pendientes por cobrar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- COMPLETADAS TAB --}}
        <div x-show="activeTab === 'completadas'">
            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-slate-800/50">
                @forelse($citasCompletadas as $cita)
                    <div class="p-4 flex justify-between items-center gap-3">
                        <div>
                            <p class="font-bold text-white text-sm">{{ $cita->cliente->nombre ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-400">Atendido por: {{ $cita->barbero->name ?? 'N/A' }}</p>
                            <span class="bg-slate-800 border border-slate-700 text-slate-300 text-[10px] px-2 py-0.5 rounded font-medium mt-1 inline-block">{{ $cita->servicio->nombre ?? 'N/A' }}</span>
                            <p class="text-[10px] text-slate-500 mt-1">{{ \Carbon\Carbon::parse($cita->fecha_hora)->format('d/m H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-white text-base block">${{ number_format($cita->precio, 2) }}</span>
                            <span class="text-[10px] text-amber-500/60 font-bold block">Bs. {{ number_format($cita->precio * $tasaBcv, 2) }}</span>
                            <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-bold px-2 py-0.5 rounded-full mt-1 inline-block uppercase">
                                {{ $cita->metodo_pago }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-500 text-sm">
                        <i class="fa-solid fa-folder-open text-2xl mb-2 opacity-20 block"></i>
                        No hay citas completadas en el historial.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                            <th class="px-6 py-4 font-bold">Cliente</th>
                            <th class="px-6 py-4 font-bold">Barbero</th>
                            <th class="px-6 py-4 font-bold">Servicio</th>
                            <th class="px-6 py-4 font-bold">Fecha / Hora</th>
                            <th class="px-6 py-4 text-center font-bold">Método Pago</th>
                            <th class="px-6 py-4 text-right font-bold">Importe (USD/Bs.)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($citasCompletadas as $cita)
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="px-6 py-4 font-bold text-white">{{ $cita->cliente->nombre ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-300 font-semibold">{{ $cita->barbero->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2.5 py-1 rounded-md font-medium">
                                        {{ $cita->servicio->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-400 font-medium">
                                    {{ \Carbon\Carbon::parse($cita->fecha_hora)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black px-2.5 py-1 rounded-md uppercase">
                                        {{ $cita->metodo_pago }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-black text-white block">${{ number_format($cita->precio, 2) }}</span>
                                    <span class="text-[10px] text-amber-500/60 font-bold">Bs. {{ number_format($cita->precio * $tasaBcv, 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 text-sm">
                                    <i class="fa-solid fa-folder-open text-2xl mb-2 opacity-20 block"></i>
                                    No hay citas completadas en el historial.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FIADAS TAB --}}
        <div x-show="activeTab === 'fiadas'">
            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-slate-800/50">
                @forelse($citasFiadas as $cita)
                    <div class="p-4 flex justify-between items-center gap-3">
                        <div>
                            <p class="font-bold text-white text-sm">{{ $cita->cliente->nombre ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-400">Atendido por: {{ $cita->barbero->name ?? 'N/A' }}</p>
                            <span class="bg-slate-800 border border-slate-700 text-slate-300 text-[10px] px-2 py-0.5 rounded font-medium mt-1 inline-block">{{ $cita->servicio->nombre ?? 'N/A' }}</span>
                            <p class="text-[10px] text-slate-500 mt-1">{{ \Carbon\Carbon::parse($cita->fecha_hora)->format('d/m H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-rose-400 text-base block">${{ number_format($cita->precio, 2) }}</span>
                            <span class="text-[10px] text-rose-500/60 font-bold block">Bs. {{ number_format($cita->precio * $tasaBcv, 2) }}</span>
                            <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[9px] font-bold px-2 py-0.5 rounded-full mt-1 inline-block uppercase">
                                FIADO
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-500 text-sm">
                        <i class="fa-solid fa-handshake-slash text-2xl mb-2 opacity-20 block"></i>
                        No hay registros de citas fiadas en este módulo.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                            <th class="px-6 py-4 font-bold">Cliente</th>
                            <th class="px-6 py-4 font-bold">Barbero</th>
                            <th class="px-6 py-4 font-bold">Servicio</th>
                            <th class="px-6 py-4 font-bold">Fecha / Hora</th>
                            <th class="px-6 py-4 text-center font-bold">Estado</th>
                            <th class="px-6 py-4 text-right font-bold">Importe (USD/Bs.)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($citasFiadas as $cita)
                            <tr class="hover:bg-slate-800/20 transition-colors">
                                <td class="px-6 py-4 font-bold text-white">{{ $cita->cliente->nombre ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-300 font-semibold">{{ $cita->barbero->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2.5 py-1 rounded-md font-medium">
                                        {{ $cita->servicio->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-400 font-medium">
                                    {{ \Carbon\Carbon::parse($cita->fecha_hora)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[10px] font-black px-2.5 py-1 rounded-md uppercase">
                                        FIADO
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-black text-rose-400 block">${{ number_format($cita->precio, 2) }}</span>
                                    <span class="text-[10px] text-rose-500/60 font-bold">Bs. {{ number_format($cita->precio * $tasaBcv, 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500 text-sm">
                                    <i class="fa-solid fa-handshake-slash text-2xl mb-2 opacity-20 block"></i>
                                    No hay registros de citas fiadas en este módulo.
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
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.4s ease-out forwards; }
</style>
@endsection

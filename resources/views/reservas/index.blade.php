@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up space-y-5" x-data="{
    activeTab: 'pendientes',
    showCobroModal: false,
    activeCitaId: null,
    activeCliente: '',
    activeMonto: 0,
    activeServicio: '',
    openCobro(id, cliente, monto, servicio) {
        this.activeCitaId = id;
        this.activeCliente = cliente;
        this.activeMonto = Number(monto);
        this.activeServicio = servicio;
        this.showCobroModal = true;
    }
}">

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
                        
                        <!-- Botón de Cobro Modal -->
                        <button type="button" @click="openCobro({{ $cita->id }}, '{{ addslashes($cita->cliente->nombre ?? 'Cliente General') }}', {{ $cita->precio }}, '{{ addslashes($cita->servicio->nombre ?? 'Servicio') }}')"
                            class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-black text-xs py-3 px-4 rounded-xl transition-all shadow-lg shadow-amber-500/10 cursor-pointer flex items-center justify-center gap-2">
                            <i class="fa-solid fa-hand-holding-dollar text-sm"></i> Registrar Cobro
                        </button>
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
                                <td class="px-6 py-4 text-center">
                                    <button type="button" @click="openCobro({{ $cita->id }}, '{{ addslashes($cita->cliente->nombre ?? 'Cliente General') }}', {{ $cita->precio }}, '{{ addslashes($cita->servicio->nombre ?? 'Servicio') }}')"
                                        class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-black text-xs py-2 px-4 rounded-lg transition-all shadow cursor-pointer inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-hand-holding-dollar text-xs"></i> Cobrar
                                    </button>
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

    <!-- MODAL: Cobro de Cita (Alpine.js) -->
    <div x-show="showCobroModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak style="display:none;">
        <div @click="showCobroModal = false" x-show="showCobroModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

        <div x-show="showCobroModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
            class="relative bg-slate-900 border border-slate-800 w-full max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden z-10 p-7">

            <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-6">
                <h3 class="text-lg font-black text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-hand-holding-dollar text-amber-400 text-sm"></i>
                    </div>
                    Registrar Cobro
                </h3>
                <button type="button" @click="showCobroModal = false"
                    class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="'/reservas/' + activeCitaId + '/completar'" method="POST" class="space-y-5">
                @csrf
                <!-- Summary -->
                <div class="bg-slate-950/60 border border-slate-800 p-4 rounded-xl space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400 font-medium">Cliente:</span>
                        <span class="font-bold text-white" x-text="activeCliente"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400 font-medium">Servicio:</span>
                        <span class="font-bold text-white text-xs bg-slate-800 border border-slate-700 px-2.5 py-0.5 rounded" x-text="activeServicio"></span>
                    </div>
                    <div class="h-px bg-slate-800"></div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400 font-medium">Total a Pagar:</span>
                        <div class="text-right">
                            <span class="font-black text-amber-400 text-lg block" x-text="'$' + Number(activeMonto).toFixed(2) + ' USD'"></span>
                            <span class="text-[10px] text-amber-500/60 font-bold block" x-text="'Bs. ' + Number(activeMonto * {{ $tasaBcv }}).toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-3 tracking-wider">Método de Pago</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-emerald-500/50 hover:bg-emerald-500/5 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10 group">
                            <input type="radio" name="metodo_pago" value="efectivo" checked class="sr-only">
                            <i class="fa-solid fa-money-bill-wave text-emerald-400 text-lg mb-1.5"></i>
                            <span class="text-[10px] font-bold text-slate-400 group-has-[:checked]:text-emerald-400">Efectivo</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-blue-500/50 hover:bg-blue-500/5 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/10 group">
                            <input type="radio" name="metodo_pago" value="transferencia" class="sr-only">
                            <i class="fa-solid fa-building-columns text-blue-400 text-lg mb-1.5"></i>
                            <span class="text-[10px] font-bold text-slate-400 group-has-[:checked]:text-blue-400">Banco</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-rose-500/50 hover:bg-rose-500/5 transition has-[:checked]:border-rose-500 has-[:checked]:bg-rose-500/10 group">
                            <input type="radio" name="metodo_pago" value="fiado" class="sr-only">
                            <i class="fa-solid fa-handshake text-rose-400 text-lg mb-1.5"></i>
                            <span class="text-[10px] font-bold text-slate-400 group-has-[:checked]:text-rose-400">Fiado</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="showCobroModal = false"
                        class="px-5 py-2.5 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-black text-black bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 rounded-xl transition-all shadow-lg shadow-amber-500/20 cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> Registrar Pago
                    </button>
                </div>
            </form>
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

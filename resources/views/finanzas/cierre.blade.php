@extends('layouts.app')

@section('content')
<style>[x-cloak] { display: none !important; }</style>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="animate-fade-in-up space-y-8" x-data="{ showModalCierre: false }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
        <div>
            <p class="text-xs font-bold text-amber-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                <i class="fa-solid fa-lock"></i> Control Contable
            </p>
            <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">Cierre de Caja General</h1>
            <p class="text-slate-400 mt-2 text-sm">Consolida los ingresos de todos los barberos y registra el cierre contable del período.</p>
        </div>
        <button @click="showModalCierre = true"
            class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl font-black text-sm transition-all shadow-lg cursor-pointer
                {{ $citasPendientesCierre->isEmpty()
                    ? 'bg-slate-800 text-slate-500 border border-slate-700 cursor-not-allowed opacity-60'
                    : 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-amber-500/20' }}"
            {{ $citasPendientesCierre->isEmpty() ? 'disabled' : '' }}>
            <i class="fa-solid fa-lock"></i>
            {{ $citasPendientesCierre->isEmpty() ? 'Sin servicios por cerrar' : 'Cerrar Caja del Día' }}
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- 🔍 Panel de Inteligencia de Cuadre (si hay diferencias post-cierre) --}}
    @if(session('diferencias_cuadre') && count(session('diferencias_cuadre')) > 0)
    <div class="bg-gradient-to-br from-rose-950/60 to-slate-900/80 border border-rose-500/30 rounded-2xl shadow-xl overflow-hidden">
        <div class="px-7 py-5 border-b border-rose-500/20 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-magnifying-glass-dollar text-rose-400"></i>
            </div>
            <div>
                <h3 class="font-black text-white text-sm">🔍 Asistente de Cuadre de Caja</h3>
                <p class="text-rose-400/70 text-xs">Se detectaron diferencias entre el conteo físico y el sistema. Aquí están los posibles sospechosos.</p>
            </div>
        </div>
        <div class="p-6 space-y-5">
            @foreach(session('diferencias_cuadre') as $dif)
            <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <span class="font-black text-white text-sm flex items-center gap-2">
                        <i class="fa-solid fa-scale-unbalanced text-rose-400"></i>
                        {{ $dif['metodo'] }}
                    </span>
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <span class="text-slate-400">Sistema: <span class="text-white">${{ number_format($dif['esperado'], 2) }}</span></span>
                        <span class="text-slate-600">vs</span>
                        <span class="text-slate-400">Contado: <span class="text-amber-400">${{ number_format($dif['contado'], 2) }}</span></span>
                        <span class="px-2 py-1 rounded-lg font-black text-xs {{ $dif['diff'] < 0 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
                            {{ $dif['diff'] > 0 ? '+' : '' }}${{ number_format($dif['diff'], 2) }}
                        </span>
                    </div>
                </div>
                @if(count($dif['sospechosos']) > 0)
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-amber-400/60"></i>
                        Servicios de mayor valor que podrían revisar:
                    </p>
                    <div class="space-y-1.5">
                        @foreach($dif['sospechosos'] as $s)
                        <div class="flex items-center justify-between bg-slate-900/60 px-4 py-2.5 rounded-lg text-xs">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-clock text-[9px]"></i>
                                </span>
                                <div>
                                    <span class="font-bold text-white">{{ $s['cliente'] }}</span>
                                    <span class="text-slate-500 ml-2">{{ $s['barbero'] }} · {{ $s['hora'] }}</span>
                                </div>
                            </div>
                            <span class="font-black text-white">${{ number_format($s['monto'], 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Banner Tasa BCV del Día --}}
    <div class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-400 text-sm font-bold">
        <i class="fa-solid fa-money-bill-transfer text-base"></i>
        <span>Tasa BCV: <strong class="text-amber-300">Bs. {{ number_format($tasaBcv, 2) }} / $1 USD</strong></span>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 md:gap-4">

        <div class="bg-slate-900/50 backdrop-blur-md p-4 rounded-2xl border border-slate-800/60 shadow-lg hover:border-emerald-500/30 transition-colors">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-emerald-400 text-xs"></i>
                </div>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Efectivo $</p>
            </div>
            <p class="text-xl font-black text-emerald-400">${{ number_format($totalEfectivoUsd + $totalEfectivoLegacy, 2) }}</p>
            <p class="text-[10px] text-emerald-500/60 font-bold mt-0.5">USD</p>
        </div>

        <div class="bg-slate-900/50 backdrop-blur-md p-4 rounded-2xl border border-slate-800/60 shadow-lg hover:border-amber-500/30 transition-colors">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-transfer text-amber-400 text-xs"></i>
                </div>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Efectivo Bs.</p>
            </div>
            <p class="text-xl font-black text-amber-400">${{ number_format($totalEfectivoBs, 2) }}</p>
            <p class="text-[10px] text-amber-500/60 font-bold mt-0.5">Bs. {{ number_format($totalEfectivoBs * $tasaBcv, 2) }}</p>
        </div>

        <div class="bg-slate-900/50 backdrop-blur-md p-4 rounded-2xl border border-slate-800/60 shadow-lg hover:border-blue-500/30 transition-colors">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-building-columns text-blue-400 text-xs"></i>
                </div>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Transferencia</p>
            </div>
            <p class="text-xl font-black text-blue-400">${{ number_format($totalTransferencia, 2) }}</p>
            <p class="text-[10px] text-blue-500/60 font-bold mt-0.5">Bs. {{ number_format($totalTransferencia * $tasaBcv, 2) }}</p>
        </div>

        <div class="bg-slate-900/50 backdrop-blur-md p-5 rounded-2xl border border-amber-900/30 shadow-lg">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-handshake text-amber-400 text-xs"></i>
                </div>
                <p class="text-xs text-amber-400/70 uppercase tracking-widest font-bold">Fiados</p>
            </div>
            <p class="text-2xl font-black text-amber-400">${{ number_format($totalFiado, 2) }}</p>
            <p class="text-[10px] text-amber-500/60 font-bold mt-0.5">Bs. {{ number_format($totalFiado * $tasaBcv, 2) }}</p>
            <p class="text-[10px] text-slate-500 mt-1 font-semibold uppercase">No entran al cierre</p>
        </div>

        <div class="bg-gradient-to-br from-rose-950/40 to-slate-900/60 backdrop-blur-md p-5 rounded-2xl border border-rose-900/30 shadow-lg">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-arrow-trend-down text-rose-400 text-xs"></i>
                </div>
                <p class="text-xs text-rose-400/70 uppercase tracking-widest font-bold">Gastos</p>
            </div>
            <p class="text-2xl font-black text-rose-400">-${{ number_format($totalGastos, 2) }}</p>
            <p class="text-[10px] text-rose-500/60 font-bold mt-0.5">-Bs. {{ number_format($totalGastos * $tasaBcv, 2) }}</p>
        </div>

        <div class="p-5 rounded-2xl border shadow-lg relative overflow-hidden
            {{ $netoReal >= 0 ? 'bg-gradient-to-br from-amber-900/30 to-slate-900/80 border-amber-800/40' : 'bg-gradient-to-br from-rose-950/60 to-slate-900/80 border-rose-800/40' }}">
            <div class="absolute -right-3 -top-3 text-5xl {{ $netoReal >= 0 ? 'text-amber-500/10' : 'text-rose-500/10' }}">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] uppercase tracking-widest font-bold mb-3 {{ $netoReal >= 0 ? 'text-amber-400/70' : 'text-rose-400/70' }}">Neto Real (Negocio)</p>
                <p class="text-2xl font-black {{ $netoReal >= 0 ? 'text-amber-400' : 'text-rose-400' }}">${{ number_format($netoReal, 2) }}</p>
                <p class="text-[10px] font-bold mt-0.5 {{ $netoReal >= 0 ? 'text-amber-500/60' : 'text-rose-500/60' }}">Bs. {{ number_format($netoReal * $tasaBcv, 2) }}</p>
                <div class="mt-2 pt-2 border-t border-slate-800/50 text-[9px] text-slate-500 space-y-0.5">
                    <p class="flex justify-between"><span>Ingresos Brutos:</span> <span class="text-slate-300 font-bold">${{ number_format($totalIngresos, 2) }}</span></p>
                    <p class="flex justify-between"><span>Comisiones:</span> <span class="text-blue-400 font-bold">-${{ number_format($totalComisiones, 2) }}</span></p>
                    <p class="flex justify-between"><span>Gastos:</span> <span class="text-rose-400 font-bold">-${{ number_format($totalGastos, 2) }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios Pendientes de Cierre -->
    <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
        <div class="px-7 py-5 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60 flex justify-between items-center flex-wrap gap-3">
            <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-3">
                <i class="fa-solid fa-hourglass-half text-amber-400"></i>
                Servicios Pendientes de Cierre
                <span class="bg-amber-500 text-slate-950 text-[10px] font-black px-2 py-0.5 rounded-md">
                    {{ $citasPendientesCierre->count() }}
                </span>
            </h3>
            <div class="flex items-center gap-2 text-sm">
                <span class="text-slate-400 font-medium">Total a cerrar:</span>
                <span class="font-black text-white text-base">${{ number_format($totalIngresos, 2) }}</span>
            </div>
        </div>
        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-slate-800/50">
            @forelse($citasPendientesCierre as $cita)
                <div class="p-4 flex justify-between items-center gap-3">
                    <div class="min-w-0">
                        <p class="font-bold text-white text-sm truncate">{{ $cita->cliente->nombre ?? 'General' }}</p>
                        <div class="flex flex-wrap items-center gap-1.5 mt-1 text-[10px] text-slate-500">
                            <span>{{ $cita->barbero->name ?? 'N/A' }}</span>
                            <span>·</span>
                            <span>{{ $cita->fecha_hora->format('d/m H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="bg-slate-800 border border-slate-700 text-slate-300 text-[9px] px-1.5 py-0.5 rounded font-medium">
                                {{ $cita->servicio->nombre ?? 'N/A' }}
                            </span>
                            @if($cita->metodo_pago === 'efectivo_usd')
                                <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-bold px-1.5 py-0.5 rounded">Efectivo $</span>
                            @elseif($cita->metodo_pago === 'efectivo_bs')
                                <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[9px] font-bold px-1.5 py-0.5 rounded">Efectivo Bs.</span>
                            @elseif($cita->metodo_pago === 'transferencia')
                                <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[9px] font-bold px-1.5 py-0.5 rounded">Banco</span>
                            @else
                                <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-bold px-1.5 py-0.5 rounded">Efectivo</span>
                            @endif
                        </div>
                    </div>
                    <span class="font-black text-white text-base flex-shrink-0">
                        ${{ number_format($cita->precio, 2) }}
                    </span>
                </div>
            @empty
                <div class="py-12 text-center text-slate-500 text-sm">
                    <i class="fa-solid fa-circle-check text-emerald-400/20 text-3xl mb-2 block"></i>
                    <p class="font-bold text-slate-400 mb-1">¡Todo cerrado!</p>
                    <p class="text-xs">No hay servicios pendientes.</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                        <th class="px-7 py-3 font-bold">Fecha / Hora</th>
                        <th class="px-7 py-3 font-bold">Cliente</th>
                        <th class="px-7 py-3 font-bold">Barbero</th>
                        <th class="px-7 py-3 font-bold">Servicio</th>
                        <th class="px-7 py-3 text-center font-bold">Método</th>
                        <th class="px-7 py-3 text-right font-bold">Precio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @forelse($citasPendientesCierre as $cita)
                        <tr class="hover:bg-amber-500/5 transition-colors">
                            <td class="px-7 py-4 text-xs text-slate-500 font-medium font-mono">
                                {{ $cita->fecha_hora->format('d/m H:i') }}
                            </td>
                            <td class="px-7 py-4 font-bold text-white">{{ $cita->cliente->nombre ?? 'General' }}</td>
                            <td class="px-7 py-4 text-slate-400">{{ $cita->barbero->name ?? 'N/A' }}</td>
                            <td class="px-7 py-4">
                                <span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2.5 py-1.5 rounded-md font-medium">
                                    {{ $cita->servicio->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-7 py-4 text-center">
                                @if($cita->metodo_pago === 'efectivo_usd')
                                    <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold px-2.5 py-1 rounded-md">Efectivo $</span>
                                @elseif($cita->metodo_pago === 'efectivo_bs')
                                    <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold px-2.5 py-1 rounded-md">Efectivo Bs.</span>
                                @elseif($cita->metodo_pago === 'transferencia')
                                    <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold px-2.5 py-1 rounded-md">Banco</span>
                                @else
                                    <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold px-2.5 py-1 rounded-md">Efectivo</span>
                                @endif
                            </td>
                            <td class="px-7 py-4 text-right font-black text-white">${{ number_format($cita->precio, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-7 py-16 text-center text-slate-500 text-sm">
                                <i class="fa-solid fa-circle-check text-emerald-400/30 text-4xl mb-3 block"></i>
                                <p class="font-bold text-slate-400 text-base mb-1">¡Todo cerrado!</p>
                                <p>No hay servicios pendientes. Todos los registros han sido cerrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Historial de Cierres Anteriores -->
    @if($historialCierres->isNotEmpty())
    <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
        <div class="px-7 py-5 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60">
            <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-3">
                <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Historial de Cierres Anteriores
            </h3>
        </div>
        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-slate-800/50">
            @foreach($historialCierres as $cierre)
                <div class="p-4 space-y-2">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center">
                                <i class="fa-solid fa-calendar-check text-slate-400 text-[10px]"></i>
                            </div>
                            <span class="font-bold text-white text-xs">{{ \Carbon\Carbon::parse($cierre->fecha)->format('d/m/Y') }}</span>
                        </div>
                        <span class="bg-slate-800 text-slate-300 font-bold text-[10px] px-2 py-0.5 rounded">
                            {{ $cierre->total_citas }} serv.
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-[10px] text-center pt-1">
                        <div class="bg-slate-800/40 rounded p-1.5">
                            <p class="text-slate-500 mb-0.5">Efectivo</p>
                            <p class="font-bold text-emerald-400">${{ number_format($cierre->total_efectivo, 2) }}</p>
                        </div>
                        <div class="bg-slate-800/40 rounded p-1.5">
                            <p class="text-slate-500 mb-0.5">Banco</p>
                            <p class="font-bold text-blue-400">${{ number_format($cierre->total_transferencia, 2) }}</p>
                        </div>
                        <div class="bg-slate-800/40 rounded p-1.5">
                            <p class="text-slate-500 mb-0.5">Fiados</p>
                            <p class="font-bold text-amber-400">${{ number_format($cierre->total_fiado, 2) }}</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center bg-slate-950/40 px-3 py-2 rounded-lg text-xs font-bold mt-1">
                        <span class="text-slate-400">Total Ingresos:</span>
                        <span class="text-white font-black">${{ number_format($cierre->total_ingresos, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                        <th class="px-7 py-3 font-bold">Fecha</th>
                        <th class="px-7 py-3 text-center font-bold">Servicios</th>
                        <th class="px-7 py-3 text-right font-bold">Efectivo</th>
                        <th class="px-7 py-3 text-right font-bold">Transferencia</th>
                        <th class="px-7 py-3 text-right font-bold">Fiado (del día)</th>
                        <th class="px-7 py-3 text-right font-bold">Total Ingresos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @foreach($historialCierres as $cierre)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-7 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center">
                                        <i class="fa-solid fa-calendar-check text-slate-400 text-xs"></i>
                                    </div>
                                    <span class="font-bold text-white">{{ \Carbon\Carbon::parse($cierre->fecha)->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td class="px-7 py-4 text-center">
                                <span class="bg-slate-800 text-slate-300 font-bold text-xs px-2.5 py-1 rounded-md">{{ $cierre->total_citas }}</span>
                            </td>
                            <td class="px-7 py-4 text-right text-emerald-400 font-semibold">${{ number_format($cierre->total_efectivo, 2) }}</td>
                            <td class="px-7 py-4 text-right text-blue-400 font-semibold">${{ number_format($cierre->total_transferencia, 2) }}</td>
                            <td class="px-7 py-4 text-right text-amber-400 font-semibold">${{ number_format($cierre->total_fiado, 2) }}</td>
                            <td class="px-7 py-4 text-right font-black text-white text-base">${{ number_format($cierre->total_ingresos, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- MODAL: Confirmar Cierre (Alpine.js) -->
    <div x-show="showModalCierre" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak style="display:none;">
        <div @click="showModalCierre = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

        <div x-show="showModalCierre"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
             class="relative bg-slate-900 border border-slate-800 w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 p-5 md:p-7">

            <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-5">
                <h3 class="text-base sm:text-lg font-black text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-lock text-amber-400 text-sm"></i>
                    </div>
                    Confirmar Cierre de Caja
                </h3>
                <button type="button" @click="showModalCierre = false"
                    class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Resumen del Cierre -->
            <div class="bg-slate-950/60 border border-slate-800 p-5 rounded-xl space-y-3 mb-6">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-400">Servicios a cerrar:</span>
                    <span class="font-black text-white">{{ $citasPendientesCierre->count() }}</span>
                </div>
                <div class="h-px bg-slate-800"></div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-400 flex items-center gap-2"><i class="fa-solid fa-money-bill-wave text-emerald-400/60 text-xs"></i> Efectivo:</span>
                    <span class="font-bold text-emerald-400">${{ number_format($totalEfectivo, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-400 flex items-center gap-2"><i class="fa-solid fa-building-columns text-blue-400/60 text-xs"></i> Transferencia:</span>
                    <span class="font-bold text-blue-400">${{ number_format($totalTransferencia, 2) }}</span>
                </div>
                <div class="h-px bg-slate-800"></div>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-white text-sm">Gran Total:</span>
                    <div class="text-right">
                        <span class="font-black text-amber-400 text-xl">${{ number_format($totalIngresos, 2) }}</span>
                        <p class="text-[10px] text-amber-500/60 font-bold">Bs. {{ number_format($totalIngresos * $tasaBcv, 2) }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('cierre.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Conteo Físico de Caja --}}
                <div class="space-y-3">
                    <p class="text-xs font-black text-slate-300 uppercase tracking-widest flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass-dollar text-amber-400"></i>
                        Conteo Físico (Opcional — para detectar descuadres)
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-emerald-400 uppercase mb-1.5 tracking-wider">Efectivo USD contado</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-xs">$</span>
                                <input type="number" name="efectivo_usd_contado" min="0" step="0.01" placeholder="0.00"
                                    class="w-full bg-slate-950/80 border border-emerald-900/40 rounded-lg py-2.5 pl-6 pr-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition placeholder-slate-700">
                            </div>
                            <p class="text-[10px] text-slate-600 mt-1">Sistema: ${{ number_format($totalEfectivoUsd + $totalEfectivoLegacy, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-amber-400 uppercase mb-1.5 tracking-wider">Efectivo Bs. contado ($)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-xs">$</span>
                                <input type="number" name="efectivo_bs_contado" min="0" step="0.01" placeholder="0.00"
                                    class="w-full bg-slate-950/80 border border-amber-900/40 rounded-lg py-2.5 pl-6 pr-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/40 transition placeholder-slate-700">
                            </div>
                            <p class="text-[10px] text-slate-600 mt-1">Sistema: ${{ number_format($totalEfectivoBs, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-blue-400 uppercase mb-1.5 tracking-wider">Banco / Trans. contado ($)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-xs">$</span>
                                <input type="number" name="transferencia_contado" min="0" step="0.01" placeholder="0.00"
                                    class="w-full bg-slate-950/80 border border-blue-900/40 rounded-lg py-2.5 pl-6 pr-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition placeholder-slate-700">
                            </div>
                            <p class="text-[10px] text-slate-600 mt-1">Sistema: ${{ number_format($totalTransferencia, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Notas del Cierre (Opcional)</label>
                    <textarea name="notas" rows="2" placeholder="Ej: Día tranquilo, feriado, novedad de caja..."
                        class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition placeholder-slate-600 resize-none"></textarea>
                </div>

                <div class="p-3.5 bg-amber-500/10 border border-amber-500/20 rounded-xl text-xs text-amber-300 flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400 mt-0.5 flex-shrink-0"></i>
                    <span>Esta acción es <strong>irreversible</strong>. Los <strong>{{ $citasPendientesCierre->count() }} servicios</strong> quedarán marcados como <strong>cerrados</strong>.</span>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="showModalCierre = false"
                        class="px-5 py-2.5 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-black text-slate-950 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 rounded-xl transition-all shadow-lg shadow-amber-500/20 cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-lock"></i> Confirmar Cierre
                    </button>
                    <a href="{{ route('reporte.descargar') }}" class="btn btn-primary bg-blue-600 text-white p-2 rounded">
    Descargar PDF
</a>
                </div>
            </form>
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

@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up">
    <!-- Header & Date Filters -->
    <div class="mb-6 flex flex-col gap-4">
        <div>
            <h1 class="text-2xl md:text-4xl font-black text-white tracking-tight mb-1">Visión General</h1>
            <p class="text-slate-400 font-medium text-xs md:text-base flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-amber-500"></i>
                Métricas para: <span class="text-amber-400 font-bold uppercase tracking-wider">{{ $labelRango }}</span>
            </p>
        </div>
        
        <!-- Toggle Filters -->
        <div class="bg-slate-900/80 backdrop-blur-md p-1.5 rounded-xl border border-slate-800 inline-flex shadow-xl shadow-black/20 self-start">
            <a href="{{ route('dashboard', ['rango' => 'hoy']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-300 {{ $rango === 'hoy' ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                HOY
            </a>
            <a href="{{ route('dashboard', ['rango' => 'semana']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-300 {{ $rango === 'semana' ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                SEMANA
            </a>
            <a href="{{ route('dashboard', ['rango' => 'mes']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-300 {{ $rango === 'mes' ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                MES
            </a>
        </div>
    </div>

    <!-- METRICS GRID - 2 cols on mobile, 4 on desktop -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6">
        <!-- Revenue Card -->
        <div class="bg-slate-900/50 backdrop-blur-md p-4 md:p-6 rounded-2xl border border-slate-800/60 shadow-lg hover:border-emerald-500/30 transition-colors group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-emerald-500/10 text-6xl group-hover:text-emerald-500/20 transition-colors"><i class="fa-solid fa-dollar-sign"></i></div>
            <div class="relative z-10">
                <p class="text-[9px] md:text-xs text-slate-400 uppercase tracking-widest font-bold mb-2">Ingresos</p>
                <p class="text-xl md:text-3xl font-black text-white mb-1"><span class="text-emerald-400">$</span>{{ number_format($ingresosPeriodo, 2) }}</p>
                <div class="hidden md:flex items-center gap-2 mt-4 text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-md border border-emerald-500/20 w-fit">
                    <i class="fa-solid fa-arrow-trend-up"></i> Pagos completados
                </div>
                <p class="md:hidden text-[9px] text-emerald-400 font-bold mt-1">Completados</p>
            </div>
        </div>

        <!-- Services Card -->
        <div class="bg-slate-900/50 backdrop-blur-md p-4 md:p-6 rounded-2xl border border-slate-800/60 shadow-lg hover:border-amber-500/30 transition-colors group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-amber-500/10 text-6xl group-hover:text-amber-500/20 transition-colors"><i class="fa-solid fa-scissors"></i></div>
            <div class="relative z-10">
                <p class="text-[9px] md:text-xs text-slate-400 uppercase tracking-widest font-bold mb-2">Cortes</p>
                <p class="text-xl md:text-3xl font-black text-white mb-1">{{ $totalCortes }} <span class="text-sm md:text-lg text-slate-500 font-medium">serv.</span></p>
                <p class="text-[9px] md:hidden text-amber-400 font-bold mt-1">{{ strtolower($labelRango) }}</p>
            </div>
        </div>

        <!-- Staff Card -->
        <div class="bg-slate-900/50 backdrop-blur-md p-4 md:p-6 rounded-2xl border border-slate-800/60 shadow-lg hover:border-blue-500/30 transition-colors group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-blue-500/10 text-6xl group-hover:text-blue-500/20 transition-colors"><i class="fa-solid fa-users"></i></div>
            <div class="relative z-10">
                <p class="text-[9px] md:text-xs text-slate-400 uppercase tracking-widest font-bold mb-2">Personal</p>
                <p class="text-xl md:text-3xl font-black text-white mb-1">{{ $barberosActivos }} <span class="text-sm md:text-lg text-slate-500 font-medium">barb.</span></p>
                <a href="{{ route('barberos.index') }}" class="text-[9px] md:text-xs text-blue-400 hover:text-blue-300 font-bold mt-1 md:mt-4 inline-flex items-center gap-1">
                    Gestionar <i class="fa-solid fa-arrow-right text-[8px]"></i>
                </a>
            </div>
        </div>

        <!-- Debt Card -->
        <div class="bg-gradient-to-br from-slate-900/80 to-rose-950/40 backdrop-blur-md p-4 md:p-6 rounded-2xl border border-rose-900/30 shadow-lg hover:border-rose-500/40 transition-colors group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-rose-500/10 text-6xl group-hover:text-rose-500/20 transition-colors"><i class="fa-solid fa-handshake-angle"></i></div>
            <div class="relative z-10">
                <p class="text-[9px] md:text-xs text-rose-400/80 uppercase tracking-widest font-bold mb-2">Fiados</p>
                <p class="text-xl md:text-3xl font-black text-rose-400 mb-1">${{ number_format($fiadosPendientesMonto, 2) }}</p>
                <a href="{{ route('fiados.index') }}" class="text-[9px] md:text-xs text-rose-300 hover:text-rose-200 font-bold mt-1 inline-flex items-center gap-1">
                    {{ $fiadosPendientesCount }} pendientes <i class="fa-solid fa-arrow-right text-[8px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT: Financial + Team -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6">
        
        <!-- Financial Summary -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-b from-slate-800/80 to-slate-900/90 backdrop-blur-xl rounded-2xl p-5 md:p-7 border border-slate-700/50 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl"></div>
                
                <h3 class="text-xs text-amber-400 font-bold uppercase tracking-widest mb-5 flex items-center gap-3">
                    <i class="fa-solid fa-vault"></i> Resumen de Caja
                </h3>
                
                <div class="space-y-4 relative z-10">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-400">Ingreso Bruto:</span>
                        <span class="text-base md:text-lg font-bold text-white">${{ number_format($ingresosPeriodo, 2) }}</span>
                    </div>
                    <div class="h-px w-full bg-gradient-to-r from-transparent via-slate-700 to-transparent opacity-50"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-400 flex items-center gap-2"><i class="fa-solid fa-arrow-turn-down text-amber-500/50"></i> Comisiones:</span>
                        <span class="text-base md:text-lg font-bold text-amber-400">${{ number_format($totalComisionesPagadas, 2) }}</span>
                    </div>
                    <div class="h-px w-full bg-gradient-to-r from-transparent via-slate-700 to-transparent opacity-50"></div>
                    <div class="flex justify-between items-center bg-emerald-500/10 -mx-4 px-4 py-3 rounded-lg border border-emerald-500/20">
                        <span class="text-sm font-bold text-emerald-400 uppercase tracking-wider">Neto Local:</span>
                        <span class="text-xl md:text-2xl font-black text-emerald-400">${{ number_format($totalGananciaNegocio, 2) }}</span>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-slate-700/50 flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 uppercase tracking-widest">Cálculo auto</span>
                    <a href="{{ route('finanzas.index') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-white px-3 py-1.5 rounded-md font-bold transition-colors">Ver Detalles</a>
                </div>
            </div>
        </div>

        <!-- Barber Performance - Card list on mobile, table on desktop -->
        <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-lg p-5 md:p-7 lg:col-span-2">
            <h3 class="text-xs text-white font-bold uppercase tracking-widest mb-5 flex items-center gap-3">
                <i class="fa-solid fa-ranking-star text-amber-400"></i> Desempeño del Equipo
            </h3>
            
            <!-- Mobile Cards -->
            <div class="md:hidden space-y-3">
                @forelse($barberosDesempeno as $barbero)
                    <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/40">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-500 font-bold text-xs">
                                    {{ strtoupper(substr($barbero->name, 0, 1)) }}
                                </div>
                                <span class="font-bold text-white text-sm">{{ $barbero->name }}</span>
                            </div>
                            <span class="bg-slate-800 px-2 py-1 rounded text-xs font-bold text-slate-300">{{ $barbero->cortes_atendidos_count }} cortes</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="bg-slate-900/60 rounded-lg p-2">
                                <p class="text-slate-500 uppercase tracking-wider text-[9px] mb-1">Comisión</p>
                                <p class="font-bold text-amber-400">${{ number_format($barbero->total_comisiones, 2) }}</p>
                            </div>
                            <div class="bg-slate-900/60 rounded-lg p-2">
                                <p class="text-slate-500 uppercase tracking-wider text-[9px] mb-1">Producido</p>
                                <p class="font-bold text-white">${{ number_format($barbero->role === 'admin' ? $barbero->total_comisiones : ($barbero->total_comisiones > 0 ? $barbero->total_comisiones / 0.60 : 0), 2) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-slate-500 text-sm">
                        <i class="fa-solid fa-ghost text-2xl mb-2 opacity-20 block"></i>
                        No hay barberos en este periodo.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/80">
                            <th class="pb-3 pl-2 font-bold">Barbero</th>
                            <th class="pb-3 text-center font-bold">Cortes</th>
                            <th class="pb-3 text-right font-bold">Comisiones</th>
                            <th class="pb-3 text-right font-bold pr-2">Producido</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($barberosDesempeno as $barbero)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="py-4 pl-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-500 font-bold text-xs shadow-inner">{{ strtoupper(substr($barbero->name, 0, 1)) }}</div>
                                        <span class="font-bold text-white">{{ $barbero->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 text-center font-bold text-slate-300"><span class="bg-slate-800 px-2 py-1 rounded text-xs">{{ $barbero->cortes_atendidos_count }}</span></td>
                                <td class="py-4 text-right text-amber-400 font-bold">${{ number_format($barbero->total_comisiones, 2) }}</td>
                                <td class="py-4 text-right text-white font-bold pr-2">${{ number_format($barbero->role === 'admin' ? $barbero->total_comisiones : ($barbero->total_comisiones > 0 ? $barbero->total_comisiones / 0.60 : 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-12 text-center text-slate-500 text-sm"><i class="fa-solid fa-ghost text-2xl mb-2 opacity-20 block"></i>No hay registros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- RECENT ACTIVITY -->
    <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-lg p-5 md:p-7">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-xs text-white font-bold uppercase tracking-widest flex items-center gap-3">
                <i class="fa-solid fa-clock-rotate-left text-emerald-400"></i> Actividad Reciente
            </h3>
            <a href="{{ route('cortes.index') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-white px-3 py-1.5 rounded-md font-bold transition-colors">Ver Todo</a>
        </div>
        
        <!-- Mobile Cards -->
        <div class="md:hidden space-y-3">
            @forelse($cortesRecientes as $corte)
                <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700/40">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-bold text-white text-sm">{{ $corte->cliente->nombre ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-400">{{ $corte->barbero->name ?? 'No asignado' }}</p>
                        </div>
                        <span class="font-black text-white text-base">${{ number_format($corte->precio, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="bg-slate-800 border border-slate-700 text-slate-300 text-[10px] px-2 py-0.5 rounded font-medium">
                            {{ $corte->servicio->nombre ?? 'N/A' }}
                        </span>
                        @if($corte->estado === 'fiado')
                            <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[10px] font-bold px-2 py-0.5 rounded">Fiado</span>
                        @elseif($corte->metodo_pago === 'transferencia')
                            <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded">Banco</span>
                        @else
                            <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded">Efectivo</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-500 text-sm">
                    <i class="fa-solid fa-inbox text-2xl mb-2 opacity-20 block"></i>No hay cortes recientes.
                </div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/80">
                        <th class="pb-3 pl-2 font-bold">Cliente</th>
                        <th class="pb-3 font-bold">Barbero</th>
                        <th class="pb-3 font-bold">Servicio</th>
                        <th class="pb-3 text-center font-bold">Estado/Pago</th>
                        <th class="pb-3 text-right font-bold pr-2">Precio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @forelse($cortesRecientes as $corte)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 pl-2 font-bold text-white">{{ $corte->cliente->nombre ?? 'N/A' }}</td>
                        <td class="py-4 text-slate-400">{{ $corte->barbero->name ?? 'No asignado' }}</td>
                        <td class="py-4"><span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2.5 py-1.5 rounded-md font-medium">{{ $corte->servicio->nombre ?? 'N/A' }}</span></td>
                        <td class="py-4 text-center">
                            @if($corte->estado === 'fiado')
                                <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold px-2.5 py-1 rounded-md">Fiado</span>
                            @elseif($corte->metodo_pago === 'transferencia')
                                <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold px-2.5 py-1 rounded-md">Banco</span>
                            @else
                                <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold px-2.5 py-1 rounded-md">Efectivo</span>
                            @endif
                        </td>
                        <td class="py-4 text-right font-black text-white pr-2">${{ number_format($corte->precio, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-12 text-center text-slate-500 text-sm"><i class="fa-solid fa-inbox text-2xl mb-2 opacity-20 block"></i>No hay cortes recientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
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
@extends('layouts.app')

@section('content')
<style>[x-cloak] { display: none !important; }</style>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="animate-fade-in-up space-y-8" x-data="{ showModalGasto: false, categoria: '' }">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
        <div>
            <p class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                <i class="fa-solid fa-wallet"></i> Control Financiero
            </p>
            <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">Gastos y Finanzas</h1>
            <p class="text-slate-400 mt-2 text-sm">Control de ingresos brutos, egresos operativos y cálculo neto de ganancias.</p>
        </div>
        <button @click="showModalGasto = true"
            class="inline-flex items-center gap-2.5 px-5 py-3 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-black text-sm transition-all shadow-lg shadow-rose-500/20 cursor-pointer">
            <i class="fa-solid fa-minus"></i> Registrar Gasto / Egreso
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Banner Tasa BCV del Día --}}
    <div class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-400 text-sm font-bold">
        <i class="fa-solid fa-money-bill-transfer text-base"></i>
        <span>Tasa BCV: <strong class="text-amber-300">Bs. {{ number_format($tasaBcv, 2) }} / $1 USD</strong></span>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <!-- Ingresos Brutos -->
        <div class="bg-slate-900/50 backdrop-blur-md p-6 rounded-2xl border border-slate-800/60 shadow-lg hover:border-emerald-500/30 transition-colors group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-emerald-500/10 text-7xl group-hover:text-emerald-500/20 transition-colors">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs text-slate-400 uppercase tracking-widest font-bold mb-2">Ingresos Brutos</p>
                <p class="text-3xl font-black text-white">${{ number_format($totalBruto, 2) }}</p>
                <p class="text-xs text-emerald-400/60 font-bold mt-1">Bs. {{ number_format($totalBruto * $tasaBcv, 2) }}</p>
                <div class="mt-3 pt-3 border-t border-slate-800/80 flex justify-between text-xs font-semibold text-slate-500">
                    <span>Efe: <span class="text-emerald-400">${{ number_format($dineroEfectivo, 2) }}</span></span>
                    <span>Trans: <span class="text-blue-400">${{ number_format($dineroTransferencia, 2) }}</span></span>
                </div>
            </div>
        </div>

        <!-- Gastos / Egresos -->
        <div class="bg-gradient-to-br from-rose-950/40 to-slate-900/60 backdrop-blur-md p-6 rounded-2xl border border-rose-900/30 shadow-lg hover:border-rose-500/30 transition-colors group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-rose-500/10 text-7xl group-hover:text-rose-500/20 transition-colors">
                <i class="fa-solid fa-arrow-trend-down"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs text-rose-400/70 uppercase tracking-widest font-bold mb-2">Gastos / Egresos</p>
                <p class="text-3xl font-black text-rose-400">-${{ number_format($totalGastos, 2) }}</p>
                <p class="text-xs text-rose-400/60 font-bold mt-1">-Bs. {{ number_format($totalGastos * $tasaBcv, 2) }}</p>
                <p class="text-xs text-slate-500 mt-3 font-semibold uppercase tracking-wider">Costos operativos registrados</p>
            </div>
        </div>

        <!-- Retenido Personal -->
        <div class="bg-slate-900/50 backdrop-blur-md p-6 rounded-2xl border border-blue-900/30 shadow-lg hover:border-blue-500/30 transition-colors group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-blue-500/10 text-7xl group-hover:text-blue-500/20 transition-colors">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs text-blue-400/70 uppercase tracking-widest font-bold mb-2">Retenido Personal</p>
                <p class="text-3xl font-black text-blue-400">-${{ number_format($totalComisiones, 2) }}</p>
                <p class="text-xs text-blue-400/60 font-bold mt-1">-Bs. {{ number_format($totalComisiones * $tasaBcv, 2) }}</p>
                <p class="text-xs text-slate-500 mt-3 font-semibold uppercase tracking-wider">Comisiones por pagar</p>
            </div>
        </div>

        <!-- Ganancia Neta -->
        <div class="p-6 rounded-2xl border shadow-lg relative overflow-hidden
            {{ $gananciaNeta >= 0 ? 'bg-gradient-to-br from-amber-900/30 to-slate-900/80 border-amber-800/40' : 'bg-gradient-to-br from-rose-950/60 to-slate-900/80 border-rose-800/40' }}">
            <div class="absolute -right-4 -top-4 text-7xl {{ $gananciaNeta >= 0 ? 'text-amber-500/10' : 'text-rose-500/10' }}">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs uppercase tracking-widest font-bold mb-2 {{ $gananciaNeta >= 0 ? 'text-amber-400/70' : 'text-rose-400/70' }}">Ganancia Neta Real</p>
                <p class="text-3xl font-black {{ $gananciaNeta >= 0 ? 'text-amber-400' : 'text-rose-400' }}">${{ number_format($gananciaNeta, 2) }}</p>
                <p class="text-xs font-bold mt-1 {{ $gananciaNeta >= 0 ? 'text-amber-400/60' : 'text-rose-400/60' }}">Bs. {{ number_format($gananciaNeta * $tasaBcv, 2) }}</p>
                <p class="text-xs text-slate-500 mt-3 font-semibold uppercase tracking-wider">Margen limpio del negocio</p>
            </div>
        </div>
    </div>

    <!-- Historial de Gastos -->
    <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
        <div class="px-7 py-5 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60 flex justify-between items-center">
            <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-3">
                <i class="fa-solid fa-receipt text-rose-400"></i> Gastos Operativos Registrados
            </h3>
            <a href="{{ route('cierre.index') }}"
                class="inline-flex items-center gap-2 text-xs font-bold text-amber-400 hover:text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 px-3 py-1.5 rounded-lg transition-colors">
                <i class="fa-solid fa-lock text-xs"></i> Ir al Cierre de Caja
            </a>
        </div>
        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-slate-800/50">
            @forelse($gastosSemanales as $gasto)
                <div class="p-4 flex justify-between items-center gap-3">
                    <div class="min-w-0">
                        <p class="font-bold text-white text-sm truncate">{{ $gasto->descripcion }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] text-slate-500 font-medium">
                                {{ \Carbon\Carbon::parse($gasto->fecha_gasto)->format('d/m H:i') }}
                            </span>
                            <span class="bg-slate-800 border border-slate-700 text-slate-300 text-[9px] px-1.5 py-0.5 rounded font-medium">
                                {{ $gasto->categoria }}
                            </span>
                        </div>
                    </div>
                    <span class="font-black text-rose-400 text-base flex-shrink-0">
                        -${{ number_format($gasto->monto, 2) }}
                    </span>
                </div>
            @empty
                <div class="py-12 text-center text-slate-500 text-sm">
                    <i class="fa-solid fa-circle-check text-emerald-400/20 text-3xl mb-2 block"></i>
                    <p class="font-bold text-slate-400 mb-1">Sin gastos registrados</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                        <th class="px-7 py-3 font-bold">Fecha</th>
                        <th class="px-7 py-3 font-bold">Descripción / Concepto</th>
                        <th class="px-7 py-3 font-bold">Categoría</th>
                        <th class="px-7 py-3 text-right font-bold">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @forelse($gastosSemanales as $gasto)
                        <tr class="hover:bg-rose-500/5 transition-colors">
                            <td class="px-7 py-4 text-xs text-slate-500 font-medium">
                                {{ \Carbon\Carbon::parse($gasto->fecha_gasto)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-7 py-4 font-semibold text-white">
                                {{ $gasto->descripcion }}
                            </td>
                            <td class="px-7 py-4">
                                <span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2.5 py-1.5 rounded-md font-medium">
                                    {{ $gasto->categoria }}
                                </span>
                            </td>
                            <td class="px-7 py-4 text-right font-black text-rose-400">
                                -${{ number_format($gasto->monto, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-7 py-16 text-center text-slate-500 text-sm">
                                <i class="fa-solid fa-circle-check text-emerald-400/30 text-4xl mb-3 block"></i>
                                <p class="font-bold text-slate-400 text-base mb-1">Sin gastos registrados</p>
                                <p>No se han registrado egresos o gastos aún.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: Registrar Gasto (Alpine.js) -->
    <div x-show="showModalGasto" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4" x-cloak style="display:none;">
        <div @click="showModalGasto = false"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

        <div x-show="showModalGasto"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
             class="relative bg-slate-900 border border-slate-800 w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 p-5 md:p-7">

            <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-5">
                <h3 class="text-base sm:text-lg font-black text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/20 border border-rose-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-minus text-rose-400 text-sm"></i>
                    </div>
                    Registrar Gasto / Egreso
                </h3>
                <button type="button" @click="showModalGasto = false"
                    class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('gastos.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Descripción del Gasto</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-regular fa-file-lines text-sm"></i>
                        </span>
                        <input type="text" name="descripcion" required placeholder="Ej: Compra de gel, renta del local..."
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-rose-500/50 transition placeholder-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Categoría</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-solid fa-tag text-sm"></i>
                        </span>
                        <select name="categoria" x-model="categoria" required
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-10 text-sm text-white focus:outline-none focus:ring-2 focus:ring-rose-500/50 transition appearance-none cursor-pointer">
                            <option value="" class="bg-slate-900">-- Selecciona una categoría --</option>
                            <option value="Insumos" class="bg-slate-900">Insumos (gel, tinte, etc.)</option>
                            <option value="Servicios" class="bg-slate-900">Servicios (luz, agua, internet)</option>
                            <option value="Local" class="bg-slate-900">Local (renta, limpieza)</option>
                            <option value="Personal" class="bg-slate-900">Personal (bono, adelanto extra)</option>
                            <option value="Otro" class="bg-slate-900">Otro</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Selector de Barbero (Solo para Adelanto/Personal) -->
                <div x-show="categoria === 'Personal'" x-transition class="space-y-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider font-semibold">Barbero que recibe el Adelanto</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-solid fa-user-tie text-sm"></i>
                        </span>
                        <select name="barbero_id" :required="categoria === 'Personal'"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-10 text-sm text-white focus:outline-none focus:ring-2 focus:ring-rose-500/50 transition appearance-none cursor-pointer">
                            <option value="" class="bg-slate-900">-- Selecciona al barbero --</option>
                            @foreach($barberos as $barbero)
                                <option value="{{ $barbero->id }}" class="bg-slate-900">{{ $barbero->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Monto (USD)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-solid fa-dollar-sign text-sm"></i>
                        </span>
                        <input type="number" name="monto" min="0.01" step="0.01" required placeholder="0.00"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-rose-500/50 transition placeholder-slate-600">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="showModalGasto = false"
                        class="px-5 py-2.5 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-black text-white bg-rose-600 hover:bg-rose-500 rounded-xl transition-all shadow-lg shadow-rose-500/20 cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-minus"></i> Registrar Gasto
                    </button>
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
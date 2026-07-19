@extends('layouts.app')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
</style>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="animate-fade-in-up space-y-8" x-data="{
    showModal: false,
    activeFiadoId: null,
    activeCliente: '',
    activeMonto: 0,
    openCobro(id, cliente, monto) {
        this.activeFiadoId = id;
        this.activeCliente = cliente;
        this.activeMonto = monto;
        this.showModal = true;
    }
}">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
        <div>
            <p class="text-xs font-bold text-rose-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                <i class="fa-solid fa-handshake"></i> Gestión Financiera
            </p>
            <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">Cuentas por Cobrar</h1>
            <p class="text-slate-400 mt-2 text-sm">Administra y liquida los saldos pendientes de clientes de la barbería.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-rose-950/40 to-slate-900/60 backdrop-blur-md p-7 rounded-2xl border border-rose-900/30 shadow-xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-rose-900/30 text-7xl"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div class="relative z-10">
                <p class="text-xs text-rose-400/70 uppercase tracking-widest font-bold mb-2">Total Pendiente de Cobro</p>
                <p class="text-4xl font-black text-rose-400">${{ number_format($fiadosPendientes->sum('precio'), 2) }}</p>
                <p class="text-xs text-slate-500 mt-2 font-semibold uppercase tracking-wider">USD en deuda activa</p>
            </div>
        </div>
        <div class="bg-slate-900/50 backdrop-blur-md p-7 rounded-2xl border border-slate-800/60 shadow-xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-amber-900/20 text-7xl"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div class="relative z-10">
                <p class="text-xs text-amber-400/70 uppercase tracking-widest font-bold mb-2">Fiados Activos</p>
                <p class="text-4xl font-black text-amber-400">{{ $fiadosPendientes->count() }}</p>
                <p class="text-xs text-slate-500 mt-2 font-semibold uppercase tracking-wider">Cuentas sin liquidar</p>
            </div>
        </div>
    </div>

    <!-- Fiados Table -->
    <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
        <div class="px-7 py-5 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60">
            <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-3">
                <i class="fa-solid fa-clock-rotate-left text-rose-400"></i> Historial de Cuentas Pendientes
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                        <th class="px-7 py-3 font-bold">Cliente</th>
                        <th class="px-7 py-3 font-bold">Barbero</th>
                        <th class="px-7 py-3 font-bold">Servicio</th>
                        <th class="px-7 py-3 text-center font-bold">Estado</th>
                        <th class="px-7 py-3 text-right font-bold">Total a Pagar</th>
                        <th class="px-7 py-3 text-center font-bold">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @forelse($fiadosPendientes as $fiado)
                        <tr class="hover:bg-rose-500/5 transition-colors group">
                            <td class="px-7 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-rose-400 font-bold text-xs shadow-inner">
                                        {{ strtoupper(substr($fiado->cliente->nombre ?? 'C', 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-white">{{ $fiado->cliente->nombre ?? 'Cliente General' }}</span>
                                </div>
                            </td>
                            <td class="px-7 py-4 text-slate-400">{{ $fiado->barbero->name ?? 'N/A' }}</td>
                            <td class="px-7 py-4">
                                <span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2.5 py-1.5 rounded-md font-medium">
                                    {{ $fiado->servicio->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-7 py-4 text-center">
                                <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold px-2.5 py-1 rounded-md inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse inline-block"></span>
                                    Pendiente
                                </span>
                            </td>
                            <td class="px-7 py-4 text-right font-black text-white text-base">
                                ${{ number_format($fiado->precio, 2) }}
                            </td>
                            <td class="px-7 py-4 text-center">
                                <button type="button"
                                    @click="openCobro({{ $fiado->id }}, '{{ addslashes($fiado->cliente->nombre ?? 'Cliente General') }}', {{ $fiado->precio }})"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs shadow-lg shadow-emerald-500/10 transition-colors cursor-pointer">
                                    <i class="fa-solid fa-hand-holding-dollar"></i> Cobrar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-7 py-16 text-center text-slate-500 text-sm">
                                <i class="fa-solid fa-circle-check text-emerald-400/30 text-4xl mb-3 block"></i>
                                <p class="font-bold text-slate-400 text-base mb-1">¡Sin cuentas pendientes!</p>
                                <p>Todos los servicios han sido liquidados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: Cobro de Fiado (Alpine.js) -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak style="display:none;">
        <div @click="showModal = false" x-show="showModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

        <div x-show="showModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden z-10 p-7">

            <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-6">
                <h3 class="text-lg font-black text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-hand-holding-dollar text-emerald-400 text-sm"></i>
                    </div>
                    Liquidar Fiado
                </h3>
                <button type="button" @click="showModal = false"
                    class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="'/fiados/pagar/' + activeFiadoId" method="POST" class="space-y-5">
                @csrf
                <!-- Summary -->
                <div class="bg-slate-950/60 border border-slate-800 p-4 rounded-xl space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400 font-medium">Cliente:</span>
                        <span class="font-bold text-white" x-text="activeCliente"></span>
                    </div>
                    <div class="h-px bg-slate-800"></div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400 font-medium">Monto Pendiente:</span>
                        <span class="font-black text-emerald-400 text-lg" x-text="'$' + Number(activeMonto).toFixed(2) + ' USD'"></span>
                    </div>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-3 tracking-wider">Método de Pago</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-emerald-500/50 hover:bg-emerald-500/5 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10 group">
                            <input type="radio" name="metodo_pago_real" value="efectivo_usd" checked class="sr-only">
                            <i class="fa-solid fa-money-bill-wave text-emerald-400 text-lg mb-1.5"></i>
                            <span class="text-[10px] font-bold text-slate-400 group-has-[:checked]:text-emerald-400">Efectivo $</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-amber-500/50 hover:bg-amber-500/5 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/10 group">
                            <input type="radio" name="metodo_pago_real" value="efectivo_bs" class="sr-only">
                            <i class="fa-solid fa-money-bill-transfer text-amber-400 text-lg mb-1.5"></i>
                            <span class="text-[10px] font-bold text-slate-400 group-has-[:checked]:text-amber-400">Efectivo Bs.</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-blue-500/50 hover:bg-blue-500/5 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/10 group">
                            <input type="radio" name="metodo_pago_real" value="transferencia" class="sr-only">
                            <i class="fa-solid fa-building-columns text-blue-400 text-lg mb-1.5"></i>
                            <span class="text-[10px] font-bold text-slate-400 group-has-[:checked]:text-blue-400">Banco</span>
                        </label>
                    </div>
                </div>

                <p class="text-xs text-slate-500 italic bg-slate-950/40 border border-slate-800 p-3 rounded-lg">
                    <i class="fa-solid fa-circle-info text-amber-500/70 mr-1.5"></i>
                    Esta acción registrará el ingreso en la caja de hoy y cerrará la cuenta pendiente.
                </p>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="showModal = false"
                        class="px-5 py-2.5 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-black text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-all shadow-lg shadow-emerald-500/20 cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> Confirmar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
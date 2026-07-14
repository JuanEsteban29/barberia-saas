@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up space-y-5">

    <!-- Header -->
    <div>
        <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1 flex items-center gap-2">
            <i class="fa-solid fa-scissors"></i> Gestión de Servicios
        </p>
        <h1 class="text-2xl md:text-4xl font-black text-white tracking-tight">Registrar Corte</h1>
        <p class="text-slate-400 mt-1 text-xs md:text-sm">El servicio se integra automáticamente en cajas y comisiones.</p>
    </div>

    {{-- Banner Tasa BCV del Día --}}
    <div class="flex items-center gap-3 px-4 py-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-400 text-sm font-bold">
        <i class="fa-solid fa-money-bill-transfer text-base"></i>
        <span>Tasa BCV del Día: <strong class="text-amber-300">Bs. {{ number_format($tasaBcv, 2) }} / $1.00 USD</strong></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- FORMULARIO DE REGISTRO -->
        <div class="bg-slate-900/50 backdrop-blur-md p-5 md:p-7 rounded-2xl border border-slate-800/60 shadow-xl h-fit">
            <h2 class="text-white font-black text-base mb-1 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-plus text-amber-400 text-xs"></i>
                </div>
                Nuevo Servicio
            </h2>

            @if(session('success'))
                <div class="my-4 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('cortes.store') }}" method="POST" class="space-y-4 mt-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Nombre del Cliente</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-regular fa-user text-sm"></i></span>
                        <input type="text" name="cliente_nombre" placeholder="Cliente General"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition placeholder-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Barbero Asignado <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-solid fa-user-tie text-sm"></i></span>
                        <select name="barbero_id" required
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-10 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition appearance-none cursor-pointer">
                            <option value="" class="bg-slate-950">-- Elige el barbero --</option>
                            @foreach($barberosDisponibles as $barbero)
                                <option value="{{ $barbero->id }}" class="bg-slate-950">{{ $barbero->name }} ({{ $barbero->role }})</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Tipo de Servicio <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-solid fa-scissors text-sm"></i></span>
                        <select name="servicio_id" required
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-10 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition appearance-none cursor-pointer">
                            <option value="" class="bg-slate-950">-- Elige el servicio --</option>
                            @foreach($serviciosDisponibles as $servicio)
                                <option value="{{ $servicio->id }}" class="bg-slate-950">
                                    {{ $servicio->nombre }} — ${{ number_format($servicio->precio, 2) }} · Bs. {{ number_format($servicio->precio * $tasaBcv, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                    </div>
                </div>

                <!-- Método de Pago -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider">Método de Pago</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-emerald-500/50 hover:bg-emerald-500/5 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10 group">
                            <input type="radio" name="metodo_pago" value="efectivo" checked class="sr-only">
                            <i class="fa-solid fa-money-bill-wave text-emerald-400 text-base mb-1.5"></i>
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
                    class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black rounded-xl text-sm transition uppercase tracking-wider cursor-pointer shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-scissors"></i> Guardar Corte
                </button>
            </form>
        </div>

        <!-- COLUMNA DERECHA -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Mini Stat Cards -->
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-slate-900/50 backdrop-blur-md p-4 rounded-2xl border border-slate-800/60 shadow-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-money-bill-wave text-emerald-400 text-xs"></i>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Efectivo</span>
                    </div>
                    <span class="text-lg md:text-2xl font-black text-emerald-400">${{ number_format($dineroEfectivo, 2) }}</span>
                    <p class="text-[9px] text-emerald-500/60 font-bold mt-0.5">Bs. {{ number_format($dineroEfectivo * $tasaBcv, 2) }}</p>
                </div>
                <div class="bg-slate-900/50 backdrop-blur-md p-4 rounded-2xl border border-slate-800/60 shadow-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-building-columns text-blue-400 text-xs"></i>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Banco</span>
                    </div>
                    <span class="text-lg md:text-2xl font-black text-blue-400">${{ number_format($dineroTransferencia, 2) }}</span>
                    <p class="text-[9px] text-blue-500/60 font-bold mt-0.5">Bs. {{ number_format($dineroTransferencia * $tasaBcv, 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-amber-900/30 to-slate-900/60 backdrop-blur-md p-4 rounded-2xl border border-amber-800/30 shadow-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-vault text-amber-400 text-xs"></i>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Caja</span>
                    </div>
                    <span class="text-lg md:text-2xl font-black text-amber-400">${{ number_format($totalRecaudado, 2) }}</span>
                    <p class="text-[9px] text-amber-500/60 font-bold mt-0.5">Bs. {{ number_format($totalRecaudado * $tasaBcv, 2) }}</p>
                </div>
            </div>

            <!-- Historial de Cortes -->
            <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60">
                    <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-3">
                        <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Historial de Cortes
                    </h3>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden divide-y divide-slate-800/50">
                    @forelse($historialCortes as $corte)
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-bold text-white text-sm">{{ $corte->cliente->nombre ?? 'N/A' }}</p>
                                    <p class="text-xs text-slate-400">{{ $corte->barbero->name ?? 'N/A' }} · {{ $corte->fecha_hora->format('d/m H:i') }}</p>
                                </div>
                                <span class="font-black text-white">${{ number_format($corte->precio, 2) }}</span>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="bg-slate-800 border border-slate-700 text-slate-300 text-[10px] px-2 py-0.5 rounded font-medium">{{ $corte->servicio->nombre ?? 'N/A' }}</span>
                                @if($corte->estado === 'pendiente')
                                    <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-bold px-2 py-0.5 rounded">Reservado</span>
                                @elseif($corte->estado === 'fiado')
                                    <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[10px] font-bold px-2 py-0.5 rounded">Fiado</span>
                                @elseif($corte->metodo_pago === 'transferencia')
                                    <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded">Banco</span>
                                @else
                                    <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded">Efectivo</span>
                                @endif
                                
                                @if($corte->estado === 'pendiente')
                                    <form action="{{ route('reservas.completar', $corte->id) }}" method="POST" class="inline-flex gap-1 ml-auto">
                                        @csrf
                                        <select name="metodo_pago" required class="bg-slate-800 border border-slate-700 rounded-lg text-[10px] px-1.5 py-1 focus:ring-1 focus:ring-amber-500 focus:outline-none text-slate-300 cursor-pointer">
                                            <option value="efectivo">Efectivo</option>
                                            <option value="transferencia">Banco</option>
                                            <option value="fiado">Fiado</option>
                                        </select>
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-2 py-1 rounded-lg text-[10px] transition cursor-pointer">
                                            <i class="fa-solid fa-check"></i> Cobrar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-500 text-sm">
                            <i class="fa-solid fa-inbox text-2xl mb-2 opacity-20 block"></i>
                            No hay registros de cortes.
                        </div>
                    @endforelse
                </div>

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                                <th class="px-5 py-3 font-bold">Fecha</th>
                                <th class="px-5 py-3 font-bold">Cliente</th>
                                <th class="px-5 py-3 font-bold">Barbero</th>
                                <th class="px-5 py-3 font-bold">Servicio</th>
                                <th class="px-5 py-3 text-center font-bold">Estado</th>
                                <th class="px-5 py-3 text-right font-bold">Precio</th>
                                <th class="px-5 py-3 text-center font-bold">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/50 text-sm">
                            @forelse($historialCortes as $corte)
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="px-5 py-3 text-xs text-slate-500 font-medium">{{ $corte->fecha_hora->format('d/m H:i') }}</td>
                                    <td class="px-5 py-3 font-bold text-white">{{ $corte->cliente->nombre ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 text-slate-400">{{ $corte->barbero->name ?? 'N/A' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="bg-slate-800 border border-slate-700 text-slate-300 text-xs px-2.5 py-1 rounded-md font-medium">{{ $corte->servicio->nombre ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
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
                                    <td class="px-5 py-3 text-right font-black text-white">${{ number_format($corte->precio, 2) }}</td>
                                    <td class="px-5 py-3 text-center">
                                        @if($corte->estado === 'pendiente')
                                            <form action="{{ route('reservas.completar', $corte->id) }}" method="POST" class="inline-flex gap-1.5 justify-center items-center">
                                                @csrf
                                                <select name="metodo_pago" required class="bg-slate-800 border border-slate-700 rounded-lg text-xs px-2 py-1.5 focus:ring-1 focus:ring-amber-500 focus:outline-none text-slate-300 cursor-pointer">
                                                    <option value="efectivo">Efectivo</option>
                                                    <option value="transferencia">Banco</option>
                                                    <option value="fiado">Fiado</option>
                                                </select>
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-2.5 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1 shadow-sm">
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
                                        <i class="fa-solid fa-inbox text-2xl mb-2 opacity-20 block"></i>No hay registros de cortes.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
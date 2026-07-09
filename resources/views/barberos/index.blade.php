@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up space-y-8">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
        <div>
            <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-2 flex items-center gap-2">
                <i class="fa-solid fa-users"></i> Gestión de Personal
            </p>
            <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">Nómina y Comisiones</h1>
            <p class="text-slate-400 mt-2 text-sm">Monitorea la producción individual y administra el alta de barberos.</p>
        </div>
        <button onclick="toggleModalBarbero(true)"
            class="inline-flex items-center gap-2.5 px-5 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 rounded-xl font-black text-sm transition-all shadow-lg shadow-amber-500/20 cursor-pointer">
            <i class="fa-solid fa-plus"></i> Registrar Nuevo Trabajador
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tabla de Nómina -->
    <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
        <div class="px-7 py-5 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60">
            <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-3">
                <i class="fa-solid fa-table-list text-amber-400"></i> Nómina Operativa de la Semana
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                        <th class="px-7 py-3 font-bold">Barbero</th>
                        <th class="px-7 py-3 font-bold">Rol</th>
                        <th class="px-7 py-3 text-center font-bold">Cortes</th>
                        <th class="px-7 py-3 text-right font-bold">Total Producido</th>
                        <th class="px-7 py-3 text-right font-bold">Comisión (60%)</th>
                        <th class="px-7 py-3 text-right font-bold">Adelantos / Vales</th>
                        <th class="px-7 py-3 text-right font-bold">Pago Neto Sábado</th>
                        <th class="px-7 py-3 text-center font-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @foreach($nominaSabado as $item)
                        <tr class="hover:bg-slate-800/30 transition-colors group">
                            <td class="px-7 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-500 font-black text-sm shadow-inner flex-shrink-0">
                                        {{ strtoupper(substr($item['name'], 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-white">{{ $item['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-7 py-4">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-md {{ $item['role'] === 'admin' ? 'bg-amber-500/10 border border-amber-500/20 text-amber-400' : 'bg-blue-500/10 border border-blue-500/20 text-blue-400' }}">
                                    {{ ucfirst($item['role']) }}
                                </span>
                            </td>
                            <td class="px-7 py-4 text-center">
                                <span class="bg-slate-800 text-slate-300 font-bold text-sm px-3 py-1 rounded-md">{{ $item['cortes_totales'] }}</span>
                            </td>
                            <td class="px-7 py-4 text-right text-slate-400 font-semibold">
                                ${{ $item['total_producido'] }}
                            </td>
                            <td class="px-7 py-4 text-right font-black text-emerald-400">
                                ${{ $item['su_comision'] }}
                            </td>
                            <td class="px-7 py-4 text-right text-rose-400 font-bold">
                                -${{ $item['descuento_adelantos'] }}
                            </td>
                            <td class="px-7 py-4 text-right">
                                <span class="text-xl font-black text-white">${{ $item['pago_neto_este_sabado'] }}</span>
                            </td>
                            <td class="px-7 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <form action="{{ route('semana.cerrar') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="barbero_id" value="{{ $item['id'] }}">
                                        <button type="submit"
                                            class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition cursor-pointer flex items-center gap-1.5 shadow-lg shadow-emerald-500/10">
                                            <i class="fa-solid fa-lock"></i> Cerrar Cuenta
                                        </button>
                                    </form>
                                    <form action="{{ route('barberos.destroy', $item['id']) }}" method="POST"
                                        onsubmit="return confirm('¿Seguro que deseas dar de baja a este trabajador?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-rose-600/80 hover:bg-rose-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition cursor-pointer flex items-center gap-1.5">
                                            <i class="fa-solid fa-user-slash"></i> Baja
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: Registrar Barbero -->
<div id="modalBarbero" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div onclick="toggleModalBarbero(false)" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

    <div class="relative bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden z-10">
        <div class="p-7">
            <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-6">
                <h3 class="text-lg font-black text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-amber-400 text-sm"></i>
                    </div>
                    Registrar Personal
                </h3>
                <button type="button" onclick="toggleModalBarbero(false)"
                    class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('barberos.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Nombre Completo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-regular fa-user text-sm"></i>
                        </span>
                        <input type="text" name="nombre" required placeholder="Ej. Pedro Pérez"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition placeholder-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Rol / Cargo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-solid fa-user-shield text-sm"></i>
                        </span>
                        <select name="rol"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-10 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition appearance-none cursor-pointer">
                            <option value="Barbero" class="bg-slate-900">Barbero</option>
                            <option value="admin" class="bg-slate-900">Administrador</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800 mt-6">
                    <button type="button" onclick="toggleModalBarbero(false)"
                        class="px-5 py-2.5 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-black text-slate-950 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 rounded-xl transition-all shadow-lg shadow-amber-500/20 cursor-pointer">
                        <i class="fa-solid fa-plus mr-1.5"></i> Guardar Barbero
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleModalBarbero(action) {
        document.getElementById('modalBarbero').style.display = action ? 'flex' : 'none';
    }
</script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
</style>
@endsection

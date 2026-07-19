@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1 flex items-center gap-2">
                <i class="fa-solid fa-users"></i> Gestión de Personal
            </p>
            <h1 class="text-2xl md:text-4xl font-black text-white tracking-tight">Nómina y Comisiones</h1>
            <p class="text-slate-400 mt-1 text-xs md:text-sm">Monitorea la producción individual y administra el alta de barberos.</p>
        </div>
        <button onclick="toggleModalBarbero(true)"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 rounded-xl font-black text-xs md:text-sm transition-all shadow-lg shadow-amber-500/20 cursor-pointer flex-shrink-0">
            <i class="fa-solid fa-plus"></i> Nuevo Trabajador
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

    <!-- TABLA DE NÓMINA -->
    <div class="bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60">
            <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-3">
                <i class="fa-solid fa-table-list text-amber-400"></i> Nómina Operativa de la Semana
            </h3>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-slate-800/50">
            @foreach($nominaSabado as $item)
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-500 font-black text-sm shadow-inner flex-shrink-0">
                                {{ strtoupper(substr($item['name'], 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-white text-sm">{{ $item['name'] }}</p>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $item['role'] === 'admin' ? 'bg-amber-500/10 border border-amber-500/20 text-amber-400' : 'bg-blue-500/10 border border-blue-500/20 text-blue-400' }}">
                                    {{ ucfirst($item['role']) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-slate-500 uppercase tracking-wider">Pago Neto</p>
                            <p class="text-lg font-black text-white">${{ $item['pago_neto_este_sabado'] }}</p>
                            <p class="text-[9px] text-amber-500/60 font-bold">Bs. {{ number_format((float)str_replace(',','',$item['pago_neto_este_sabado']) * $tasaBcv, 2) }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div class="bg-slate-800/50 rounded-lg p-2 text-center">
                            <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Cortes</p>
                            <p class="font-bold text-slate-300 text-sm">{{ $item['cortes_totales'] }}</p>
                        </div>
                        <div class="bg-slate-800/50 rounded-lg p-2 text-center">
                            <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Comisión</p>
                            <p class="font-bold text-emerald-400 text-sm">${{ $item['su_comision'] }}</p>
                            <span class="text-[8px] text-slate-500 block mb-0.5">({{ $item['porcentaje_comision'] !== null ? $item['porcentaje_comision'] : ($barberia->porcentaje_barbero ?? 60) }}%)</span>
                        </div>
                        <div class="bg-slate-800/50 rounded-lg p-2 text-center">
                            <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Adelantos</p>
                            <p class="font-bold text-rose-400 text-sm">-${{ $item['descuento_adelantos'] }}</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 mb-2">
                        <button type="button"
                            onclick="abrirModalPago({{ $item['id'] }}, '{{ addslashes($item['name']) }}', '{{ $item['su_comision'] }}', '{{ $item['descuento_adelantos'] }}', '{{ $item['pago_neto_este_sabado'] }}')"
                            class="flex-1 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-black py-3 px-4 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20 text-sm">
                            <i class="fa-solid fa-hand-holding-dollar text-base"></i>
                            <span>Confirmar Pago</span>
                        </button>
                        <button onclick="abrirModalEditar({{ $item['id'] }}, '{{ addslashes($item['name']) }}', {{ $item['porcentaje_comision'] ?? 'null' }})"
                            class="bg-slate-800 hover:bg-blue-900/40 border border-slate-700 hover:border-blue-700/60 text-slate-400 hover:text-blue-400 text-xs font-bold px-3 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-pencil text-xs"></i>
                        </button>
                        <form action="{{ route('barberos.destroy', $item['id']) }}" method="POST"
                            onsubmit="return confirm('¿Seguro que deseas dar de baja a este trabajador?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-slate-800 hover:bg-rose-900/40 border border-slate-700 hover:border-rose-700/60 text-slate-400 hover:text-rose-400 text-xs font-bold px-3 h-full rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-user-slash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if(empty($nominaSabado))
                <div class="py-12 text-center text-slate-500 text-sm">
                    <i class="fa-solid fa-users-slash text-2xl mb-2 opacity-20 block"></i>
                    No hay personal registrado.
                </div>
            @endif
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                        <th class="px-5 py-3 font-bold">Barbero</th>
                        <th class="px-5 py-3 font-bold">Rol</th>
                        <th class="px-5 py-3 text-center font-bold">Cortes</th>
                        <th class="px-5 py-3 text-right font-bold">Total Producido</th>
                        <th class="px-5 py-3 text-right font-bold">Comisión (%)</th>
                        <th class="px-5 py-3 text-right font-bold">Adelantos</th>
                        <th class="px-5 py-3 text-right font-bold">Pago Neto Sáb.</th>
                        <th class="px-5 py-3 text-center font-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    @foreach($nominaSabado as $item)
                        <tr class="hover:bg-slate-800/30 transition-colors group">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-500 font-black text-sm shadow-inner flex-shrink-0">
                                        {{ strtoupper(substr($item['name'], 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-white">{{ $item['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-md {{ $item['role'] === 'admin' ? 'bg-amber-500/10 border border-amber-500/20 text-amber-400' : 'bg-blue-500/10 border border-blue-500/20 text-blue-400' }}">
                                    {{ ucfirst($item['role']) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="bg-slate-800 text-slate-300 font-bold text-sm px-3 py-1 rounded-md">{{ $item['cortes_totales'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-right text-slate-400 font-semibold">${{ $item['total_producido'] }}</td>
                            <td class="px-5 py-4 text-right text-emerald-400">
                                <span class="font-black">${{ $item['su_comision'] }}</span>
                                <span class="text-[10px] text-slate-500 block font-normal">({{ $item['porcentaje_comision'] !== null ? $item['porcentaje_comision'] : ($barberia->porcentaje_barbero ?? 60) }}%)</span>
                            </td>
                            <td class="px-5 py-4 text-right text-rose-400 font-bold">-${{ $item['descuento_adelantos'] }}</td>
                            <td class="px-5 py-4 text-right">
                                <div>
                                    <span class="text-xl font-black text-white">${{ $item['pago_neto_este_sabado'] }}</span>
                                    <p class="text-[10px] text-amber-500/60 font-bold">Bs. {{ number_format((float)str_replace(',','',$item['pago_neto_este_sabado']) * $tasaBcv, 0) }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button type="button"
                                        title="Registrar pago al barbero y limpiar adelantos"
                                        onclick="abrirModalPago({{ $item['id'] }}, '{{ addslashes($item['name']) }}', '{{ $item['su_comision'] }}', '{{ $item['descuento_adelantos'] }}', '{{ $item['pago_neto_este_sabado'] }}')"
                                        class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition cursor-pointer flex items-center gap-1.5 shadow-lg shadow-emerald-500/10">
                                        <i class="fa-solid fa-hand-holding-dollar"></i> Pagar
                                    </button>
                                    <button onclick="abrirModalEditar({{ $item['id'] }}, '{{ addslashes($item['name']) }}', {{ $item['porcentaje_comision'] ?? 'null' }})"
                                        title="Editar barbero"
                                        class="bg-blue-600/80 hover:bg-blue-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition cursor-pointer flex items-center gap-1.5">
                                        <i class="fa-solid fa-pencil"></i> Editar
                                    </button>
                                    <form action="{{ route('barberos.destroy', $item['id']) }}" method="POST"
                                        onsubmit="return confirm('¿Seguro que deseas dar de baja a este trabajador?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-600/80 hover:bg-rose-600 text-white text-xs font-bold py-1.5 px-2 rounded-lg transition cursor-pointer flex items-center gap-1.5">
                                            <i class="fa-solid fa-user-slash"></i>
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
<div id="modalBarbero" style="display: none;" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div onclick="toggleModalBarbero(false)" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

    <div class="relative bg-slate-900 border border-slate-800 w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden z-10">
        <div class="p-5 md:p-7">
            <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-5">
                <h3 class="text-base font-black text-white flex items-center gap-3">
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
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-regular fa-user text-sm"></i></span>
                        <input type="text" name="nombre" required placeholder="Ej. Pedro Pérez"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition placeholder-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Rol / Cargo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-solid fa-user-shield text-sm"></i></span>
                        <select name="rol" class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-10 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition appearance-none cursor-pointer">
                            <option value="barbero" class="bg-slate-900">Barbero</option>
                            <option value="admin" class="bg-slate-900">Administrador</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Porcentaje Comisión (%)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-solid fa-percent text-sm"></i></span>
                        <input type="number" name="porcentaje_comision" min="0" max="100" placeholder="Ej. 60 (Por defecto: {{ $barberia->porcentaje_barbero ?? 60 }}%)"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition placeholder-slate-600">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
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

<!-- MODAL: Editar Barbero -->
<div id="modalEditarBarbero" style="display: none;" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div onclick="cerrarModalEditar()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

    <div class="relative bg-slate-900 border border-slate-800 w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden z-10">
        <div class="p-5 md:p-7">
            <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-5">
                <h3 class="text-base font-black text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-pencil text-blue-400 text-sm"></i>
                    </div>
                    Editar Barbero
                </h3>
                <button type="button" onclick="cerrarModalEditar()"
                    class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form id="formEditarBarbero" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Nombre Completo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-regular fa-user text-sm"></i></span>
                        <input type="text" id="editNombre" name="nombre" required
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Porcentaje Comisión (%)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-solid fa-percent text-sm"></i></span>
                        <input type="number" id="editPorcentaje" name="porcentaje_comision" min="0" max="100" placeholder="{{ $barberia->porcentaje_barbero ?? 60 }}"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition placeholder-slate-600">
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">Dejar vacío para usar el porcentaje global ({{ $barberia->porcentaje_barbero ?? 60 }}%).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Nueva Contraseña <span class="font-normal text-slate-500 normal-case">(opcional)</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500"><i class="fa-solid fa-key text-sm"></i></span>
                        <input type="password" name="password" minlength="6" placeholder="Mínimo 6 caracteres"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition placeholder-slate-600">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="cerrarModalEditar()"
                        class="px-5 py-2.5 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-black text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 rounded-xl transition-all shadow-lg shadow-blue-500/20 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk mr-1.5"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Confirmar Pago al Barbero -->
<div id="modalConfirmarPago" style="display: none;" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div onclick="cerrarModalPago()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

    <div class="relative bg-slate-900 border border-slate-800 w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden z-10">
        <div class="p-5 md:p-7">
            <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-5">
                <h3 class="text-base font-black text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-hand-holding-dollar text-emerald-400 text-sm"></i>
                    </div>
                    Confirmar Pago Semanal
                </h3>
                <button type="button" onclick="cerrarModalPago()"
                    class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <p class="text-slate-400 text-xs mb-3">Estás a punto de registrar el pago semanal para:</p>
            <p class="text-white font-black text-lg mb-5 flex items-center gap-3">
                <span id="pagoAvatar" class="w-9 h-9 rounded-full bg-slate-800 border border-amber-500/30 flex items-center justify-center text-amber-500 font-black text-sm flex-shrink-0"></span>
                <span id="pagoNombreTexto"></span>
            </p>

            <div class="space-y-2 mb-5">
                <div class="flex items-center justify-between px-4 py-3 bg-slate-800/60 rounded-xl">
                    <span class="text-xs text-slate-400 font-bold flex items-center gap-2">
                        <i class="fa-solid fa-scissors text-emerald-400/70"></i> Comisión ganada esta semana
                    </span>
                    <span id="pagoComision" class="text-emerald-400 font-black text-sm"></span>
                </div>
                <div class="flex items-center justify-between px-4 py-3 bg-slate-800/60 rounded-xl">
                    <span class="text-xs text-slate-400 font-bold flex items-center gap-2">
                        <i class="fa-solid fa-minus text-rose-400/70"></i> Adelantos que se descuentan
                    </span>
                    <span id="pagoAdelantos" class="text-rose-400 font-black text-sm"></span>
                </div>
                <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-emerald-950/60 to-slate-900/80 border border-emerald-500/20 rounded-xl">
                    <span class="text-sm text-white font-black flex items-center gap-2">
                        <i class="fa-solid fa-circle-dollar-to-slot text-emerald-400"></i> Total a entregar en mano
                    </span>
                    <span id="pagoNeto" class="text-emerald-400 font-black text-xl"></span>
                </div>
            </div>

            <div class="p-3.5 bg-amber-500/10 border border-amber-500/20 rounded-xl text-xs text-amber-300 flex items-start gap-3 mb-5">
                <i class="fa-solid fa-circle-info text-amber-400 mt-0.5 flex-shrink-0"></i>
                <span>Al confirmar, los <strong>adelantos pendientes quedarán marcados como pagados</strong> y la próxima semana el barbero empieza desde cero. No se elimina ningún registro histórico.</span>
            </div>

            <form id="formConfirmarPago" action="{{ route('semana.cerrar') }}" method="POST">
                @csrf
                <input type="hidden" id="pagoBarberoId" name="barbero_id" value="">
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="cerrarModalPago()"
                        class="px-5 py-2.5 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-black text-white bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 rounded-xl transition-all shadow-lg shadow-emerald-500/20 cursor-pointer flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> Sí, confirmar pago
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
    function abrirModalEditar(id, nombre, porcentaje) {
        document.getElementById('formEditarBarbero').action = '/trabajador/' + id;
        document.getElementById('editNombre').value = nombre;
        document.getElementById('editPorcentaje').value = porcentaje !== null ? porcentaje : '';
        document.getElementById('modalEditarBarbero').style.display = 'flex';
    }
    function cerrarModalEditar() {
        document.getElementById('modalEditarBarbero').style.display = 'none';
    }
    function abrirModalPago(id, nombre, comision, adelantos, neto) {
        document.getElementById('pagoBarberoId').value = id;
        document.getElementById('pagoNombreTexto').textContent = nombre;
        document.getElementById('pagoAvatar').textContent = nombre.charAt(0).toUpperCase();
        document.getElementById('pagoComision').textContent = '+$' + comision;
        document.getElementById('pagoAdelantos').textContent = adelantos > 0 ? '-$' + adelantos : '$0.00';
        document.getElementById('pagoNeto').textContent = '$' + neto;
        document.getElementById('modalConfirmarPago').style.display = 'flex';
    }
    function cerrarModalPago() {
        document.getElementById('modalConfirmarPago').style.display = 'none';
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

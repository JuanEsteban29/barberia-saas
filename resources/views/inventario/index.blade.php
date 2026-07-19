@extends('layouts.app')

@section('content')
<div class="animate-fade-in-up space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1 flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked"></i> Control de Stock y Ventas
            </p>
            <h1 class="text-2xl md:text-4xl font-black text-white tracking-tight">Inventario de Productos</h1>
            <p class="text-slate-400 mt-1 text-xs md:text-sm">Administra ceras, lociones y productos. Registra ventas asignando comisiones.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <button onclick="toggleModalVenta(true)"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-slate-950 rounded-xl font-black text-xs md:text-sm transition-all shadow-lg shadow-emerald-500/20 cursor-pointer">
                <i class="fa-solid fa-cart-shopping"></i> Registrar Venta
            </button>
            <button onclick="toggleModalProducto(true)"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 rounded-xl font-black text-xs md:text-sm transition-all shadow-lg shadow-amber-500/20 cursor-pointer">
                <i class="fa-solid fa-plus"></i> Nuevo Producto
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-xmark text-lg"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Productos -->
        <div class="bg-slate-900/50 backdrop-blur-md p-5 rounded-2xl border border-slate-800/60 shadow-lg hover:border-amber-500/20 transition-colors">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-tag text-amber-400 text-xs"></i>
                </div>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Variedad Productos</p>
            </div>
            <p class="text-2xl font-black text-white">{{ $totalProductos }}</p>
            <p class="text-[10px] text-slate-500 mt-1 uppercase font-semibold">Tipos de productos en catálogo</p>
        </div>

        <!-- Valor Inventario -->
        <div class="bg-slate-900/50 backdrop-blur-md p-5 rounded-2xl border border-slate-800/60 shadow-lg hover:border-emerald-500/20 transition-colors">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-warehouse text-emerald-400 text-xs"></i>
                </div>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Valor del Stock</p>
            </div>
            <p class="text-2xl font-black text-emerald-400">${{ number_format($valorInventario, 2) }}</p>
            <p class="text-[10px] text-slate-500 mt-1 uppercase font-semibold">Valor total de venta acumulado</p>
        </div>

        <!-- Total Vendido -->
        <div class="bg-slate-900/50 backdrop-blur-md p-5 rounded-2xl border border-slate-800/60 shadow-lg hover:border-blue-500/20 transition-colors">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-cart-shopping text-blue-400 text-xs"></i>
                </div>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Ventas Realizadas</p>
            </div>
            <p class="text-2xl font-black text-blue-400">${{ number_format($totalVendido, 2) }}</p>
            <p class="text-[10px] text-slate-500 mt-1 uppercase font-semibold">Ventas de productos registradas</p>
        </div>
    </div>

    <!-- TABS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Grid 1: Catalogo de Productos (2/3 width) -->
        <div class="lg:col-span-2 bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60 flex justify-between items-center">
                <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-amber-400"></i> Catálogo en Stock
                </h3>
            </div>

            <!-- Mobile Cards List -->
            <div class="md:hidden divide-y divide-slate-800/50">
                @forelse($productos as $producto)
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-white text-sm">{{ $producto->nombre }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $producto->descripcion ?? 'Sin descripción' }}</p>
                            </div>
                            <span class="text-amber-400 font-black text-sm">${{ number_format($producto->precio_venta, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-slate-500">Stock:</span>
                                @if($producto->stock <= 0)
                                    <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[10px] font-black px-2 py-0.5 rounded">Agotado</span>
                                @elseif($producto->stock <= 3)
                                    <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-black px-2 py-0.5 rounded">{{ $producto->stock }} units (Bajo)</span>
                                @else
                                    <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black px-2 py-0.5 rounded">{{ $producto->stock }} units</span>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <button onclick='fillEditProductoModal(@json($producto))' class="text-[10px] font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-1 rounded">Editar</button>
                                <form action="{{ route('inventario.destroy', $producto->id) }}" method="POST" onsubmit="return confirm('¿Seguro de eliminar este producto?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 px-2 py-1 rounded">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-500 text-sm">
                        <i class="fa-solid fa-inbox text-3xl mb-2 opacity-20 block"></i>
                        Catálogo vacío. Agrega tu primer producto.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 uppercase text-[10px] tracking-widest border-b border-slate-800/60">
                            <th class="px-5 py-3 font-bold">Producto</th>
                            <th class="px-5 py-3 text-right font-bold">Precio Compra</th>
                            <th class="px-5 py-3 text-right font-bold">Precio Venta</th>
                            <th class="px-5 py-3 text-center font-bold">Stock</th>
                            <th class="px-5 py-3 text-center font-bold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50 text-sm">
                        @forelse($productos as $producto)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-white">{{ $producto->nombre }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $producto->descripcion ?? 'Sin descripción' }}</p>
                                </td>
                                <td class="px-5 py-4 text-right text-slate-400 font-mono">${{ number_format($producto->precio_compra, 2) }}</td>
                                <td class="px-5 py-4 text-right font-bold text-amber-400 font-mono">${{ number_format($producto->precio_venta, 2) }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if($producto->stock <= 0)
                                        <span class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold px-2 py-0.5 rounded-md">Agotado</span>
                                    @elseif($producto->stock <= 3)
                                        <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-bold px-2 py-0.5 rounded-md">{{ $producto->stock }} units (Bajo)</span>
                                    @else
                                        <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold px-2 py-0.5 rounded-md">{{ $producto->stock }} units</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button onclick='fillEditProductoModal(@json($producto))' class="bg-amber-500 hover:bg-amber-400 text-black text-xs font-black py-1 px-2.5 rounded-lg transition-colors cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('inventario.destroy', $producto->id) }}" method="POST" onsubmit="return confirm('¿Seguro de eliminar este producto?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-rose-600/80 hover:bg-rose-600 text-white text-xs font-black py-1 px-2.5 rounded-lg transition-colors cursor-pointer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500 text-sm">
                                    <i class="fa-solid fa-inbox text-3xl mb-2 opacity-20 block"></i>
                                    Catálogo vacío. Agrega tu primer producto.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grid 2: Historial de Ventas (1/3 width) -->
        <div class="lg:col-span-1 bg-slate-900/50 backdrop-blur-md rounded-2xl border border-slate-800/60 shadow-xl overflow-hidden flex flex-col">
            <div class="px-5 py-4 bg-gradient-to-r from-slate-800/80 to-transparent border-b border-slate-800/60">
                <h3 class="font-bold text-white text-xs uppercase tracking-widest flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-400"></i> Últimas Ventas
                </h3>
            </div>
            
            <div class="p-4 space-y-3 flex-1 overflow-y-auto max-h-[480px]">
                @forelse($ventas as $venta)
                    <div class="bg-slate-950/40 border border-slate-850 p-3.5 rounded-xl space-y-2 relative">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-white text-xs">{{ $venta->producto->nombre }}</p>
                                <p class="text-[9px] text-slate-500">{{ $venta->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-sm text-blue-400">${{ number_format($venta->cantidad * $venta->precio_unitario, 2) }}</p>
                                <p class="text-[8px] text-slate-500">{{ $venta->cantidad }} x ${{ number_format($venta->precio_unitario, 2) }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-900/80 text-[10px]">
                            <span class="text-slate-400">Barbero: <strong class="text-slate-200">{{ $venta->barbero->name ?? 'Ninguno' }}</strong></span>
                            @if($venta->metodo_pago === 'efectivo_usd')
                                <span class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[8px] font-bold px-1.5 py-0.5 rounded">Efe $</span>
                            @elseif($venta->metodo_pago === 'efectivo_bs')
                                <span class="bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[8px] font-bold px-1.5 py-0.5 rounded">Efe Bs.</span>
                            @else
                                <span class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[8px] font-bold px-1.5 py-0.5 rounded">Trans</span>
                            @endif
                        </div>
                        @if($venta->comision_barbero > 0)
                            <div class="text-[8px] text-amber-500/70 font-semibold absolute top-1 right-2 bg-amber-500/5 px-1 py-0.2 rounded border border-amber-500/10">
                                Comisión: +${{ number_format($venta->comision_barbero, 2) }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-500 text-sm">
                        <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 opacity-20 block"></i>
                        No hay ventas registradas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Nuevo / Editar Producto -->
<div id="modalProducto" style="display: none;" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div onclick="toggleModalProducto(false)" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

    <div class="relative bg-slate-900 border border-slate-800 w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden z-10">
        <div class="p-5 md:p-7">
            <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-5">
                <h3 id="modalProductoTitle" class="text-base font-black text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-plus text-amber-400 text-sm"></i>
                    </div>
                    Registrar Producto
                </h3>
                <button type="button" onclick="toggleModalProducto(false)"
                    class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form id="formProducto" action="{{ route('inventario.store') }}" method="POST" class="space-y-4">
                @csrf
                <div id="formMethodContainer"></div> {{-- Para poner @method('PUT') si se edita --}}

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Nombre del Producto</label>
                    <input type="text" name="nombre" id="prod_nombre" required placeholder="Ej. Cera Premium Mate"
                        class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Descripción (Opcional)</label>
                    <textarea name="descripcion" id="prod_descripcion" placeholder="Ej. Fijación fuerte, efecto seco." rows="2"
                        class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Costo Compra ($)</label>
                        <input type="number" step="0.01" name="precio_compra" id="prod_compra" required placeholder="0.00"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Precio Venta ($)</label>
                        <input type="number" step="0.01" name="precio_venta" id="prod_venta" required placeholder="0.00"
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Cantidad en Stock</label>
                    <input type="number" name="stock" id="prod_stock" required placeholder="0"
                        class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition font-mono">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="toggleModalProducto(false)"
                        class="px-5 py-2.5 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-black text-slate-950 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 rounded-xl transition-all shadow-lg shadow-amber-500/20 cursor-pointer">
                        Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Registrar Venta -->
<div id="modalVenta" style="display: none;" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div onclick="toggleModalVenta(false)" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

    <div class="relative bg-slate-900 border border-slate-800 w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden z-10">
        <div class="p-5 md:p-7">
            <div class="flex justify-between items-center pb-4 border-b border-slate-800 mb-5">
                <h3 class="text-base font-black text-white flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                        <i class="fa-solid fa-cart-shopping text-emerald-400 text-sm"></i>
                    </div>
                    Registrar Venta de Producto
                </h3>
                <button type="button" onclick="toggleModalVenta(false)"
                    class="w-8 h-8 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="{{ route('inventario.vender') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Producto a Vender</label>
                    <select name="producto_id" required class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition cursor-pointer">
                        <option value="">— Selecciona un producto —</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id }}" {{ $prod->stock <= 0 ? 'disabled' : '' }} class="bg-slate-900">
                                {{ $prod->nombre }} - ${{ number_format($prod->precio_venta, 2) }} (Stock: {{ $prod->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Cantidad</label>
                        <input type="number" name="cantidad" min="1" value="1" required
                            class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2 tracking-wider">Barbero (Recomienda)</label>
                        <select name="barbero_id" class="w-full bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition cursor-pointer">
                            <option value="" class="bg-slate-900">Ninguno (Venta Local)</option>
                            @foreach($barberos as $barbero)
                                <option value="{{ $barbero->id }}" class="bg-slate-900">{{ $barbero->name }} (10% com.)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Método de Pago -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-3 tracking-wider">Método de Pago</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-emerald-500/50 hover:bg-emerald-500/5 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10 group">
                            <input type="radio" name="metodo_pago" value="efectivo_usd" checked class="sr-only">
                            <i class="fa-solid fa-money-bill-wave text-emerald-400 text-lg mb-1.5"></i>
                            <span class="text-[9px] font-bold text-slate-400 group-has-[:checked]:text-emerald-400">Efe $</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-amber-500/50 hover:bg-amber-500/5 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/10 group">
                            <input type="radio" name="metodo_pago" value="efectivo_bs" class="sr-only">
                            <i class="fa-solid fa-money-bill-transfer text-amber-400 text-lg mb-1.5"></i>
                            <span class="text-[9px] font-bold text-slate-400 group-has-[:checked]:text-amber-400">Efe Bs.</span>
                        </label>
                        <label class="flex flex-col items-center justify-center p-3 border border-slate-800 rounded-xl bg-slate-950/60 cursor-pointer hover:border-blue-500/50 hover:bg-blue-500/5 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/10 group">
                            <input type="radio" name="metodo_pago" value="transferencia" class="sr-only">
                            <i class="fa-solid fa-building-columns text-blue-400 text-lg mb-1.5"></i>
                            <span class="text-[9px] font-bold text-slate-400 group-has-[:checked]:text-blue-400">Banco</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" onclick="toggleModalVenta(false)"
                        class="px-5 py-2.5 text-sm font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-black text-slate-950 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 rounded-xl transition-all shadow-lg shadow-emerald-500/20 cursor-pointer">
                        Registrar Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleModalProducto(action) {
        if (action) {
            document.getElementById('modalProductoTitle').innerHTML = `
                <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                    <i class="fa-solid fa-plus text-amber-400 text-sm"></i>
                </div>
                Registrar Producto
            `;
            document.getElementById('formProducto').action = "{{ route('inventario.store') }}";
            document.getElementById('formMethodContainer').innerHTML = "";
            document.getElementById('prod_nombre').value = "";
            document.getElementById('prod_descripcion').value = "";
            document.getElementById('prod_compra').value = "";
            document.getElementById('prod_venta').value = "";
            document.getElementById('prod_stock').value = "";
        }
        document.getElementById('modalProducto').style.display = action ? 'flex' : 'none';
    }

    function fillEditProductoModal(prod) {
        toggleModalProducto(false);
        document.getElementById('modalProductoTitle').innerHTML = `
            <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                <i class="fa-solid fa-pen-to-square text-amber-400 text-sm"></i>
            </div>
            Editar Producto
        `;
        document.getElementById('formProducto').action = "/inventario/" + prod.id;
        document.getElementById('formMethodContainer').innerHTML = `<input type="hidden" name="_method" value="PUT">`;
        document.getElementById('prod_nombre').value = prod.nombre;
        document.getElementById('prod_descripcion').value = prod.descripcion || "";
        document.getElementById('prod_compra').value = prod.precio_compra;
        document.getElementById('prod_venta').value = prod.precio_venta;
        document.getElementById('prod_stock').value = prod.stock;

        document.getElementById('modalProducto').style.display = 'flex';
    }

    function toggleModalVenta(action) {
        document.getElementById('modalVenta').style.display = action ? 'flex' : 'none';
    }
</script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.4s ease-out forwards; }
</style>
@endsection

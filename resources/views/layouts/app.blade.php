<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Barberia ERP - Panel Premium</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(9, 9, 11, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.04);
        }
        .nav-item-active {
            background: linear-gradient(90deg, rgba(245,166,35,0.18) 0%, rgba(245,166,35,0.02) 100%);
            border-left: 3px solid #f5a623;
            color: #fbbf24;
        }
        .nav-item {
            border-left: 3px solid transparent;
            transition: all 0.25s ease;
        }
        .nav-item:hover {
            background: rgba(245,166,35,0.05);
            border-left: 3px solid rgba(245,166,35,0.4);
            color: #fcd34d;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(245,166,35,0.15); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(245,166,35,0.3); }

        /* Bottom Nav active state */
        .bottom-nav-item { transition: all 0.2s ease; }
        .bottom-nav-item.active { color: #f5a623; }
        .bottom-nav-item.active i { transform: translateY(-2px); }

        /* Safe area for notched phones */
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom, 12px); }

        /* Notification dropdown */
        #notif-dropdown { display: none; }
        #notif-dropdown.open { display: block; animation: fadeDown .2s ease; }
        @keyframes fadeDown { from { opacity:0; transform: translateY(-8px); } to { opacity:1; transform: translateY(0); } }

        /* Gold shimmer on cards */
        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        .gold-shimmer {
            background: linear-gradient(90deg, transparent, rgba(245,166,35,.08), transparent);
            background-size: 200% auto;
            animation: shimmer 3s linear infinite;
        }
    </style>
</head>
<body class="text-slate-300 font-sans flex h-screen overflow-hidden relative" style="background-color:#09090b;">

    <!-- Ambient Glow Background -->
    <div class="absolute top-[-15%] left-[-5%] w-[35%] h-[50%] rounded-full blur-[140px] pointer-events-none" style="background:rgba(245,166,35,0.07);"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-[30%] h-[40%] rounded-full blur-[120px] pointer-events-none" style="background:rgba(245,166,35,0.04);"></div>

    <!-- SIDEBAR BACKDROP (Mobile only) -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR (Desktop always visible, Mobile slide-in) -->
    <aside id="sidebar" class="fixed md:static inset-y-0 left-0 w-72 glass-panel flex flex-col h-[100dvh] max-h-[100dvh] shadow-2xl z-50 border-r border-slate-800/50 transition-transform duration-300 transform -translate-x-full md:translate-x-0">
        <!-- Close Button (Mobile only) -->
        <button onclick="toggleSidebar()" class="absolute top-4 right-4 text-slate-400 hover:text-white md:hidden cursor-pointer" title="Cerrar Menú">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <!-- Logo -->
        <div class="p-6 border-b flex items-center space-x-3" style="border-color:rgba(245,166,35,.12);">
            @if(auth()->check() && auth()->user()->barberia && auth()->user()->barberia->logo)
                <img src="{{ auth()->user()->barberia->logo }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl border border-slate-800 shadow-lg">
            @else
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg" style="background:linear-gradient(135deg,#f5a623,#d4891a);box-shadow:0 4px 20px rgba(245,166,35,.3);">
                    <i class="fa-solid fa-scissors text-black text-lg"></i>
                </div>
            @endif
            <div>
                <h2 class="text-xl font-black tracking-widest text-white leading-none">BARBER<span style="color:#f5a623;">ERP</span></h2>
                <p class="text-[10px] uppercase tracking-widest font-semibold mt-1" style="color:#52525b;">Premium &bull; Black & Gold</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-6 overflow-y-auto space-y-1">
            <div class="px-6 mb-2 text-xs font-bold text-slate-500 uppercase tracking-wider">Menú Principal</div>
            @if(auth()->check())
                @if(strtolower(auth()->user()->role) === 'admin')
                    <a href="{{ route('dashboard') }}" class="nav-item flex items-center space-x-4 px-6 py-3 {{ request()->routeIs('dashboard') ? 'nav-item-active' : 'text-slate-400' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-lg"></i>
                        <span class="font-medium text-sm">Dashboard</span>
                    </a>
                    <a href="{{ route('cortes.index') }}" class="nav-item flex items-center space-x-4 px-6 py-3 {{ request()->routeIs('cortes.index') ? 'nav-item-active' : 'text-slate-400' }}">
                        <i class="fa-solid fa-cut w-5 text-center text-lg"></i>
                        <span class="font-medium text-sm">Cortes y Servicios</span>
                    </a>
                    <a href="{{ route('barberos.index') }}" class="nav-item flex items-center space-x-4 px-6 py-3 {{ request()->routeIs('barberos.index') ? 'nav-item-active' : 'text-slate-400' }}">
                        <i class="fa-solid fa-user-tie w-5 text-center text-lg"></i>
                        <span class="font-medium text-sm">Personal</span>
                    </a>
                    <a href="{{ route('finanzas.index') }}" class="nav-item flex items-center space-x-4 px-6 py-3 {{ request()->routeIs('finanzas.index') ? 'nav-item-active' : 'text-slate-400' }}">
                        <i class="fa-solid fa-wallet w-5 text-center text-lg"></i>
                        <span class="font-medium text-sm">Finanzas</span>
                    </a>
                    <a href="{{ route('cierre.index') }}" class="nav-item flex items-center space-x-4 px-6 py-3 {{ request()->routeIs('cierre.index') ? 'nav-item-active' : 'text-slate-400' }}">
                        <i class="fa-solid fa-lock w-5 text-center text-lg"></i>
                        <span class="font-medium text-sm">Cierre de Caja</span>
                    </a>
                    <a href="{{ route('fiados.index') }}" class="nav-item flex items-center justify-between px-6 py-3 {{ request()->routeIs('fiados.index') ? 'nav-item-active' : 'text-slate-400' }}">
                        <div class="flex items-center space-x-4">
                            <i class="fa-solid fa-handshake w-5 text-center text-lg"></i>
                            <span class="font-medium text-sm">Cuentas por Cobrar</span>
                        </div>
                        @if(isset($fiadosCount) && $fiadosCount > 0)
                            <span class="bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-md shadow-lg shadow-rose-500/30">
                                {{ $fiadosCount }}
                            </span>
                        @endif
                    </a>
                @elseif(strtolower(auth()->user()->role) === 'barbero')
                    <a href="{{ route('barbero.dashboard') }}" class="nav-item flex items-center space-x-4 px-6 py-3 {{ request()->routeIs('barbero.dashboard') ? 'nav-item-active' : 'text-slate-400' }}">
                        <i class="fa-solid fa-chart-line w-5 text-center text-lg"></i>
                        <span class="font-medium text-sm">Mi Rendimiento</span>
                    </a>
                @endif
                
                <div class="px-6 mt-6 mb-2 text-xs font-bold text-slate-500 uppercase tracking-wider">Módulos</div>
                @if(strtolower(auth()->user()->role) === 'admin')
                    <a href="{{ route('reservas.index') }}" class="nav-item flex items-center space-x-4 px-6 py-3 {{ request()->routeIs('reservas.index') ? 'nav-item-active' : 'text-slate-400' }}">
                        <i class="fa-regular fa-calendar-check w-5 text-center text-lg"></i>
                        <span class="font-medium text-sm">Ver Reservas</span>
                    </a>
                    <a href="{{ route('barberia.configuracion') }}" class="nav-item flex items-center space-x-4 px-6 py-3 {{ request()->routeIs('barberia.configuracion') ? 'nav-item-active' : 'text-slate-400' }}">
                        <i class="fa-solid fa-gears w-5 text-center text-lg"></i>
                        <span class="font-medium text-sm">Configuraciones</span>
                    </a>
                @else
                    <a href="{{ route('reservas.public.create', auth()->user()->barberia->slug ?? 'demo') }}" target="_blank" class="nav-item flex items-center space-x-4 px-6 py-3 text-slate-400 group">
                        <i class="fa-regular fa-calendar-check w-5 text-center text-lg group-hover:text-emerald-400 transition-colors"></i>
                        <span class="font-medium text-sm group-hover:text-emerald-400 transition-colors">Ver Link de Reservas</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-auto opacity-50"></i>
                    </a>
                @endif

                {{-- Cerrar sesión en el menú para móviles por si el footer queda tapado --}}
                <div class="h-px bg-slate-800/60 my-4 mx-6"></div>
                <form action="{{ route('logout') }}" method="POST" class="block w-full">
                    @csrf
                    <button type="submit" class="w-full nav-item flex items-center space-x-4 px-6 py-3 text-rose-400 hover:text-rose-300 hover:bg-rose-500/5 cursor-pointer">
                        <i class="fa-solid fa-power-off w-5 text-center text-lg"></i>
                        <span class="font-medium text-sm">Cerrar Sesión</span>
                    </button>
                </form>
            @endif
        </nav>

        <!-- Footer Sidebar -->
        <div class="p-6 border-t border-slate-800/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center border border-slate-700 shadow-inner flex-shrink-0">
                    <span class="text-amber-500 font-bold text-sm">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'U' }}</span>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ auth()->check() ? auth()->user()->name : 'Usuario' }}</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ auth()->check() ? auth()->user()->role : 'Invitado' }}</p>
                </div>
                @if(auth()->check())
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-lg hover:bg-rose-500/20 text-slate-400 hover:text-rose-500 flex items-center justify-center transition-colors cursor-pointer" title="Cerrar Sesión">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col h-full relative z-10 min-w-0">
        
        <!-- Header -->
        <header class="glass-panel h-16 md:h-18 border-b flex items-center justify-between px-4 md:px-10 sticky top-0 z-30 flex-shrink-0" style="border-color:rgba(245,166,35,.1);">
            <div class="flex items-center gap-3">
                <!-- Hamburger Button (Mobile only) -->
                <button onclick="toggleSidebar()" class="p-2 -ml-2 text-slate-400 hover:text-white md:hidden focus:outline-none cursor-pointer" title="Abrir Menú">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-base md:text-xl font-bold text-white tracking-tight">
                        @if(auth()->check() && strtolower(auth()->user()->role) === 'barbero')
                            Espacio del Barbero
                        @else
                            Centro de Mando
                        @endif
                    </h1>
                    <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full" style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">Online</span>
                    </div>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] uppercase tracking-wider font-bold" style="background:rgba(245,166,35,.08);border:1px solid rgba(245,166,35,.2);color:#f5a623;">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                        <span>BCV: Bs. {{ number_format($tasaBcv, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3 md:gap-4">

                {{-- ─── NOTIFICATION BELL ─── --}}
                @php
                    $notifCount = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;
                    $notifs = auth()->check() ? auth()->user()->unreadNotifications->take(5) : collect();
                @endphp
                <div class="relative" id="notif-wrapper">
                    <button onclick="toggleNotifDropdown()" class="relative text-slate-400 hover:text-white transition-colors p-2 rounded-xl hover:bg-white/5 cursor-pointer">
                        <i class="fa-regular fa-bell text-lg md:text-xl"></i>
                        @if($notifCount > 0)
                            <span class="absolute top-1 right-1 w-4 h-4 flex items-center justify-center rounded-full text-[9px] font-black text-black" style="background:#f5a623;">
                                {{ $notifCount > 9 ? '9+' : $notifCount }}
                            </span>
                        @else
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full" style="background:#22c55e;"></span>
                        @endif
                    </button>

                    <!-- Dropdown -->
                    <div id="notif-dropdown" class="absolute right-0 top-full mt-2 w-80 rounded-2xl shadow-2xl z-50 overflow-hidden" style="background:#111113;border:1px solid rgba(245,166,35,.15);">
                        <div class="px-4 py-3 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,.05);">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-bell" style="color:#f5a623;font-size:13px;"></i>
                                <span class="font-black text-white text-sm">Notificaciones</span>
                                @if($notifCount > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black text-black" style="background:#f5a623;">{{ $notifCount }}</span>
                                @endif
                            </div>
                            @if($notifCount > 0)
                                <form action="{{ route('notificaciones.markAll') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-bold cursor-pointer" style="color:#f5a623;">Marcar todo leído</button>
                                </form>
                            @endif
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @if($notifs->isEmpty())
                                <div class="py-8 text-center">
                                    <i class="fa-regular fa-bell-slash" style="font-size:1.5rem;color:#27272a;"></i>
                                    <p class="text-xs mt-2" style="color:#52525b;">Sin notificaciones nuevas</p>
                                </div>
                            @else
                                @foreach($notifs as $notif)
                                    <div class="px-4 py-3 hover:bg-white/3 transition-colors" style="border-bottom:1px solid rgba(255,255,255,.04);">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-black" style="background:rgba(245,166,35,.15);color:#f5a623;">
                                                ✂️
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-white leading-snug">{{ $notif->data['mensaje'] ?? 'Nueva cita agendada' }}</p>
                                                <p class="text-xs mt-0.5" style="color:#71717a;">
                                                    {{ $notif->data['servicio_nombre'] ?? '' }} &bull; {{ isset($notif->data['fecha_hora']) ? \Carbon\Carbon::parse($notif->data['fecha_hora'])->format('d/m H:i') : '' }}
                                                </p>
                                                <p class="text-[10px] mt-1" style="color:#52525b;">{{ $notif->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="px-4 py-2.5" style="border-top:1px solid rgba(255,255,255,.04);">
                            <a href="{{ route('cortes.index') }}" class="text-xs font-bold block text-center" style="color:#71717a;">Ver todas las citas →</a>
                        </div>
                    </div>
                </div>

                <div class="h-8 w-px" style="background:rgba(245,166,35,.15);"></div>
                @if(auth()->check() && strtolower(auth()->user()->role) === 'admin')
                    <a href="{{ route('barberia.configuracion') }}" class="w-8 h-8 rounded-lg hover:bg-slate-800/80 border border-slate-800 hover:border-slate-700/50 flex items-center justify-center text-slate-400 hover:text-white transition-all cursor-pointer" title="Configuraciones">
                        <i class="fa-solid fa-gears text-sm"></i>
                    </a>
                    <div class="h-8 w-px bg-slate-800/60"></div>
                @endif
                <div class="text-right">
                    <p class="text-xs md:text-sm font-bold text-white max-w-[90px] sm:max-w-none truncate">{{ auth()->check() ? (auth()->user()->barberia->nombre ?? 'Mi Barbería') : 'Sistema' }}</p>
                    <p class="text-[9px] md:text-xs text-slate-400 hidden sm:block" id="live-time">Cargando...</p>
                </div>
            </div>
        </header>

        <!-- Dynamic Content -->

        <main class="flex-1 overflow-y-auto p-3 md:p-10 pb-36 md:pb-10 relative">
            <!-- Decorative inner glow -->
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-950/50 pointer-events-none"></div>
            
            <div class="relative z-10 max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- BOTTOM NAVIGATION (Mobile only) -->
    @if(auth()->check())
    <nav class="fixed bottom-0 left-0 right-0 z-50 md:hidden glass-panel border-t border-slate-800/60 safe-bottom">
        <div class="flex items-stretch">
            @if(strtolower(auth()->user()->role) === 'admin')
                <a href="{{ route('dashboard') }}" class="bottom-nav-item flex-1 flex flex-col items-center py-3 gap-1 text-xs font-bold {{ request()->routeIs('dashboard') ? 'active text-amber-500' : 'text-slate-500' }}">
                    <i class="fa-solid fa-chart-pie text-lg"></i>
                    <span class="text-[9px]">Dashboard</span>
                </a>
                <a href="{{ route('cortes.index') }}" class="bottom-nav-item flex-1 flex flex-col items-center py-3 gap-1 text-xs font-bold {{ request()->routeIs('cortes.index') ? 'active text-amber-500' : 'text-slate-500' }}">
                    <i class="fa-solid fa-scissors text-lg"></i>
                    <span class="text-[9px]">Cortes</span>
                </a>
                <a href="{{ route('barberos.index') }}" class="bottom-nav-item flex-1 flex flex-col items-center py-3 gap-1 text-xs font-bold {{ request()->routeIs('barberos.index') ? 'active text-amber-500' : 'text-slate-500' }}">
                    <i class="fa-solid fa-user-tie text-lg"></i>
                    <span class="text-[9px]">Personal</span>
                </a>
                <a href="{{ route('finanzas.index') }}" class="bottom-nav-item flex-1 flex flex-col items-center py-3 gap-1 text-xs font-bold {{ request()->routeIs('finanzas.index') ? 'active text-amber-500' : 'text-slate-500' }}">
                    <i class="fa-solid fa-wallet text-lg"></i>
                    <span class="text-[9px]">Finanzas</span>
                </a>
                <a href="{{ route('fiados.index') }}" class="bottom-nav-item flex-1 flex flex-col items-center py-3 gap-1 text-xs font-bold {{ request()->routeIs('fiados.index') ? 'active text-amber-500' : 'text-slate-500' }} relative">
                    <i class="fa-solid fa-handshake text-lg"></i>
                    <span class="text-[9px]">Fiados</span>
                    @if(isset($fiadosCount) && $fiadosCount > 0)
                        <span class="absolute top-2 right-1/4 w-4 h-4 bg-rose-500 rounded-full text-white text-[8px] font-black flex items-center justify-center">{{ $fiadosCount }}</span>
                    @endif
                </a>
            @elseif(strtolower(auth()->user()->role) === 'barbero')
                <a href="{{ route('barbero.dashboard') }}" class="bottom-nav-item flex-1 flex flex-col items-center py-3 gap-1 text-xs font-bold {{ request()->routeIs('barbero.dashboard') ? 'active text-amber-500' : 'text-slate-500' }}">
                    <i class="fa-solid fa-chart-line text-lg"></i>
                    <span class="text-[9px]">Rendimiento</span>
                </a>
            @endif
        </div>
    </nav>
    @endif

    <script>
        // Live clock
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            const dateString = now.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'short' });
            const el = document.getElementById('live-time');
            if (el) el.textContent = dateString + ' • ' + timeString;
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Toggle Notification Dropdown
        function toggleNotifDropdown() {
            const dd = document.getElementById('notif-dropdown');
            if (dd) dd.classList.toggle('open');
        }
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notif-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                const dd = document.getElementById('notif-dropdown');
                if (dd) dd.classList.remove('open');
            }
        });

        // Toggle Sidebar on Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    backdrop.classList.add('opacity-100');
                }, 10);
            } else {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            }
        }

        // Close sidebar when a nav link is clicked on mobile
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    toggleSidebar();
                }
            });
        });
    </script>
</body>
</html>
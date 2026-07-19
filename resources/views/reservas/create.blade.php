<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cita — {{ $barberia->nombre }}</title>
    <meta name="description" content="Agenda tu cita en {{ $barberia->nombre }}. Elige tu barbero, servicio y horario favorito.">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #f5a623;
            --gold-light: #fbbf24;
            --gold-dark: #d4891a;
            --black: #09090b;
            --black-2: #111113;
            --black-3: #18181b;
        }
        * { font-family: 'Outfit', sans-serif; box-sizing: border-box; }
        body {
            background-color: var(--black);
            background-image:
                radial-gradient(ellipse 80% 50% at 20% -20%, rgba(245,166,35,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 60% at 80% 110%, rgba(245,166,35,0.08) 0%, transparent 60%);
            min-height: 100vh;
            color: #e4e4e7;
        }

        /* ─── Step Wizard ─── */
        .step-item { transition: all .4s cubic-bezier(.4,0,.2,1); }
        .step-connector { flex: 1; height: 2px; background: #27272a; transition: background .4s; }
        .step-connector.active { background: linear-gradient(90deg, var(--gold), transparent); }
        .step-connector.done { background: var(--gold); }
        .step-circle {
            width: 36px; height: 36px; border-radius: 50%;
            border: 2px solid #3f3f46;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 14px;
            transition: all .4s cubic-bezier(.4,0,.2,1);
            flex-shrink: 0;
        }
        .step-circle.active { border-color: var(--gold); color: var(--gold); box-shadow: 0 0 20px rgba(245,166,35,.35); }
        .step-circle.done { background: var(--gold); border-color: var(--gold); color: #000; }

        /* ─── Barber Card ─── */
        .barber-card {
            border: 2px solid #27272a;
            border-radius: 16px;
            padding: 16px;
            cursor: pointer;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            background: rgba(24,24,27,0.8);
            position: relative;
            overflow: hidden;
        }
        .barber-card::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(245,166,35,.05) 0%, transparent 100%);
            opacity: 0; transition: opacity .3s;
        }
        .barber-card:hover { border-color: rgba(245,166,35,.5); transform: translateY(-2px); box-shadow: 0 8px 32px rgba(245,166,35,.12); }
        .barber-card:hover::before { opacity: 1; }
        .barber-card.selected { border-color: var(--gold); background: rgba(245,166,35,.06); box-shadow: 0 0 0 1px rgba(245,166,35,.3), 0 8px 32px rgba(245,166,35,.15); }
        .barber-card.selected::before { opacity: 1; }
        .barber-card .check-badge { opacity: 0; transform: scale(0); transition: all .3s cubic-bezier(.34,1.56,.64,1); }
        .barber-card.selected .check-badge { opacity: 1; transform: scale(1); }

        /* ─── Service Card ─── */
        .service-card {
            border: 2px solid #27272a;
            border-radius: 14px;
            padding: 14px;
            cursor: pointer;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            background: rgba(24,24,27,0.8);
        }
        .service-card:hover { border-color: rgba(245,166,35,.4); }
        .service-card.selected { border-color: var(--gold); background: rgba(245,166,35,.06); box-shadow: 0 0 0 1px rgba(245,166,35,.25); }

        /* ─── Input ─── */
        .gold-input {
            width: 100%;
            background: rgba(9,9,11,.9);
            border: 2px solid #27272a;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px; font-weight: 500;
            color: #fff;
            outline: none;
            transition: border-color .3s, box-shadow .3s;
        }
        .gold-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(245,166,35,.15); }
        .gold-input::placeholder { color: #52525b; }
        .gold-input-wrap { position: relative; }
        .gold-input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #52525b; font-size: 13px; pointer-events: none; }
        .gold-input-wrap .gold-input { padding-left: 40px; }

        /* ─── Buttons ─── */
        .btn-gold {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #000; font-weight: 900;
            padding: 14px 28px; border-radius: 12px;
            border: none; cursor: pointer;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 4px 24px rgba(245,166,35,.3);
            font-size: 15px;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(245,166,35,.4); filter: brightness(1.1); }
        .btn-ghost {
            background: transparent;
            color: #71717a; font-weight: 700;
            padding: 14px 28px; border-radius: 12px;
            border: 2px solid #27272a; cursor: pointer;
            transition: all .3s;
            font-size: 15px;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-ghost:hover { border-color: #52525b; color: #a1a1aa; }

        /* ─── Panel ─── */
        .glass-panel {
            background: rgba(18,18,20,.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,.05);
            border-radius: 24px;
        }
        .gold-divider { height: 1px; background: linear-gradient(90deg, transparent, rgba(245,166,35,.3), transparent); margin: 20px 0; }

        /* ─── Animations ─── */
        @keyframes fadeSlideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(.9); } to { opacity: 1; transform: scale(1); } }
        .fade-slide-up { animation: fadeSlideUp .5s cubic-bezier(.4,0,.2,1) forwards; }
        .scale-in { animation: scaleIn .4s cubic-bezier(.34,1.56,.64,1) forwards; }

        /* ─── Avatar ─── */
        .avatar {
            width: 56px; height: 56px; border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; font-weight: 900; color: #000;
            flex-shrink: 0; box-shadow: 0 4px 16px rgba(245,166,35,.25);
        }
        .avatar.sm { width: 40px; height: 40px; font-size: 16px; }

        /* ─── Success Screen ─── */
        @keyframes checkDraw {
            0% { stroke-dashoffset: 100; } 100% { stroke-dashoffset: 0; }
        }
        .check-path { stroke-dasharray: 100; stroke-dashoffset: 100; animation: checkDraw .8s .3s cubic-bezier(.4,0,.2,1) forwards; }
        @keyframes ringPulse {
            0%, 100% { transform: scale(1); opacity: .7; }
            50% { transform: scale(1.15); opacity: .3; }
        }
        .ring-pulse { animation: ringPulse 2s infinite; }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 10px; }

        /* Step panel transition */
        .step-panel { display: none; }
        .step-panel.active { display: block; animation: fadeSlideUp .45s cubic-bezier(.4,0,.2,1); }
    </style>
</head>
<body>

    <!-- ─── TOP BAR ─── -->
    <div style="background: rgba(9,9,11,.95); border-bottom: 1px solid rgba(255,255,255,.05); backdrop-filter: blur(20px);" class="sticky top-0 z-50">
        <div class="max-w-2xl mx-auto px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($barberia->logo)
                    <img src="{{ $barberia->logo }}" alt="Logo" style="width:36px;height:36px;object-fit:contain;border-radius:10px;border:1px solid rgba(255,255,255,.08);">
                @else
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#f5a623,#d4891a);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-scissors" style="color:#000;font-size:15px;"></i>
                    </div>
                @endif
                <div>
                    <p class="font-black text-white text-sm leading-none">{{ $barberia->nombre }}</p>
                    <p style="font-size:10px;color:#71717a;font-weight:600;letter-spacing:.08em;text-transform:uppercase;" class="mt-0.5">Reserva tu cita</p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:6px;padding:4px 10px;background:rgba(245,166,35,.1);border:1px solid rgba(245,166,35,.2);border-radius:999px;">
                <span style="width:6px;height:6px;background:#22c55e;border-radius:50%;display:inline-block;animation:pulse 2s infinite;"></span>
                <span style="font-size:10px;font-weight:700;color:#f5a623;text-transform:uppercase;letter-spacing:.08em;">Disponible</span>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 py-8 pb-16">

        {{-- ─── SUCCESS MESSAGE ─── --}}
        @if(session('success'))
        <div class="scale-in mb-6 p-5 rounded-2xl text-center" style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.25);">
            <div class="mb-4 relative inline-block">
                <div class="ring-pulse" style="width:80px;height:80px;border:2px solid rgba(34,197,94,.3);border-radius:50%;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);"></div>
                <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="36" cy="36" r="34" stroke="#22c55e" stroke-width="2" fill="rgba(34,197,94,.1)"/>
                    <path class="check-path" d="M22 36l10 10 18-20" stroke="#22c55e" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>
            <h2 class="text-xl font-black text-white mt-2">¡Cita Confirmada!</h2>
            <p class="text-sm text-slate-400 mt-1">{{ session('success') }}</p>
            <p class="text-xs mt-3" style="color:#52525b;">Te esperamos en <strong style="color:#f5a623;">{{ $barberia->nombre }}</strong></p>
        </div>
        @endif

        {{-- ─── ERRORS ─── --}}
        @if($errors->any())
        <div class="mb-5 p-4 rounded-2xl" style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);">
            <div class="flex items-center gap-2 mb-2">
                <i class="fa-solid fa-triangle-exclamation" style="color:#f87171;font-size:14px;"></i>
                <span class="font-bold text-red-400 text-sm">Por favor revisa los siguientes campos:</span>
            </div>
            <ul class="space-y-1 pl-5 list-disc">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-red-400">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ─── WIZARD STEPS INDICATOR ─── --}}
        <div class="flex items-center gap-0 mb-8 px-2">
            <div class="flex flex-col items-center gap-1.5">
                <div class="step-circle done" id="circle-1">
                    <i class="fa-solid fa-check" style="font-size:13px;"></i>
                </div>
                <span style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#f5a623;">Barbero</span>
            </div>
            <div class="step-connector done" id="connector-1"></div>
            <div class="flex flex-col items-center gap-1.5">
                <div class="step-circle" id="circle-2" style="color:#52525b;">2</div>
                <span style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#52525b;" id="label-2">Servicio</span>
            </div>
            <div class="step-connector" id="connector-2"></div>
            <div class="flex flex-col items-center gap-1.5">
                <div class="step-circle" id="circle-3" style="color:#52525b;">3</div>
                <span style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#52525b;" id="label-3">Confirmar</span>
            </div>
        </div>

        <form action="{{ route('reservas.public.store', $barberia->slug) }}" method="POST" id="booking-form">
            @csrf

            {{-- ════════════════════════════════════
                 PASO 1: ELIGE TU BARBERO
            ════════════════════════════════════ --}}
            <div class="step-panel active" id="step-1">
                <div class="glass-panel p-6 md:p-8">
                    <div class="mb-6">
                        <p style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#f5a623;" class="mb-1">Paso 1 de 3</p>
                        <h2 class="text-2xl font-black text-white">¿Quién te va a atender?</h2>
                        <p style="color:#71717a;font-size:14px;" class="mt-1">Selecciona tu barbero de confianza</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="barber-grid">
                        @foreach($barberos as $barbero)
                        <div class="barber-card" onclick="selectBarber({{ $barbero->id }}, '{{ $barbero->name }}')" id="barber-card-{{ $barbero->id }}">
                            <!-- Check Badge -->
                            <div class="check-badge absolute top-3 right-3 w-6 h-6 rounded-full flex items-center justify-center" style="background:var(--gold);">
                                <i class="fa-solid fa-check" style="font-size:10px;color:#000;"></i>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="avatar">{{ strtoupper(substr($barbero->name, 0, 1)) }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-black text-white text-base leading-tight">{{ $barbero->name }}</p>
                                    <p style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-top:4px;"
                                       class="{{ $barbero->role === 'admin' ? 'text-amber-400' : 'text-blue-400' }}">
                                        {{ $barbero->role === 'admin' ? '✦ Senior / Dueño' : 'Barbero Profesional' }}
                                    </p>
                                    <div class="flex items-center gap-1 mt-2">
                                        <?php for($s = 0; $s < 5; $s++): ?><i class="fa-solid fa-star" style="font-size:10px;color:#f5a623;"></i><?php endfor; ?>
                                        <span style="font-size:10px;color:#71717a;margin-left:4px;">Top barber</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if($barberos->isEmpty())
                        <div class="col-span-2 py-10 text-center">
                            <i class="fa-solid fa-user-slash" style="font-size:2rem;color:#27272a;"></i>
                            <p class="mt-3 text-sm" style="color:#52525b;">No hay barberos disponibles en este momento.</p>
                        </div>
                        @endif
                    </div>

                    <input type="hidden" name="barbero_id" id="barbero_id_input" value="{{ old('barbero_id') }}">

                    <div class="mt-6 flex justify-end">
                        <button type="button" class="btn-gold" onclick="goToStep(2)" id="btn-next-1" disabled style="opacity:.4;cursor:not-allowed;">
                            Siguiente <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════
                 PASO 2: SERVICIO Y FECHA
            ════════════════════════════════════ --}}
            <div class="step-panel" id="step-2">
                <div class="glass-panel p-6 md:p-8">
                    <div class="mb-6">
                        <p style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#f5a623;" class="mb-1">Paso 2 de 3</p>
                        <h2 class="text-2xl font-black text-white">¿Qué servicio deseas?</h2>
                        <p style="color:#71717a;font-size:14px;" class="mt-1">Escoge el servicio y agenda tu horario</p>
                    </div>

                    <!-- Barbero seleccionado (resumen) -->
                    <div class="flex items-center gap-3 p-3 rounded-xl mb-6" style="background:rgba(245,166,35,.08);border:1px solid rgba(245,166,35,.2);">
                        <div class="avatar sm" id="summary-barber-avatar-2">?</div>
                        <div>
                            <p style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#71717a;">Barbero seleccionado</p>
                            <p class="font-black text-white text-sm" id="summary-barber-name-2">—</p>
                        </div>
                        <button type="button" onclick="goToStep(1)" class="ml-auto text-xs font-bold" style="color:#f5a623;">Cambiar</button>
                    </div>

                    <!-- Servicios -->
                    <div class="space-y-3 mb-6">
                        <p style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#71717a;" class="mb-3">Servicios disponibles</p>
                        @foreach($servicios as $servicio)
                        <div class="service-card" onclick="selectService({{ $servicio->id }}, '{{ $servicio->nombre }}', {{ $servicio->precio }}, {{ $servicio->duracion ?? 30 }})" id="service-card-{{ $servicio->id }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div style="width:38px;height:38px;border-radius:10px;background:rgba(245,166,35,.1);border:1px solid rgba(245,166,35,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="fa-solid fa-scissors" style="color:#f5a623;font-size:14px;"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white text-sm">{{ $servicio->nombre }}</p>
                                        @if($servicio->descripcion)
                                        <p style="font-size:11px;color:#71717a;" class="mt-0.5">{{ $servicio->descripcion }}</p>
                                        @endif
                                        <div class="flex items-center gap-3 mt-1">
                                            <span style="font-size:10px;color:#52525b;font-weight:600;">
                                                <i class="fa-regular fa-clock" style="font-size:9px;"></i> ~{{ $servicio->duracion ?? 30 }} min
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 ml-3">
                                    <p class="font-black text-lg" style="color:#f5a623;">${{ number_format($servicio->precio, 2) }}</p>
                                    <p style="font-size:10px;color:#52525b;font-weight:600;">USD</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <input type="hidden" name="servicio_id" id="servicio_id_input" value="{{ old('servicio_id') }}">

                    <div class="gold-divider"></div>

                    <!-- Fecha y Hora -->
                    <p style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#71717a;" class="mb-3">Fecha y hora de la cita</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-2">Fecha <span style="color:#ef4444;">*</span></label>
                            <div class="gold-input-wrap">
                                <i class="fa-regular fa-calendar"></i>
                                <input type="date" name="fecha_hora_date" id="fecha_input"
                                       min="{{ now()->addHour()->format('Y-m-d') }}"
                                       value="{{ old('fecha_hora_date', now()->addDay()->format('Y-m-d')) }}"
                                       class="gold-input" onchange="cargarHorariosDisponibles()" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-2">Hora <span style="color:#ef4444;">*</span></label>
                            <div class="gold-input-wrap">
                                <i class="fa-regular fa-clock"></i>
                                <select name="fecha_hora_time" id="hora_input" class="gold-input" style="appearance:none;cursor:pointer;" required>
                                    <option value="">— Elige hora —</option>
                                    <?php
                                    $horarios = [];
                                    for ($h = 8; $h <= 19; $h++) {
                                        foreach (['00','30'] as $m) {
                                            $val = sprintf('%02d:%s', $h, $m);
                                            $label = $val . ' ' . ($h < 12 ? 'AM' : 'PM');
                                            $sel = old('fecha_hora_time') == $val ? 'selected' : '';
                                            echo "<option value=\"$val\" $sel>$label</option>\n";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Hidden combined field -->
                    <input type="hidden" name="fecha_hora" id="fecha_hora_combined">

                    <div class="mt-6 flex justify-between gap-3">
                        <button type="button" class="btn-ghost" onclick="goToStep(1)">
                            <i class="fa-solid fa-arrow-left"></i> Atrás
                        </button>
                        <button type="button" class="btn-gold" onclick="goToStep(3)" id="btn-next-2" disabled style="opacity:.4;cursor:not-allowed;">
                            Siguiente <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════
                 PASO 3: TUS DATOS + CONFIRMAR
            ════════════════════════════════════ --}}
            <div class="step-panel" id="step-3">
                <div class="glass-panel p-6 md:p-8">
                    <div class="mb-6">
                        <p style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#f5a623;" class="mb-1">Paso 3 de 3</p>
                        <h2 class="text-2xl font-black text-white">Confirma tu cita</h2>
                        <p style="color:#71717a;font-size:14px;" class="mt-1">Ingresa tus datos de contacto</p>
                    </div>

                    <!-- Resumen de selección -->
                    <div class="rounded-2xl p-4 mb-6 space-y-3" style="background:rgba(245,166,35,.06);border:1px solid rgba(245,166,35,.2);">
                        <p style="font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#f5a623;" class="mb-3">📋 Resumen de tu cita</p>
                        <div class="flex justify-between text-sm">
                            <span style="color:#71717a;">Barbero:</span>
                            <span class="font-bold text-white" id="summary-barber-3">—</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span style="color:#71717a;">Servicio:</span>
                            <span class="font-bold text-white" id="summary-service-3">—</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span style="color:#71717a;">Duración:</span>
                            <span class="font-bold text-white" id="summary-duration-3">—</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span style="color:#71717a;">Fecha y hora:</span>
                            <span class="font-bold text-white" id="summary-datetime-3">—</span>
                        </div>
                        <div style="height:1px;background:rgba(245,166,35,.2);"></div>
                        <div class="flex justify-between items-center">
                            <span style="color:#71717a;font-size:14px;">Total:</span>
                            <span class="font-black text-xl" style="color:#f5a623;" id="summary-price-3">—</span>
                        </div>
                    </div>

                    <!-- Datos del cliente -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">Tu Nombre Completo <span style="color:#ef4444;">*</span></label>
                            <div class="gold-input-wrap">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" name="cliente_nombre" required
                                       placeholder="Ej: Juan Pérez"
                                       value="{{ old('cliente_nombre') }}"
                                       class="gold-input">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">Teléfono</label>
                                <div class="gold-input-wrap">
                                    <i class="fa-solid fa-phone"></i>
                                    <input type="tel" name="cliente_telefono"
                                           placeholder="+58 412 0000000"
                                           value="{{ old('cliente_telefono') }}"
                                           class="gold-input">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">Email</label>
                                <div class="gold-input-wrap">
                                    <i class="fa-regular fa-envelope"></i>
                                    <input type="email" name="cliente_email"
                                           placeholder="tu@correo.com"
                                           value="{{ old('cliente_email') }}"
                                           class="gold-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aviso -->
                    <div class="mt-5 flex items-start gap-3 p-3 rounded-xl" style="background:rgba(245,166,35,.05);border:1px solid rgba(245,166,35,.15);">
                        <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0" style="color:#f5a623;font-size:13px;"></i>
                        <p style="font-size:12px;color:#71717a;line-height:1.5;">El pago se realiza directamente en la barbería. Tu barbero recibirá una notificación de tu cita de inmediato.</p>
                    </div>

                    <div class="mt-6 flex justify-between gap-3">
                        <button type="button" class="btn-ghost" onclick="goToStep(2)">
                            <i class="fa-solid fa-arrow-left"></i> Atrás
                        </button>
                        <button type="submit" class="btn-gold flex-1 justify-center">
                            <i class="fa-solid fa-calendar-check"></i> Confirmar Cita
                        </button>
                    </div>
                </div>
            </div>

        </form><!-- end form -->

        <!-- Footer -->
        <div class="text-center mt-8">
            <p style="font-size:11px;color:#3f3f46;font-weight:600;">
                Powered by <span style="color:#f5a623;">BarberERP</span> Premium &nbsp;•&nbsp; ✂️
            </p>
        </div>
    </div>

    <script>
        // ─── State ───
        let currentStep = 1;
        let selectedBarberId = null;
        let selectedBarberName = '';
        let selectedServiceId = null;
        let selectedServiceName = '';
        let selectedServicePrice = 0;
        let selectedServiceDuration = 0;

        // ─── Step navigation ───
        function goToStep(step) {
            // Validate before advancing
            if (step === 2 && !selectedBarberId) {
                showValidationError('Por favor selecciona un barbero.');
                return;
            }
            if (step === 3) {
                if (!selectedServiceId) { showValidationError('Por favor selecciona un servicio.'); return; }
                const fecha = document.getElementById('fecha_input').value;
                const hora  = document.getElementById('hora_input').value;
                if (!fecha || !hora) { showValidationError('Por favor selecciona la fecha y hora.'); return; }
                // Combine date+time
                document.getElementById('fecha_hora_combined').value = fecha + ' ' + hora + ':00';
                // Update summary
                updateStep3Summary(fecha, hora);
            }

            document.getElementById('step-' + currentStep).classList.remove('active');
            currentStep = step;
            document.getElementById('step-' + currentStep).classList.add('active');
            updateStepIndicator(step);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateStepIndicator(step) {
            for (let i = 1; i <= 3; i++) {
                const circle = document.getElementById('circle-' + i);
                const label  = document.getElementById('label-' + i);

                if (i < step) {
                    // Done
                    circle.className = 'step-circle done';
                    circle.innerHTML = '<i class="fa-solid fa-check" style="font-size:13px;"></i>';
                    if (label) label.style.color = '#f5a623';
                } else if (i === step) {
                    // Active
                    circle.className = 'step-circle active';
                    circle.innerHTML = i;
                    if (label) label.style.color = '#f5a623';
                } else {
                    // Pending
                    circle.className = 'step-circle';
                    circle.style.color = '#52525b';
                    circle.innerHTML = i;
                    if (label) label.style.color = '#52525b';
                }
            }
            // Connectors
            for (let i = 1; i <= 2; i++) {
                const conn = document.getElementById('connector-' + i);
                if (i < step) conn.className = 'step-connector done';
                else if (i === step) conn.className = 'step-connector active';
                else conn.className = 'step-connector';
            }
        }

        // ─── Barber selection ───
        function selectBarber(id, name) {
            selectedBarberId = id;
            selectedBarberName = name;
            document.getElementById('barbero_id_input').value = id;

            document.querySelectorAll('.barber-card').forEach(c => c.classList.remove('selected'));
            document.getElementById('barber-card-' + id).classList.add('selected');

            const btn = document.getElementById('btn-next-1');
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';

            // Update step 2 summary
            const av2 = document.getElementById('summary-barber-avatar-2');
            const nm2 = document.getElementById('summary-barber-name-2');
            if (av2) av2.textContent = name.charAt(0).toUpperCase();
            if (nm2) nm2.textContent = name;

            // Fetch and block busy hours
            cargarHorariosDisponibles();
        }

        // ─── Service selection ───
        function selectService(id, name, price, duration) {
            selectedServiceId = id;
            selectedServiceName = name;
            selectedServicePrice = price;
            selectedServiceDuration = duration;
            document.getElementById('servicio_id_input').value = id;

            document.querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
            document.getElementById('service-card-' + id).classList.add('selected');

            const btn = document.getElementById('btn-next-2');
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        }

        // ─── Step 3 summary ───
        function updateStep3Summary(fecha, hora) {
            document.getElementById('summary-barber-3').textContent  = selectedBarberName;
            document.getElementById('summary-service-3').textContent = selectedServiceName;
            document.getElementById('summary-duration-3').textContent = '~' + selectedServiceDuration + ' min';
            document.getElementById('summary-price-3').textContent   = '$' + parseFloat(selectedServicePrice).toFixed(2) + ' USD';

            // Format date nicely
            const d = new Date(fecha + 'T' + hora);
            const formatted = d.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' }) + ' a las ' + hora;
            document.getElementById('summary-datetime-3').textContent = formatted;
        }

        // ─── Validation toast ───
        function showValidationError(msg) {
            let toast = document.getElementById('toast-error');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'toast-error';
                toast.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:#18181b;border:1px solid rgba(239,68,68,.5);padding:12px 20px;border-radius:12px;display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:#f87171;z-index:9999;opacity:0;transition:all .3s;white-space:nowrap;max-width:90vw;';
                document.body.appendChild(toast);
            }
            toast.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>' + msg;
            setTimeout(() => { toast.style.opacity = '1'; toast.style.transform = 'translateX(-50%) translateY(0)'; }, 10);
            setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(-50%) translateY(20px)'; }, 3000);
        }

        // ─── Fetch busy hours dynamically ───
        async function cargarHorariosDisponibles() {
            const barberId = selectedBarberId;
            const fecha = document.getElementById('fecha_input').value;
            const selectHora = document.getElementById('hora_input');

            if (!barberId || !fecha) {
                return;
            }

            const currentSelectedVal = selectHora.value;
            selectHora.disabled = true;
            selectHora.options[0].text = 'Buscando horarios...';

            try {
                const response = await fetch(`/api/barberos/${barberId}/ocupados?fecha=${fecha}`);
                const horasOcupadas = await response.json();

                for (let i = 1; i < selectHora.options.length; i++) {
                    const option = selectHora.options[i];
                    const val = option.value;

                    if (horasOcupadas.includes(val)) {
                        option.disabled = true;
                        option.text = val + ' ' + (parseInt(val.split(':')[0]) < 12 ? 'AM' : 'PM') + ' (Ocupado)';
                        option.style.color = '#ef4444';
                        option.style.textDecoration = 'line-through';
                    } else {
                        option.disabled = false;
                        option.text = val + ' ' + (parseInt(val.split(':')[0]) < 12 ? 'AM' : 'PM');
                        option.style.color = '#fff';
                        option.style.textDecoration = 'none';
                    }
                }

                if (horasOcupadas.includes(currentSelectedVal)) {
                    selectHora.value = '';
                } else {
                    selectHora.value = currentSelectedVal;
                }

            } catch (error) {
                console.error('Error al cargar horarios:', error);
            } finally {
                selectHora.disabled = false;
                selectHora.options[0].text = '— Elige hora —';
            }
        }

        // ─── Restore state if old() values exist ───
        window.addEventListener('DOMContentLoaded', () => {
            updateStepIndicator(1);

            @if(old('barbero_id'))
                selectBarber({{ old('barbero_id') }}, document.getElementById('barber-card-{{ old('barbero_id') }}')?.querySelector('p')?.textContent ?? 'Barbero');
            @endif
        });
    </script>
</body>
</html>

<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarberiaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\CorteController;
use App\Http\Controllers\CierreController;
use App\Http\Controllers\ReservaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Barbería ERP
|--------------------------------------------------------------------------
*/

// ==========================================
// RUTAS DE AUTENTICACIÓN
// ==========================================
// ==========================================
// RUTAS DE AUTENTICACIÓN
// ==========================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Redirección por defecto al entrar al home
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// RUTAS PROTEGIDAS
// ==========================================
Route::middleware(['auth'])->group(function () {

    // ------------------------------------------
    // GRUPO: ADMINISTRADOR (Dueño / Caja Global)
    // ------------------------------------------
    Route::middleware(['role:admin'])->group(function () {
        
        // Inicio / Resumen
        Route::get('/dashboard', [ReporteController::class, 'dashboardAdministrativo'])->name('dashboard');

        // Notificaciones
        Route::post('/notificaciones/mark-all', function () {
            auth()->user()->unreadNotifications->markAsRead();
            return redirect()->back()->with('success', 'Notificaciones marcadas como leídas.');
        })->name('notificaciones.markAll');

        // Cortes y Servicios
        Route::get('/cortes', [ReporteController::class, 'vistaCortes'])->name('cortes.index');
        Route::post('/corte', [CorteController::class, 'store'])->name('cortes.store');

        // Barberos / Personal
        Route::get('/barberos', [ReporteController::class, 'vistaBarberos'])->name('barberos.index');
        Route::post('/barberos', [ReporteController::class, 'guardarBarbero'])->name('barberos.store');
        Route::delete('/trabajador/{id}', [ReporteController::class, 'deleteTrabajador'])->name('barberos.destroy');

        // Gastos y Finanzas
        Route::get('/finanzas', [ReporteController::class, 'vistaFinanzas'])->name('finanzas.index');
        Route::post('/gasto', [ReporteController::class, 'storeGasto'])->name('gastos.store');
        Route::post('/semana/cerrar', [ReporteController::class, 'cerrarSemana'])->name('semana.cerrar');

        // Cierre de caja general
        Route::get('/cierre', [CierreController::class, 'index'])->name('cierre.index');
        Route::post('/cierre', [CierreController::class, 'store'])->name('cierre.store');

        // API y Fiados
        Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas.index');
        Route::get('/barberias', [BarberiaController::class, 'index'])->name('barberias.index');
        Route::get('/barberia/{slug}', [BarberiaController::class, 'show'])->name('barberias.show');
        Route::get('/citas', [CitaController::class, 'index'])->name('citas.index');
        Route::get('/fiados', [ReporteController::class, 'vistaFiados'])->name('fiados.index');
        Route::post('/fiados/pagar/{id}', [ReporteController::class, 'pagarFiado'])->name('fiados.pagar');

        // Configuraciones de la Barbería
        Route::get('/configuracion', [BarberiaController::class, 'editSettings'])->name('barberia.configuracion');
        Route::post('/configuracion', [BarberiaController::class, 'updateSettings'])->name('barberia.configuracion.update');
    });

    // ------------------------------------------
    // GRUPO: BARBERO (Acceso Restringido)
    // ------------------------------------------
    Route::middleware(['role:barbero'])->group(function () {
        Route::get('/barbero/dashboard', [ReporteController::class, 'dashboardBarbero'])->name('barbero.dashboard');
    });

    // ------------------------------------------
    // COMPLETAR RESERVA (ACCESIBLE POR AMBOS)
    // ------------------------------------------
    Route::post('/reservas/{id}/completar', [ReservaController::class, 'completar'])->name('reservas.completar');
});

// ==========================================
// RUTAS PÚBLICAS DE RESERVAS (CLIENTES)
// ==========================================
Route::get('/api/barberos/{id}/ocupados', [ReservaController::class, 'obtenerHorasOcupadas']);

Route::get('/{slug}/reservar', [ReservaController::class, 'createPublic'])->name('reservas.public.create');
Route::get('/{slug}/reserva', [ReservaController::class, 'createPublic']);
Route::post('/{slug}/reservar', [ReservaController::class, 'storePublic'])->name('reservas.public.store');
Route::post('/{slug}/reserva', [ReservaController::class, 'storePublic']);
Route::get('/reporte/descargar', [App\Http\Controllers\ReporteController::class, 'descargarReporte'])->name('reporte.descargar');
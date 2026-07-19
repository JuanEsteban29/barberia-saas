<?php

namespace App\Notifications;

use App\Models\Corte;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NuevaCitaNotification extends Notification
{
    use Queueable;

    public function __construct(public Corte $corte) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'corte_id'       => $this->corte->id,
            'cliente_nombre' => $this->corte->cliente->nombre ?? 'Cliente',
            'servicio_nombre'=> $this->corte->servicio->nombre ?? 'Servicio',
            'precio'         => $this->corte->precio,
            'fecha_hora'     => $this->corte->fecha_hora,
            'mensaje'        => '¡Nueva cita agendada por ' . ($this->corte->cliente->nombre ?? 'un cliente') . '!',
        ];
    }
}

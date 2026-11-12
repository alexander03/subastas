<?php

namespace App\Mail;

use App\Models\Inscripcion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Confirmación al participante + recordatorio de que el voucher es obligatorio.
 */
class InscripcionRecibidaMail extends Mailable
{
    use Queueable, SerializesModels, ConLogo;

    public $inscripcion;

    public function __construct(Inscripcion $inscripcion)
    {
        $this->inscripcion = $inscripcion;
    }

    public function build()
    {
        return $this->conLogo()
            ->subject('Inscripción recibida')
            ->markdown('emails.inscripcion-recibida');
    }
}

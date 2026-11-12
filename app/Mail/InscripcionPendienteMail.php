<?php

namespace App\Mail;

use App\Models\Inscripcion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Aviso al organizador: hay un participante pendiente de aprobación.
 */
class InscripcionPendienteMail extends Mailable
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
            ->subject('Participante pendiente de aprobación')
            ->markdown('emails.inscripcion-pendiente');
    }
}

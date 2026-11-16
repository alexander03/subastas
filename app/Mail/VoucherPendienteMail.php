<?php

namespace App\Mail;

use App\Models\Inscripcion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Recordatorio al participante: su inscripción no se puede aprobar hasta que suba el voucher de pago.
 */
class VoucherPendienteMail extends Mailable
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
            ->subject('Suba su voucher de pago')
            ->markdown('emails.voucher-pendiente');
    }
}

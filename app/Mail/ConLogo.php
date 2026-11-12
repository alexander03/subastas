<?php

namespace App\Mail;

/**
 * Incrusta los logos (CID) en el correo. Las plantillas los referencian como
 * cid:logo-azul@subastaperu y cid:logo-claro@subastaperu, asi no dependen de
 * una URL publica ni de que el cliente cargue imagenes remotas.
 *
 * Uso: en build(), llamar a $this->conLogo() antes de markdown().
 */
trait ConLogo
{
    protected function conLogo()
    {
        return $this->withSwiftMessage(function ($message) {
            foreach (['azul', 'claro'] as $variante) {
                $imagen = \Swift_Image::fromPath(resource_path("mail/logo-{$variante}.png"));
                $imagen->setId("logo-{$variante}@subastaperu");
                $message->embed($imagen);
            }
        });
    }
}

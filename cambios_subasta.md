# Cambios solicitados: Subasta Perú (backend)

Estado: implementado y probado con tests automáticos (25 tests). Falta desplegar en `develop.garzasoft.com` y configurar el correo en ese servidor.

Documentación interactiva de la API (Scramble): `/docs/api`.

## Resumen

| # | Cambio solicitado | Estado | Dónde |
|---|---|---|---|
| 1 | Notificación de participantes pendientes de aprobación | Hecho | Correo a la empresa organizadora al inscribirse |
| 2 | Recordatorio para subir voucher de pago | Hecho | Voucher obligatorio + aviso en el correo al participante |
| 3 | Restringir acceso después de iniciada la subasta | Hecho | `GET /api/subasta/verificarAcceso` |
| 4 | No permitir inscripciones fuera del horario | Hecho | `POST /api/inscripcion` responde 422 |
| 5 | Apartado de instrucciones o manual de usuario | Hecho | `GET /api/instrucciones` y `/manual-usuario` |

## ¿La subasta cambia de estado cuando inicia?

Sí. Cada bien tiene `situacion`:

| Valor | Significado |
|---|---|
| `P` | Pendiente (aún no inicia) |
| `E` | En subasta |
| `F` | Finalizada |

Se ve en `obtenerProceso` dentro de `bienes[].situacion`.

**Importante:** el cambio no ocurre solo por la hora. Pasa de `P` a `E` cuando se llama a `POST /api/bien/empezarSubasta` (o a `empezarSubastaManual`), y de `E` a `F` al finalizar. Si nadie la inicia, el bien sigue en `P` aunque ya haya pasado la hora de inicio.

Por eso el front no debe depender solo de ese campo: debe usar `verificarAcceso`, que revisa el estado y también la hora.

## 1. Notificación de participantes pendientes

Al crear una inscripción se envían dos correos:

- **A la empresa organizadora** (`empresa.email` del proceso): "Participante pendiente de aprobación", con los datos del participante, el proceso, el bien y un aviso para revisar el voucher. Si la empresa no tiene email, se usa el remitente configurado (`MAIL_FROM_ADDRESS`).
- **Al participante**: "Inscripción recibida", con el estado pendiente y el recordatorio del voucher.

Un fallo al enviar correo no rompe la inscripción: queda un aviso en `storage/logs/laravel.log`.

## 2. Voucher de pago obligatorio

- `POST /api/inscripcion` sin voucher responde **422**: "Debe subir su voucher de pago. Sin este documento no se puede aprobar su inscripción."
- El voucher va en el campo `image`. Formatos: jpg, jpeg, png, webp o pdf, hasta 5 MB.
- `POST /api/actualizar-inscripcion/{id}` con `situacion=A` y sin voucher responde **422**: "No se puede aprobar la inscripción: el participante no ha subido su voucher de pago."
- El voucher es obligatorio para **enviar** la inscripción. Aprobarla es un paso aparte, que hace el organizador.

## 3. Restringir acceso después de iniciada la subasta

`GET /api/subasta/verificarAcceso?bien_id=125` (pública)

```json
{ "puede_ingresar": true, "motivo": null }
```

Devuelve `puede_ingresar: false` con su `motivo` cuando:

| Caso | Motivo |
|---|---|
| El bien está en `E` | La subasta ya inició. No se permite el ingreso de nuevos participantes. |
| El bien está en `F` | La subasta ya finalizó. |
| Ya pasó la hora de inicio (etapa tipo 3) | La hora de inicio de la subasta ya pasó. No se permite el ingreso. |

Errores: sin `bien_id` responde 422; con un bien inexistente responde 404.

## 4. Inscripciones solo dentro del horario

`POST /api/inscripcion` revisa la etapa de inscripción (tipo `1`) del proceso:

| Caso | Respuesta |
|---|---|
| Aún no inicia | 422: "El periodo de inscripciones aún no ha iniciado." |
| Ya cerró | 422: "El periodo de inscripciones ha finalizado." |
| Bien inexistente | 404: "El bien indicado no existe." |

Si el proceso no tiene etapa de inscripción, no se restringe.

## 5. Instrucciones / manual de usuario

- `GET /api/instrucciones` (pública): devuelve `manual_url` y 6 `pasos` (`titulo` y `detalle`).
- `GET /manual-usuario`: página web con el manual.

## Cómo inscribirse (referencia para el front)

`POST /api/inscripcion` (requiere token Bearer, `multipart/form-data`)

| Campo | Obligatorio | Nota |
|---|---|---|
| `bien_id` | Sí | Id del bien (no del proceso) |
| `usuario_id` | Sí | |
| `image` | Sí | Voucher: imagen o PDF |
| `etapa_id` | No | Si falta, se usa la etapa de inscripción del proceso |
| `fecha` | No | Si falta, la fecha de hoy |
| `monto`, `facturacion`, `comentario` | No | |

La inscripción siempre queda en `situacion = P` (pendiente). Aunque se envíe `A` o `R`, se ignora: solo el organizador aprueba.

## Para el front

- Llamar a `verificarAcceso` antes de entrar a la sala de subasta y mostrar el `motivo` si no puede ingresar.
- Mostrar el `message` de los errores 422 de la inscripción.
- Enviar el voucher como `image` (multipart).
- Mostrar las instrucciones desde `/api/instrucciones` o enlazar `manual_url`.

## Pendiente

- Desplegar en `develop.garzasoft.com`.
- Configurar el correo en ese servidor: `MAIL_FROM_ADDRESS` (hoy está en `null` y sin remitente no se envía nada), el SMTP real y `FRONT_URL`.
- Confirmar que las empresas tengan su email cargado (a él llega el aviso de pendientes).
- Confirmar que la etapa tipo `1` sea inscripción y la tipo `3` sea la subasta.
- El formulario de contacto (`ContactoController::store`) solo guarda en base de datos y no envía correo, aunque en el documento original figuraba como "Verificado".

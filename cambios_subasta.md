# Consultas y observaciones: respuesta punto por punto

Documentación interactiva de la API (Scramble): `/docs/api`.

## Resumen

| # | Consulta / observación | Respuesta | API |
|---|---|---|---|
| 1 | Tipo de usuario y accesos | Solucionado | Sin cambios |
| 2 | Notificación de participantes pendientes | Implementado (correo) | `POST /api/inscripcion` y **nueva** `POST /api/inscripcion/{id}/notificar` |
| 3 | Recordatorio del voucher de pago | Implementado | Sin API nueva |
| 4 | Restringir acceso tras iniciada la subasta | Implementado | **Nueva** `GET /api/subasta/verificarAcceso` |
| 5 | Listado de pujas | Solucionado | Sin cambios |
| 6 | Inscripciones fuera de horario | Implementado | Sin API nueva |
| 7 | Mostrar ganador al finalizar | Solucionado | Sin cambios |
| 8 | Instrucciones / manual de usuario | Implementado | **Nuevas** `GET /api/instrucciones` y `GET /manual-usuario` |
| 9 | Correo de destino de "Contacto" | Por confirmar | Sin cambios |

---

## 1. Tipo de usuario y accesos (Solucionado)

La cuenta registrada con `tipousuario_id: 2` puede inscribirse y participar en las etapas del proceso.

- API: `POST /api/usuario`. No hay cambios.
- El backend no asigna el tipo por defecto: lo envía el front en el registro.

## 2. Notificación de participantes pendientes de aprobación

No se creó una API de notificaciones. Se resolvió con correo, como propone el documento.

Al crear una inscripción se envían dos correos:

- **A la empresa organizadora** (`empresa.email` del proceso): "Participante pendiente de aprobación", con los datos del participante, el proceso, el bien y un aviso para revisar el voucher. Si la empresa no tiene email, se usa el remitente configurado (`MAIL_FROM_ADDRESS`).
- **Al participante**: "Inscripción recibida", con el estado pendiente y el recordatorio del voucher.

Un fallo al enviar correo no rompe la inscripción: queda un aviso en `storage/logs/laravel.log`.

### El envío al crear es opcional

`POST /api/inscripcion` acepta el campo `notificar`:

| Valor | Resultado |
|---|---|
| No se envía, o `true` | Se envían los 2 correos (comportamiento por defecto) |
| `false` | Se crea la inscripción y no se envía ningún correo |

Acepta `true`/`false` y `1`/`0`, también en multipart. Un valor inválido responde 422.

### API nueva: enviar o reenviar el aviso al organizador

`POST /api/inscripcion/{id}/notificar` (requiere token, sin body)

Envía solo el correo "Participante pendiente de aprobación" al organizador. No envía nada al participante. Máximo 10 envíos por minuto.

| Caso | Respuesta |
|---|---|
| Enviado | 200 `{"message": "Notificación enviada.", "destinatario": "..."}` |
| La inscripción no existe | 404 |
| La inscripción ya no está pendiente (aprobada o rechazada) | 422 |
| La empresa no tiene email y no hay remitente configurado | 422 |
| Falla el envío (SMTP) | 500 con mensaje |

### Correo de destino

El documento habla del "correo con el que se registró la cuenta": es el correo de la empresa del proceso. El aviso se envía al `email` de la empresa (`empresa.email`) asociada al proceso del bien. Por eso cada empresa debe tener su email cargado.

## 3. Recordatorio para subir voucher de pago

- `POST /api/inscripcion` sin voucher responde **422**: "Debe subir su voucher de pago. Sin este documento no se puede aprobar su inscripción."
- El voucher va en el campo `image`. Formatos: jpg, jpeg, png, webp o pdf, hasta 5 MB.
- `POST /api/actualizar-inscripcion/{id}` con `situacion=A` y sin voucher responde **422**: "No se puede aprobar la inscripción: el participante no ha subido su voucher de pago."
- El correo "Inscripción recibida" también le recuerda al participante que el voucher es obligatorio.
- El voucher es obligatorio para enviar la inscripción. Aprobarla es un paso aparte que hace el organizador.

## 4. Restringir acceso después de iniciada la subasta

### ¿La subasta cambia de estado cuando inicia?

Sí. Cada bien tiene `situacion`:

| Valor | Significado |
|---|---|
| `P` | Pendiente (aún no inicia) |
| `E` | En subasta |
| `F` | Finalizada |

Se ve en `obtenerProceso`, dentro de `bienes[].situacion`.

El cambio no ocurre solo por la hora. Pasa de `P` a `E` cuando se llama a `POST /api/bien/empezarSubasta` (o a `empezarSubastaManual`), y de `E` a `F` al finalizar. Si nadie la inicia, el bien sigue en `P` aunque ya haya pasado la hora de inicio. Por eso el front no debe depender solo de ese campo.

### API nueva

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

Sin `bien_id` responde 422. Con un bien inexistente responde 404.

## 5. Listado de pujas (Solucionado)

Ya se visualizan. API: `GET /api/bids?bien_id=`. No hay cambios.

## 6. No permitir inscripciones fuera del horario

`POST /api/inscripcion` revisa la etapa de inscripción (tipo `1`) del proceso:

| Caso | Respuesta |
|---|---|
| Aún no inicia | 422: "El periodo de inscripciones aún no ha iniciado." |
| Ya cerró | 422: "El periodo de inscripciones ha finalizado." |
| Bien inexistente | 404: "El bien indicado no existe." |

Si el proceso no tiene etapa de inscripción, no se restringe.

## 7. Mostrar ganador al finalizar (Solucionado)

Al finalizar, se muestra el ganador. No hay cambios.

- `POST /api/proceso/obtenerGanadorProceso` (body: `proceso_id`, `bien_id`).
- `GET /api/procesosListaGanadoresPDF`, para el PDF.

## 8. Instrucciones o manual de usuario

- `GET /api/instrucciones` (pública): devuelve `manual_url` y 6 `pasos` (`titulo` y `detalle`).
- `GET /manual-usuario`: página web con el manual.

## 9. Correo de destino de "Contacto"

`POST /api/contacto/guardar` solo guarda el mensaje en la tabla de contactos. El backend no envía ningún correo, y `atencionalcliente@garzasoft.com` no está configurado en el proyecto. Si ese envío funciona, lo hace otro componente (por ejemplo el front).

Si se quiere que el backend envíe el correo de contacto, es un cambio pequeño: hay que indicar el correo de destino.

---

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
| `notificar` | No | `false` para no enviar correos al crear |

La inscripción siempre queda en `situacion = P` (pendiente). Aunque se envíe `A` o `R`, se ignora: solo el organizador aprueba.

## Para el front

- Llamar a `verificarAcceso` antes de entrar a la sala de subasta y mostrar el `motivo` si no puede ingresar.
- Mostrar el `message` de los errores 422 de la inscripción.
- Enviar el voucher como `image` (multipart).
- Mostrar las instrucciones desde `/api/instrucciones` o enlazar `manual_url`.

## A verificar

- Las empresas existentes que no tengan email: ahora es obligatorio al crear o editar una empresa (`POST /api/empresa` y `PUT /api/empresa/{id}`), pero las que ya estaban creadas sin email hay que completarlas. A ese correo llega el aviso de pendientes.
## Etapas del proceso (revisado en la base de datos)

Cada proceso tiene 4 etapas (`tipo` 1 a 4), una de cada una, y ningún proceso carece de la tipo 1 ni de la tipo 3.

| Tipo | Uso en esta implementación | Evidencia |
|---|---|---|
| `1` | Inscripción | Es la etapa que abre primero y con la ventana más amplia; siempre empieza antes que las demás |
| `3` | Subasta | El sistema ya la usa como la etapa de subasta para ordenar y filtrar procesos (`procesosFiltroWeb`, `procesosUltimos` y sliders) |
| `2` y `4` | No se usan en estos cambios | |

La base no guarda un nombre por tipo, así que esta equivalencia se deduce del orden de las fechas y del uso que ya hace el sistema.

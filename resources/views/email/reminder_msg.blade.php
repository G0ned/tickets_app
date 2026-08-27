<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio — {{ $edition->event->name }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:40px 0;">
    <tr>
        <td>
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,0.10); max-width:600px;">

                <tr>
                    <td style="background:linear-gradient(135deg,#0f172a 0%,#134e4a 100%); padding:36px 48px; text-align:center;">
                        <p style="color:#5eead4; margin:0 0 4px; font-size:11px; letter-spacing:3px; text-transform:uppercase; font-weight:600;">Recordatorio</p>
                        <h1 style="color:#ffffff; margin:0; font-size:30px; font-weight:800; letter-spacing:-0.5px;">{{ $edition->event->name }}</h1>
                        <p style="color:#99f6e4; margin:10px 0 0; font-size:14px;">
                            {{ $daysBefore == 1 ? 'Queda 1 día' : "Quedan {$daysBefore} días" }} para el evento
                        </p>
                    </td>
                </tr>

                @if($edition->event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($edition->event->poster_path))
                <tr>
                    <td style="padding:0; line-height:0;">
                        <img src="{{ $message->embed(\Illuminate\Support\Facades\Storage::disk('public')->path($edition->event->poster_path)) }}"
                             alt="Póster de {{ $edition->event->name }}"
                             style="width:100%; max-height:260px; object-fit:cover; display:block;">
                    </td>
                </tr>
                @endif

                <tr>
                    <td style="padding:40px 48px 0;">
                        <p style="color:#0f172a; font-size:22px; font-weight:700; margin:0 0 8px;">
                            Hola, {{ $attendee->name }} {{ $attendee->surname }}
                        </p>
                        <p style="color:#64748b; font-size:15px; line-height:1.7; margin:0 0 32px;">
                            Te recordamos que estás inscrito/a en <strong>{{ $edition->event->name }}</strong>.
                            A continuación tienes los detalles del evento.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 48px;">
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">

                            <tr>
                                <td style="background:#0f172a; padding:14px 20px;">
                                    <p style="color:#5eead4; font-size:11px; letter-spacing:2px; text-transform:uppercase; font-weight:600; margin:0;">Detalles del evento</p>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:0 20px;">
                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td style="padding:16px 0; border-bottom:1px solid #f1f5f9; width:36%;">
                                                <span style="color:#94a3b8; font-size:11px; text-transform:uppercase; letter-spacing:1px; font-weight:600;">Evento</span>
                                            </td>
                                            <td style="padding:16px 0; border-bottom:1px solid #f1f5f9;">
                                                <span style="color:#0f172a; font-size:15px; font-weight:700;">{{ $edition->event->name }}</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:16px 0; border-bottom:1px solid #f1f5f9;">
                                                <span style="color:#94a3b8; font-size:11px; text-transform:uppercase; letter-spacing:1px; font-weight:600;">Fecha</span>
                                            </td>
                                            <td style="padding:16px 0; border-bottom:1px solid #f1f5f9;">
                                                <span style="color:#0f172a; font-size:15px; font-weight:600;">
                                                    {{ $edition->date->translatedFormat('d \d\e F \d\e Y') }}
                                                </span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:16px 0; border-bottom:1px solid #f1f5f9;">
                                                <span style="color:#94a3b8; font-size:11px; text-transform:uppercase; letter-spacing:1px; font-weight:600;">Hora</span>
                                            </td>
                                            <td style="padding:16px 0; border-bottom:1px solid #f1f5f9;">
                                                <span style="color:#0f172a; font-size:15px; font-weight:600;">{{ $edition->date->format('H:i') }} h</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:16px 0;">
                                                <span style="color:#94a3b8; font-size:11px; text-transform:uppercase; letter-spacing:1px; font-weight:600;">Lugar</span>
                                            </td>
                                            <td style="padding:16px 0;">
                                                <span style="color:#0f172a; font-size:15px; font-weight:600;">{{ $edition->location }}</span>
                                            </td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:32px 48px 0;">
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="background:linear-gradient(135deg,#0f172a 0%,#134e4a 100%); border-radius:12px;">
                            <tr>
                                <td style="padding:32px; text-align:center;">
                                    <p style="color:#5eead4; font-size:11px; text-transform:uppercase; letter-spacing:3px; font-weight:600; margin:0 0 20px;">Código de acceso</p>
                                    <img src="{{ $message->embed(\Illuminate\Support\Facades\Storage::disk('public')->path('tickets/' . $token . '.png')) }}"
                                         alt="Código QR de acceso"
                                         style="width:180px; height:180px; display:inline-block; background:#ffffff; padding:14px; border-radius:10px;">
                                    <p style="color:#94a3b8; font-size:12px; margin:20px 0 0; line-height:1.6;">
                                        Presenta este código en la entrada.<br>Es personal e intransferible.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 48px 32px;">
                        <p style="color:#94a3b8; font-size:12px; line-height:1.7; margin:32px 0 0;">
                            Recibes este recordatorio porque autorizaste el envío de comunicaciones al inscribirte.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 48px; text-align:center;">
                        <p style="color:#cbd5e1; font-size:12px; margin:0;">
                            © {{ date('Y') }} eventia &nbsp;·&nbsp; Todos los derechos reservados
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>

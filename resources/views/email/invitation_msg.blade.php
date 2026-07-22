<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitación — {{ $edition->event->name }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:40px 0;">
    <tr>
        <td>
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,0.10); max-width:600px;">

                <tr>
                    <td style="background:linear-gradient(135deg,#0f172a 0%,#134e4a 100%); padding:36px 48px; text-align:center;">
                        <p style="color:#5eead4; margin:0 0 4px; font-size:11px; letter-spacing:3px; text-transform:uppercase; font-weight:600;">Estás invitado/a</p>
                        <h1 style="color:#ffffff; margin:0; font-size:30px; font-weight:800; letter-spacing:-0.5px;">eventia</h1>
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
                            Hola, {{ $person->name }} {{ $person->surname }}
                        </p>
                        <p style="color:#64748b; font-size:15px; line-height:1.7; margin:0 0 32px;">
                            Has sido invitado/a a
                            <strong>{{ $edition->event->name }}</strong>.
                            Usa el enlace de abajo para completar tu inscripción.
                            @if ($allowedRegistrations > 1)
                                Con este mismo enlace puedes inscribir hasta <strong>{{ $allowedRegistrations }}</strong> personas
                                (una por cada vez que se utilice).
                            @endif
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
                    <td style="padding:32px 48px; text-align:center;">
                        <a href="{{ route('invitation-registration-create', $token) }}"
                           style="display:inline-block; background:linear-gradient(135deg,#0f172a 0%,#134e4a 100%); color:#5eead4; text-decoration:none; font-weight:700; font-size:15px; padding:16px 32px; border-radius:10px;">
                            Completar inscripción
                        </a>
                        <p style="color:#94a3b8; font-size:12px; margin:16px 0 0; line-height:1.6;">
                            Este enlace solo es válido para esta edición del evento.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 48px 32px;">
                        <p style="color:#94a3b8; font-size:12px; line-height:1.7; margin:0;">
                            Si no esperabas esta invitación, puedes ignorar este correo con seguridad.
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

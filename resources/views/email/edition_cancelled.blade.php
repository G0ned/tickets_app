<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelación — {{ $edition->event->name }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:40px 0;">
    <tr>
        <td>
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,0.10); max-width:600px;">

                <tr>
                    <td style="background:linear-gradient(135deg,#0f172a 0%,#7f1d1d 100%); padding:36px 48px; text-align:center;">
                        <p style="color:#fca5a5; margin:0 0 4px; font-size:11px; letter-spacing:3px; text-transform:uppercase; font-weight:600;">Cancelación</p>
                        <h1 style="color:#ffffff; margin:0; font-size:28px; font-weight:800; letter-spacing:-0.5px;">{{ $edition->event->name }}</h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding:40px 48px 8px;">
                        <p style="color:#0f172a; font-size:22px; font-weight:700; margin:0 0 8px;">
                            Hola, {{ $attendee->name }} {{ $attendee->surname }}
                        </p>
                        <p style="color:#64748b; font-size:15px; line-height:1.7; margin:0;">
                            Lamentamos informarte de que la edición de <strong>{{ $edition->event->name }}</strong> a la que
                            estabas inscrito/a, prevista para el {{ $edition->date->translatedFormat('d \d\e F \d\e Y') }}
                            a las {{ $edition->date->format('H:i') }} h en {{ $edition->location }}, ha sido cancelada.
                            Tu inscripción y tu entrada ya no son válidas.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px 48px 32px;">
                        <p style="color:#94a3b8; font-size:12px; line-height:1.7; margin:0;">
                            Si tienes alguna duda, ponte en contacto con
                            <a href="mailto:ec@eurocos.es" style="color:#0f766e;">ec@eurocos.es</a>.
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

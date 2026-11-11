<!DOCTYPE html>
<html lang="es" style="height:100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de satisfacción — {{ $edition->event->name }}</title>
</head>
<body style="margin:0; padding:0; height:100%; background-color:#f1f5f9; font-family:'Segoe UI',Arial,sans-serif;">

<table role="presentation" width="100%" height="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; height:100%;">
    <tr>
        <td valign="middle" style="padding:40px 0;">
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,0.10); max-width:600px; margin:0 auto;">

                <tr>
                    <td style="background:linear-gradient(135deg,#0f172a 0%,#134e4a 100%); padding:36px 48px; text-align:center;">
                        <p style="color:#5eead4; margin:0 0 4px; font-size:11px; letter-spacing:3px; text-transform:uppercase; font-weight:600;">Encuesta de satisfacción</p>
                        <h1 style="color:#ffffff; margin:0; font-size:30px; font-weight:800; letter-spacing:-0.5px;">{{ $edition->event->name }}</h1>
                    </td>
                </tr>

                <tr>
                    <td style="padding:40px 48px 0;">
                        <p style="color:#0f172a; font-size:22px; font-weight:700; margin:0 0 8px;">
                            Hola, {{ $attendee->name }} {{ $attendee->surname }}
                        </p>
                        <p style="color:#64748b; font-size:15px; line-height:1.7; margin:0 0 32px;">
                            Gracias por acompañarnos en <strong>{{ $edition->event->name }}</strong>.
                            Tu opinión nos ayuda a mejorar; rellenar la encuesta solo te llevará un par de minutos.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 48px 8px; text-align:center;">
                        <a href="{{ $surveyUrl }}"
                           style="display:inline-block; background:#0d9488; color:#ffffff; text-decoration:none; font-size:15px; font-weight:700; padding:14px 32px; border-radius:10px;">
                            Responder la encuesta
                        </a>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 48px 32px;">
                        <p style="color:#94a3b8; font-size:12px; line-height:1.7; margin:24px 0 0;">
                            Si el botón no funciona, copia este enlace en tu navegador:<br>
                            <a href="{{ $surveyUrl }}" style="color:#0d9488; word-break:break-all;">{{ $surveyUrl }}</a>
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

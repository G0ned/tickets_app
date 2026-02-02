<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tu Entrada</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background-color: #4a5568; color: #ffffff; padding: 20px; text-align: center; }
        .content { padding: 30px; line-height: 1.6; }
        .event-details { background-color: #edf2f7; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #718096; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #3182ce; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Confirmación de Registro!</h1>
        </div>
        
        <div class="content">
            <p>Hola, <strong>{{ $user->firstname }}</strong>,</p>
            <p>Gracis por registrate. Con este correo se confirma tu inscripción al evento:</p>
            
            <div class="event-details">
                <h2 style="margin-top: 0;">{{ $event->name }}</h2>
                <p><strong>📍 Ubicación:</strong> {{ $event->location }}</p>
                <p><strong>📅 Fecha:</strong> {{ $event->date }}</p>
            </div>

            <p>Hemos adjuntado tu <strong>Código QR</strong> a este correo. Deberás presentarlo en la entrada (puedes llevarlo impreso o en tu móvil).</p>
            
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
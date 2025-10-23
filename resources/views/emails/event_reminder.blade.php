<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rappel d'événement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            background: #f9f9f9;
            padding: 20px;
        }

        .event-details {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📅 Rappel d'Événement</h1>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $user->name }}</strong>,</p>

            <p>Nous vous rappelons que vous avez un événement dans <strong>{{ $daysUntilEvent }} jour(s)</strong> :</p>

            <div class="event-details">
                <h2>{{ $event->titre }}</h2>
                <p><strong>Description :</strong> {{ $event->description }}</p>
                <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($event->date_event)->format('d/m/Y') }}</p>
                <p><strong>Club :</strong> {{ $event->club->nom ?? 'Club' }}</p>
            </div>

            <p>Nous espérons vous y voir nombreux !</p>

            <p>Cordialement,<br>L'équipe de votre bibliothèque</p>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
        </div>
    </div>
</body>

</html>
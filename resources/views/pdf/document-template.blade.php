<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Document' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
        }

        .page {
            width: 100%;
            padding: 20px;
        }

        .document-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 30px 0 20px 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .content {
            margin-top: 20px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 9pt;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- En-tête officielle de l'école -->
        <div style="margin-bottom: 30px;">
            {!! $header !!}
        </div>

        <!-- Titre du document -->
        @if (isset($title))
            <div class="document-title">{{ $title }}</div>
        @endif

        <!-- Contenu du document -->
        <div class="content">
            {!! $content !!}
        </div>

        <!-- Footer -->
        <div class="footer">
            Document généré le {{ now()->format('d/m/Y à H:i') }}<br>
            {{ $ecole->libelle_ecole }}
        </div>
    </div>
</body>

</html>

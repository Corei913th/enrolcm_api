<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Répartition des candidats - {{ $concours->libelle_concours }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
        }

        .page {
            width: 100%;
            padding: 15px;
        }

        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 15px 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .section {
            margin-top: 12px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 6px;
            color: #003366;
            text-transform: uppercase;
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th {
            background-color: #003366;
            color: #fff;
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }

        td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 9pt;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }

        .muted {
            color: #444;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="page">
        @include('pdf.includes.header')

        <div class="title" style="margin-top: 30px;">Répartition des candidats</div>

        <div class="section">
            <div class="section-title">Informations</div>
            <p><strong>Concours:</strong> {{ $concours->libelle_concours }}</p>
            @if ($session)
                <p><strong>Session:</strong> {{ $session->libelle_session ?? '' }}</p>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Répartition par filière</div>
            @if ($filieres->isEmpty())
                <div style="padding: 12px;" class="muted">Aucune filière rattachée.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50%;">Filière</th>
                            <th style="width: 15%;">Places</th>
                            <th style="width: 15%;">Validées</th>
                            <th style="width: 20%;">Restantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filieres as $filiere)
                            <tr>
                                <td>{{ $filiere['libelle_filiere'] ?? '' }}</td>
                                <td class="text-center">{{ $filiere['nombre_places'] ?? '' }}</td>
                                <td class="text-center">{{ $filiere['candidatures_validees'] ?? 0 }}</td>
                                <td class="text-center">{{ $filiere['places_restantes'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Répartition par centre</div>
            @if (empty($candidaturesParCentre))
                <div style="padding: 12px;" class="muted">Aucun centre rattaché.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50%;">Centre</th>
                            <th style="width: 30%;">Ville</th>
                            <th style="width: 20%;">Candidats</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidaturesParCentre as $item)
                            <tr>
                                <td>{{ $item['centre'] ?? '' }}</td>
                                <td>{{ $item['ville'] ?? '' }}</td>
                                <td class="text-center">{{ $item['count'] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="footer">
            Document généré le {{ $dateGeneration }}
        </div>
    </div>
</body>

</html>

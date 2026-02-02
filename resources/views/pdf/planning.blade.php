<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Planning des Épreuves - {{ $concours->libelle_concours }}</title>
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
        }

        .concours-info {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #003366;
            color: #fff;
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }

        td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 9pt;
        }

        .col-date {
            width: 12%;
            text-align: center;
        }

        .col-heure {
            width: 10%;
            text-align: center;
        }

        .col-duree {
            width: 8%;
            text-align: center;
        }

        .col-epreuve {
            width: 25%;
        }

        .col-type {
            width: 12%;
        }

        .col-coef {
            width: 8%;
            text-align: center;
        }

        .col-centre {
            width: 20%;
        }

        .col-instructions {
            width: 5%;
            text-align: center;
        }

        .date-group {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 8px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }

        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- Header Officiel de l'École -->
        @include('pdf.includes.header')

        <!-- Titre -->
        <div class="title" style="margin-top: 30px;">Planning des Épreuves</div>

        <!-- Informations Concours -->
        <div class="concours-info">
            <strong>{{ $concours->libelle_concours }}</strong>
            @if ($session)
                <br>Session : {{ $session->libelle_session }}
            @endif
        </div>

        @if ($plannings->isEmpty())
            <div style="text-align: center; padding: 40px; color: #666;">
                Aucune épreuve planifiée pour ce concours.
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th class="col-date">Date</th>
                        <th class="col-heure">Heure Début</th>
                        <th class="col-heure">Heure Fin</th>
                        <th class="col-duree">Durée</th>
                        <th class="col-epreuve">Épreuve</th>
                        <th class="col-type">Type</th>
                        <th class="col-coef">Coef.</th>
                        <th class="col-centre">Centre</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($plannings as $planning)
                        <tr>
                            <td class="col-date">
                                {{ $planning->date_epreuve ? \Carbon\Carbon::parse($planning->date_epreuve)->format('d/m/Y') : '' }}
                            </td>
                            <td class="col-heure">{{ $planning->getHeureDebutFormatee() ?? '' }}</td>
                            <td class="col-heure">{{ $planning->getHeureFinFormatee() ?? '' }}</td>
                            <td class="col-duree">{{ $planning->getDureeEnMinutes() ?? '' }} min</td>
                            <td class="col-epreuve">{{ $planning->epreuve->intitule ?? '' }}</td>
                            <td class="col-type">{{ $planning->epreuve->type_epreuve ?? '' }}</td>
                            <td class="col-coef">{{ $planning->epreuve->coefficient ?? '' }}</td>
                            <td class="col-centre">{{ $planning->centre->libelle_centre ?? '' }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="8">
                            <strong>Total épreuves : {{ $plannings->count() }}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        <!-- Footer -->
        <div class="footer">
            Document généré le {{ $dateGeneration }}<br>
            Planning officiel des épreuves
        </div>
    </div>
</body>

</html>

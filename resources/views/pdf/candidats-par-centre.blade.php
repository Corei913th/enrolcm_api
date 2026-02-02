<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Candidats par Centre - {{ $concours->libelle_concours }}</title>
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

        .centre-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .centre-header {
            background-color: #003366;
            color: #fff;
            padding: 10px;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .centre-info {
            font-size: 10pt;
            margin-left: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }

        td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 9pt;
        }

        .col-num {
            width: 4%;
            text-align: center;
        }

        .col-code {
            width: 15%;
        }

        .col-nom-prenom {
            width: 30%;
        }

        .col-date-naissance {
            width: 15%;
            text-align: center;
        }

        .col-lieu-naissance {
            width: 22%;
        }

        .col-sexe {
            width: 8%;
            text-align: center;
        }

        .col-langue {
            width: 10%;
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- Header Officiel de l'École -->
        @include('pdf.includes.header')

        <!-- Titre -->
        <div class="title" style="margin-top: 30px;">Liste des Candidats par Centre d'Examen</div>

        <!-- Informations Concours -->
        <div class="concours-info">
            <strong>{{ $concours->libelle_concours }}</strong>
            @if ($session)
                <br>Session : {{ $session->libelle_session }}
            @endif
            @if ($dateExamen)
                <br>Date d'examen : {{ $dateExamen }}
            @endif
        </div>

        @php
            $totalGeneral = 0;
        @endphp

        @foreach ($candidaturesParCentre as $centreId => $data)
            @php
                $centre = $data['centre'];
                $candidatures = $data['candidatures'];
                $totalGeneral += $candidatures->count();
            @endphp

            <div class="centre-section">
                <div class="centre-header">
                    {{ $centre ? $centre->nom_centre : 'Sans centre assigné' }}
                    @if ($centre)
                        <span class="centre-info">- {{ $centre->ville_centre }}</span>
                    @endif
                </div>

                <table>
                    <thead>
                        <tr>
                            <th class="col-num">N°</th>
                            <th class="col-code">Code</th>
                            <th class="col-nom-prenom">Nom et Prénom</th>
                            <th class="col-date-naissance">Date Nais.</th>
                            <th class="col-lieu-naissance">Lieu Naissance</th>
                            <th class="col-sexe">Sexe</th>
                            <th class="col-langue">Langue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidatures as $index => $candidature)
                            @php
                                $candidat = $candidature->candidat;
                            @endphp
                            <tr>
                                <td class="col-num">{{ $index + 1 }}</td>
                                <td class="col-code">
                                    {{ $candidature->code_cand_def ?? ($candidature->code_cand_temp ?? '') }}</td>
                                <td class="col-nom-prenom">
                                    {{ strtoupper($candidat->nom_cand ?? '') }}
                                    {{ ucwords(strtolower($candidat->prenom_cand ?? '')) }}
                                </td>
                                <td class="col-date-naissance">
                                    {{ $candidat->date_naissance_cand ? \Carbon\Carbon::parse($candidat->date_naissance_cand)->format('d/m/Y') : '' }}
                                </td>
                                <td class="col-lieu-naissance">{{ $candidat->lieu_naissance_cand ?? '' }}</td>
                                <td class="col-sexe">
                                    @if($candidat->sexe_cand)
                                        {{ $candidat->sexe_cand->value ?? '' }}
                                    @endif
                                </td>
                                <td class="col-langue">
                                    @if($candidat->premiere_langue)
                                        {{ $candidat->premiere_langue->value ?? '' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="7">
                                <strong>Total candidats pour ce centre : {{ $candidatures->count() }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if (!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach

        <!-- Total général -->
        <div style="margin-top: 20px; padding: 10px; background-color: #f0f0f0; border: 2px solid #003366;">
            <strong style="font-size: 11pt;">TOTAL GÉNÉRAL : {{ $totalGeneral }} candidats</strong>
        </div>

        <!-- Footer -->
        <div class="footer">
            Document généré le {{ $dateGeneration }}<br>
            Liste officielle des candidats par centre d'examen
        </div>
    </div>
</body>

</html>

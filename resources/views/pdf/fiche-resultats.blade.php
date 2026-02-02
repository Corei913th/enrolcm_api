<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Résultats - {{ $concours->libelle_concours }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
        }

        .page {
            width: 100%;
            padding: 15px;
        }

        /* Titre du document */
        .document-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        /* Informations concours */
        .concours-info {
            text-align: center;
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .concours-info .concours-name {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 5px;
        }

        .concours-info .session-name {
            font-style: italic;
            color: #333;
        }

        /* Tableau des résultats */
        .resultats-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8.5pt;
        }

        .resultats-table th {
            background-color: #003366;
            color: #fff;
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #000;
        }

        .resultats-table td {
            padding: 6px 5px;
            border: 1px solid #666;
            vertical-align: middle;
        }

        .resultats-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .resultats-table tr:hover {
            background-color: #f0f0f0;
        }

        /* Colonnes spécifiques */
        .col-rang {
            width: 5%;
            text-align: center;
            font-weight: bold;
        }

        .col-numero {
            width: 10%;
            text-align: center;
        }

        .col-nom {
            width: 18%;
            font-weight: bold;
            text-transform: uppercase;
        }

        .col-prenom {
            width: 15%;
        }

        .col-sexe {
            width: 5%;
            text-align: center;
        }

        .col-date-naissance {
            width: 10%;
            text-align: center;
            font-size: 8pt;
        }

        .col-lieu-naissance {
            width: 15%;
            font-size: 8pt;
        }

        .col-moyenne {
            width: 7%;
            text-align: center;
            font-weight: bold;
        }

        .col-mention {
            width: 15%;
            text-align: center;
            font-weight: bold;
        }

        /* Mentions colorées */
        .mention {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 8pt;
        }

        .mention-excellent {
            background-color: #d4edda;
            color: #155724;
        }

        .mention-tres-bien {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .mention-bien {
            background-color: #fff3cd;
            color: #856404;
        }

        .mention-assez-bien {
            background-color: #f8d7da;
            color: #721c24;
        }

        .mention-passable {
            background-color: #e2e3e5;
            color: #383d41;
        }

        /* Statistiques */
        .statistiques {
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            font-size: 9pt;
        }

        .statistiques h3 {
            font-size: 10pt;
            margin-bottom: 8px;
            color: #003366;
        }

        .stat-row {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }

        .stat-label {
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }

        /* Signatures */
        .signatures {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            font-size: 9pt;
        }

        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- En-tête officielle de l'école -->
        @include('pdf.includes.header')

        <!-- Titre -->
        <div class="document-title">
            Liste des Résultats par Ordre de Mérite
        </div>

        <!-- Informations concours -->
        <div class="concours-info">
            <div class="concours-name">{{ $concours->libelle_concours }}</div>
            @if ($session)
                <div class="session-name">Session: {{ $session->libelle_session }}</div>
            @endif
            @if (isset($filiere))
                <div class="session-name">Filière: {{ $filiere->libelle_filiere }}</div>
            @endif
        </div>

        <!-- Tableau des résultats -->
        <table class="resultats-table">
            <thead>
                <tr>
                    <th class="col-rang">Rang</th>
                    <th class="col-numero">N° Candidat</th>
                    <th class="col-nom">Nom</th>
                    <th class="col-prenom">Prénom(s)</th>
                    <th class="col-sexe">Sexe</th>
                    <th class="col-date-naissance">Date Naissance</th>
                    <th class="col-lieu-naissance">Lieu Naissance</th>
                    <th class="col-moyenne">Moyenne</th>
                    <th class="col-mention">Mention</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resultats as $index => $resultat)
                    <tr>
                        <td class="col-rang">{{ $index + 1 }}</td>
                        <td class="col-numero">
                            {{ $resultat->candidature->numero_candidature ?? $resultat->candidature->code_cand_def }}
                        </td>
                        <td class="col-nom">{{ strtoupper($resultat->candidature->candidat->nom_cand) }}</td>
                        <td class="col-prenom">{{ ucwords(strtolower($resultat->candidature->candidat->prenom_cand)) }}
                        </td>
                        <td class="col-sexe">{{ $resultat->candidature->candidat->sexe_cand ?? '-' }}</td>
                        <td class="col-date-naissance">
                            @if ($resultat->candidature->candidat->date_naissance_cand)
                                {{ \Carbon\Carbon::parse($resultat->candidature->candidat->date_naissance_cand)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="col-lieu-naissance">
                            {{ $resultat->candidature->candidat->lieu_naissance_cand ?? '-' }}
                        </td>
                        <td class="col-moyenne">{{ number_format($resultat->moyenne_generale, 2) }}</td>
                        <td class="col-mention">
                            @php
                                $mentionClass = match ($resultat->mention?->value ?? '') {
                                    'EXCELLENT' => 'mention-excellent',
                                    'TRES_BIEN' => 'mention-tres-bien',
                                    'BIEN' => 'mention-bien',
                                    'ASSEZ_BIEN' => 'mention-assez-bien',
                                    'PASSABLE' => 'mention-passable',
                                    default => '',
                                };
                            @endphp
                            <span class="mention {{ $mentionClass }}">
                                {{ $resultat->mention?->label() ?? 'N/A' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px; color: #999;">
                            Aucun résultat disponible pour ce concours
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Statistiques -->
        @if ($resultats->isNotEmpty())
            <div class="statistiques">
                <h3>Statistiques</h3>
                <div class="stat-row">
                    <span class="stat-label">Total candidats:</span> {{ $resultats->count() }}
                </div>
                <div class="stat-row">
                    <span class="stat-label">Moyenne générale:</span>
                    {{ number_format($resultats->avg('moyenne_generale'), 2) }}
                </div>
                <div class="stat-row">
                    <span class="stat-label">Meilleure moyenne:</span>
                    {{ number_format($resultats->max('moyenne_generale'), 2) }}
                </div>
                <div class="stat-row">
                    <span class="stat-label">Moyenne la plus basse:</span>
                    {{ number_format($resultats->min('moyenne_generale'), 2) }}
                </div>
            </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div>Le Président du Jury</div>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <div>Le Directeur</div>
                <div class="signature-line"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Document généré le {{ $dateGeneration }} à {{ $heureGeneration }}<br>
            {{ $ecole->libelle_ecole }} - Liste officielle des résultats
        </div>
    </div>
</body>

</html>

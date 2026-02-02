<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Résultats - {{ $concours->libelle_concours }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
        }

        .page {
            width: 100%;
            padding: 15px;
        }

        /* Titre principal */
        .document-title {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #003366;
            color: #fff;
        }

        .document-title h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .document-title .subtitle {
            font-size: 11pt;
            font-weight: normal;
            font-style: italic;
        }

        /* Informations concours */
        .concours-info {
            margin: 20px 0;
            padding: 12px;
            background-color: #f8f9fa;
            border-left: 4px solid #003366;
        }

        .concours-info .info-row {
            margin: 5px 0;
            font-size: 10pt;
        }

        .concours-info .label {
            font-weight: bold;
            color: #003366;
            display: inline-block;
            width: 150px;
        }

        /* Tableau des résultats */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9pt;
        }

        thead {
            background-color: #003366;
            color: #fff;
            display: table-row-group;
        }

        th {
            padding: 10px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #003366;
            font-size: 9pt;
        }

        tbody tr {
            border-bottom: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        td {
            padding: 8px 6px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        /* Colonnes spécifiques */
        .col-rang {
            width: 6%;
            text-align: center;
            font-weight: bold;
            color: #003366;
        }

        .col-numero {
            width: 12%;
            font-family: 'Courier New', monospace;
        }

        .col-nom {
            width: 22%;
            font-weight: 500;
        }

        .col-date {
            width: 12%;
            text-align: center;
            font-size: 8.5pt;
        }

        .col-lieu {
            width: 18%;
            font-size: 8.5pt;
        }

        .col-sexe {
            width: 6%;
            text-align: center;
        }

        .col-filiere {
            width: 16%;
            font-size: 8.5pt;
        }

        .col-decision {
            width: 8%;
            text-align: center;
            font-weight: bold;
        }

        /* Badge décision */
        .badge-admis {
            display: inline-block;
            padding: 4px 8px;
            background-color: #28a745;
            color: #fff;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        /* Statistiques */
        .statistics {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 2px solid #003366;
            border-radius: 5px;
        }

        .statistics h3 {
            color: #003366;
            font-size: 11pt;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-row {
            display: table;
            width: 100%;
            margin: 5px 0;
        }

        .stat-label {
            display: table-cell;
            width: 70%;
            font-weight: bold;
        }

        .stat-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            color: #003366;
            font-weight: bold;
            font-size: 11pt;
        }

        /* Footer officiel */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #003366;
            font-size: 8pt;
            color: #666;
        }

        .footer-content {
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
            text-align: left;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: right;
        }

        .signature-section {
            margin-top: 40px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 250px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
            text-decoration: underline;
        }

        .signature-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 9pt;
        }

        /* Cachet officiel */
        .official-stamp {
            margin-top: 20px;
            padding: 10px;
            border: 2px solid #003366;
            text-align: center;
            font-weight: bold;
            color: #003366;
            font-size: 9pt;
        }

        /* Message vide */
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="page">
        <!-- Header Officiel de l'École -->
        @if (isset($ecoleHeader))
        @include('pdf.includes.header')
        @endif

        <!-- Titre du document -->
        <div class="document-title">
            <h1>Liste des Candidats Admis</h1>
            <div class="subtitle">Résultats Officiels du Concours</div>
        </div>

        <!-- Informations du concours -->
        <div class="concours-info">
            <div class="info-row">
                <span class="label">Concours :</span>
                <span>{{ $concours->libelle_concours }}</span>
            </div>
            @if (isset($concours->session) && $concours->session)
            <div class="info-row">
                <span class="label">Session :</span>
                <span>{{ $concours->session->libelle_session }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="label">Date de publication :</span>
                <span>{{ $date_generation }}</span>
            </div>
        </div>

        <!-- Tableau des résultats -->
        <table>
            <thead>
                <tr>
                    <th class="col-numero">N° Candidat</th>
                    <th class="col-nom">Nom et Prénom</th>
                    <th class="col-date">Date Nais.</th>
                    <th class="col-lieu">Lieu de Naissance</th>
                    <th class="col-sexe">Sexe</th>
                    <th class="col-filiere">Filière</th>
                    {{-- <th>Moyenne</th> --}}
                    <th>Mention</th>
                    {{-- <th class="col-decision">Décision</th> --}}
                </tr>
            </thead>
            <tbody>
                @php
                $admisCount = 0;
                @endphp
                @forelse ($resultats as $resultat)
                @if ($resultat->est_admis)
                @php
                $admisCount++;
                @endphp
                <tr>
                    <td class="col-numero">
                        {{ $resultat->candidature->numero_candidature ?? $resultat->candidature->code_cand_def }}
                    </td>
                    <td class="col-nom">
                        {{ strtoupper($resultat->candidature->candidat->nom_cand) }}
                        {{ ucwords(strtolower($resultat->candidature->candidat->prenom_cand)) }}
                    </td>
                    <td class="col-date">
                        {{ $resultat->candidature->candidat->date_naissance_cand ? \Carbon\Carbon::parse($resultat->candidature->candidat->date_naissance_cand)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="col-lieu">{{ $resultat->candidature->candidat->lieu_naissance_cand ?? '-' }}</td>
                    <td class="col-sexe">
                        {{ $resultat->candidature->candidat->sexe_cand === 'M' ? 'M' : ($resultat->candidature->candidat->sexe_cand === 'F' ? 'F' : '-') }}
                    </td>
                    <td class="col-filiere">
                        {{ $resultat->candidature->candidat->filiere->libelle_filiere ?? '-' }}
                    </td>
                    {{-- <td>{{ number_format($resultat->moyenne_generale, 2) }}</td> --}}
                    <td>{{ $resultat->mention?->label() ?? '-' }}</td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="7" class="empty-message">
                        Aucun résultat disponible pour ce concours
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>


        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Le Directeur</div>
                <div class="signature-line">Signature et Cachet</div>
            </div>
        </div>

        <!-- Footer officiel -->
        <div class="footer">
            <div class="footer-content">
                <div class="footer-left">
                    Document officiel généré le {{ $date_generation }}<br>
                    Ce document fait foi jusqu'à preuve du contraire
                </div>
                <div class="footer-right">
                    {{ $ecole->libelle_ecole ?? '' }}<br>

                </div>
            </div>
        </div>
    </div>
</body>

</html>
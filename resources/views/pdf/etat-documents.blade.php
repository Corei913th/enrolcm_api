<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>État des documents - {{ $concours->libelle_concours }}</title>
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
            padding: 5px;
            text-align: left;
            font-size: 8pt;
            font-weight: bold;
        }

        td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 8pt;
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

        <div class="title" style="margin-top: 30px;">État des documents</div>

        <div class="section">
            <div class="section-title">Informations</div>
            <p><strong>Concours:</strong> {{ $concours->libelle_concours }}</p>
            @if ($session)
                <p><strong>Session:</strong> {{ $session->libelle_session ?? '' }}</p>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Liste des candidats et documents</div>
            @if ($candidatures->isEmpty())
                <div style="padding: 12px;" class="muted">Aucun candidat trouvé.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">N°</th>
                            <th style="width: 12%;">Code</th>
                            <th style="width: 20%;">Nom</th>
                            <th style="width: 20%;">Prénom</th>
                            @if ($documentsRequis->isEmpty())
                                <th style="width: 43%;">Statut</th>
                            @else
                                @foreach ($documentsRequis as $doc)
                                    <th style="width: {{ 43 / $documentsRequis->count() }}%;">{{ $doc->libelle_document ?? 'Doc' }}</th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidatures as $index => $candidature)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $candidature->code_cand_def ?? $candidature->code_cand_temp ?? '' }}</td>
                                <td>{{ strtoupper($candidature->candidat->nom_cand ?? '') }}</td>
                                <td>{{ ucwords(strtolower($candidature->candidat->prenom_cand ?? '')) }}</td>
                                @if ($documentsRequis->isEmpty())
                                    <td class="text-center muted">Aucun document requis</td>
                                @else
                                    @foreach ($documentsRequis as $docRequis)
                                        @php
                                            $documentSoumis = $candidature->documents->firstWhere('document_requis_id', $docRequis->id);
                                            $statut = $documentSoumis ? $documentSoumis->statut_verification->value : 'NON_SOUMIS';
                                            $statusClass = match($statut) {
                                                'VALIDE' => 'success',
                                                'REJETE' => 'error',
                                                'EN_ATTENTE' => 'muted',
                                                default => 'muted'
                                            };
                                            $statusLabel = match($statut) {
                                                'VALIDE' => '✓',
                                                'REJETE' => '✗',
                                                'EN_ATTENTE' => '⏳',
                                                'NON_SOUMIS' => '-',
                                                default => '?'
                                            };
                                        @endphp
                                        <td class="text-center {{ $statusClass }}">{{ $statusLabel }}</td>
                                    @endforeach
                                @endif
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

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Statistiques - {{ $concours->libelle_concours }}</title>
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

        .kv {
            width: 100%;
        }

        .kv td {
            border: none;
            padding: 2px 0;
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

        <div class="title" style="margin-top: 30px;">Statistiques du concours</div>

        <div class="section">
            <div class="section-title">Informations générales</div>
            <table class="kv">
                <tr>
                    <td style="width: 30%;"><strong>Concours</strong></td>
                    <td class="muted">{{ $concours->libelle_concours }}</td>
                </tr>
                @if ($session)
                    <tr>
                        <td><strong>Session</strong></td>
                        <td class="muted">{{ $session->libelle_session ?? '' }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Places totales</strong></td>
                    <td class="muted">{{ $concours->nbre_max_places ?? '' }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Statistiques des candidatures</div>
            <table>
                <thead>
                    <tr>
                        <th>Total</th>
                        <th>Validées</th>
                        <th>Soumises</th>
                        <th>Rejetées</th>
                        <th>Brouillon</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{ $stats['total_candidatures'] ?? 0 }}</td>
                        <td class="text-center">{{ $stats['validees'] ?? 0 }}</td>
                        <td class="text-center">{{ $stats['soumises'] ?? 0 }}</td>
                        <td class="text-center">{{ $stats['rejetees'] ?? 0 }}</td>
                        <td class="text-center">{{ $stats['brouillon'] ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if ($filieres->isNotEmpty())
            <div class="section">
                <div class="section-title">Filières</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50%;">Filière</th>
                            <th style="width: 15%;">Places</th>
                            <th style="width: 15%;">Validées</th>
                            <th style="width: 20%;">Taux</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filieres as $filiere)
                            <tr>
                                <td>{{ $filiere['libelle_filiere'] ?? '' }}</td>
                                <td class="text-center">{{ $filiere['nombre_places'] ?? '' }}</td>
                                <td class="text-center">{{ $filiere['candidatures_validees'] ?? 0 }}</td>
                                <td class="text-center">{{ $filiere['taux_remplissage'] ?? 0 }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($centres->isNotEmpty())
            <div class="section">
                <div class="section-title">Centres</div>
                <p><strong>Nombre de centres:</strong> {{ $centres->count() }}</p>
            </div>
        @endif

        @if (!empty($paymentConfig))
            <div class="section">
                <div class="section-title">Paiement</div>
                <table class="kv">
                    <tr>
                        <td style="width: 30%;"><strong>Montant</strong></td>
                        <td class="muted">{{ $paymentConfig['montant'] ?? '' }} {{ $paymentConfig['devise'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Frais</strong></td>
                        <td class="muted">{{ $paymentConfig['frais_paiement'] ?? '' }} {{ $paymentConfig['devise'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="muted">{{ $paymentConfig['montant_total'] ?? '' }} {{ $paymentConfig['devise'] ?? '' }}</td>
                    </tr>
                </table>
            </div>
        @endif

        <div class="footer">
            Document généré le {{ $dateGeneration }}
        </div>
    </div>
</body>

</html>

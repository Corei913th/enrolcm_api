<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fiche Concours - {{ $concours->libelle_concours }}</title>
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
    </style>
</head>

<body>
    <div class="page">
        @include('pdf.includes.header')

        <div class="title" style="margin-top: 30px;">Fiche concours</div>

        <div class="section">
            <div class="section-title">Informations générales</div>
            <table class="kv">
                <tr>
                    <td style="width: 25%;"><strong>Concours</strong></td>
                    <td class="muted">{{ $concours->libelle_concours }}</td>
                </tr>
                @if ($session)
                    <tr>
                        <td><strong>Session</strong></td>
                        <td class="muted">{{ $session->libelle_session ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Ouverture inscriptions</strong></td>
                        <td class="muted">{{ $session->date_ouverture_inscription ? \Carbon\Carbon::parse($session->date_ouverture_inscription)->format('d/m/Y') : '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Clôture inscriptions</strong></td>
                        <td class="muted">{{ $session->date_fermeture_inscription ? \Carbon\Carbon::parse($session->date_fermeture_inscription)->format('d/m/Y') : '' }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Date limite dépôt</strong></td>
                    <td class="muted">{{ $concours->date_limite_depot ? \Carbon\Carbon::parse($concours->date_limite_depot)->format('d/m/Y') : '' }}</td>
                </tr>
                <tr>
                    <td><strong>Frais inscription</strong></td>
                    <td class="muted">{{ $concours->frais_inscription ?? '' }}</td>
                </tr>
                <tr>
                    <td><strong>Places max</strong></td>
                    <td class="muted">{{ $concours->nbre_max_places ?? '' }}</td>
                </tr>
                <tr>
                    <td><strong>Statut</strong></td>
                    <td class="muted">{{ $concours->est_actif ? 'Actif' : 'Inactif' }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Statistiques</div>
            <table>
                <thead>
                    <tr>
                        <th>Total candidatures</th>
                        <th>Total validées</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $stats['total_candidatures'] ?? 0 }}</td>
                        <td>{{ $stats['total_validees'] ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Règles de paiement</div>
            @if (!empty($paymentConfig))
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
                        <td><strong>Montant total</strong></td>
                        <td class="muted">{{ $paymentConfig['montant_total'] ?? '' }} {{ $paymentConfig['devise'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date limite paiement</strong></td>
                        <td class="muted">{{ $paymentConfig['date_limite'] ? \Carbon\Carbon::parse($paymentConfig['date_limite'])->format('d/m/Y') : '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Type paiement</strong></td>
                        <td class="muted">{{ $paymentConfig['type_paiement'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Banques acceptées</strong></td>
                        <td class="muted">{{ !empty($paymentConfig['banques_acceptees']) ? implode(', ', $paymentConfig['banques_acceptees']) : '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Validation auto</strong></td>
                        <td class="muted">{{ !empty($paymentConfig['validation_auto']) ? 'Oui' : 'Non' }}</td>
                    </tr>
                    @if (!empty($paymentConfig['instructions']))
                        <tr>
                            <td><strong>Instructions</strong></td>
                            <td class="muted">{{ $paymentConfig['instructions'] }}</td>
                        </tr>
                    @endif
                </table>
            @else
                <div style="padding: 12px;" class="muted">Aucune configuration de paiement disponible.</div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Centres rattachés</div>
            @if (!empty($centres) && $centres->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40%;">Centre</th>
                            <th style="width: 20%;">Ville</th>
                            <th style="width: 20%;">Région</th>
                            <th style="width: 20%;">Capacité</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($centres as $centre)
                            <tr>
                                <td>{{ $centre->libelle_centre ?? '' }}</td>
                                <td>{{ $centre->ville_centre ?? '' }}</td>
                                <td>{{ $centre->region?->libelle?->label() ?? '' }}</td>
                                <td>{{ $centre->capacite ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding: 12px;" class="muted">Aucun centre rattaché.</div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Filières et places</div>
            @if (!empty($filieres) && $filieres->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40%;">Filière</th>
                            <th style="width: 20%;">Places</th>
                            <th style="width: 20%;">Validées</th>
                            <th style="width: 20%;">Restantes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filieres as $filiere)
                            <tr>
                                <td>{{ $filiere['libelle_filiere'] ?? '' }}</td>
                                <td>{{ $filiere['nombre_places'] ?? '' }}</td>
                                <td>{{ $filiere['candidatures_validees'] ?? '' }}</td>
                                <td>{{ $filiere['places_restantes'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding: 12px;" class="muted">Aucune filière rattachée.</div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Planning des épreuves</div>
            @if ($plannings->isEmpty())
                <div style="padding: 12px;" class="muted">Aucune épreuve planifiée.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 14%;">Date</th>
                            <th style="width: 10%;">Début</th>
                            <th style="width: 10%;">Fin</th>
                            <th style="width: 10%;">Durée</th>
                            <th style="width: 36%;">Épreuve</th>
                            <th style="width: 20%;">Centre</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plannings as $planning)
                            <tr>
                                <td>{{ $planning->date_epreuve ? \Carbon\Carbon::parse($planning->date_epreuve)->format('d/m/Y') : '' }}</td>
                                <td>{{ $planning->getHeureDebutFormatee() ?? '' }}</td>
                                <td>{{ $planning->getHeureFinFormatee() ?? '' }}</td>
                                <td>{{ $planning->getDureeEnMinutes() ?? '' }} min</td>
                                <td>{{ $planning->epreuve->intitule ?? '' }}</td>
                                <td>{{ $planning->centre->libelle_centre ?? '' }}</td>
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

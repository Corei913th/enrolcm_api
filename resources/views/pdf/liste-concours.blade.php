<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Concours</title>
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
            margin: 15px 0 20px 0;
            text-transform: uppercase;
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

        .col-num {
            width: 4%;
            text-align: center;
        }

        .col-code {
            width: 12%;
        }

        .col-libelle {
            width: 30%;
        }

        .col-ecole {
            width: 20%;
        }

        .col-session {
            width: 12%;
            text-align: center;
        }

        .col-dates {
            width: 12%;
            text-align: center;
            font-size: 8pt;
        }

        .col-statut {
            width: 10%;
            text-align: center;
        }

        .status-actif {
            color: #16a34a;
            font-weight: bold;
        }

        .status-inactif {
            color: #dc2626;
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
    </style>
</head>

<body>
    <div class="page">
        @include('pdf.includes.header')

        <div class="title" style="margin-top: 30px;">Liste des Concours</div>

        @if ($concours->isEmpty())
            <div style="padding: 20px; text-align: center; color: #666;">
                Aucun concours trouvé.
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th class="col-num">N°</th>
                        <th class="col-code">Code</th>
                        <th class="col-libelle">Libellé</th>
                        <th class="col-ecole">École</th>
                        <th class="col-session">Session Active</th>
                        <th class="col-dates">Dates</th>
                        <th class="col-statut">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($concours as $index => $c)
                        <tr>
                            <td class="col-num">{{ $index + 1 }}</td>
                            <td class="col-code">{{ $c->code_concours ?? '' }}</td>
                            <td class="col-libelle">{{ $c->libelle_concours ?? '' }}</td>
                            <td class="col-ecole">{{ $c->ecole->nom_ecole ?? 'N/A' }}</td>
                            <td class="col-session">
                                @php
                                    $activeSession = $c->sessions->first();
                                @endphp
                                @if($activeSession)
                                    {{ $activeSession->libelle_session ?? '' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="col-dates">
                                @if($activeSession)
                                    {{ $activeSession->date_debut_depot ? \Carbon\Carbon::parse($activeSession->date_debut_depot)->format('d/m/Y') : '' }}
                                    <br>au<br>
                                    {{ $activeSession->date_fin_depot ? \Carbon\Carbon::parse($activeSession->date_fin_depot)->format('d/m/Y') : '' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="col-statut">
                                @if($c->est_actif)
                                    <span class="status-actif">✓ Actif</span>
                                @else
                                    <span class="status-inactif">✗ Inactif</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="7">
                            <strong>Total : {{ $concours->count() }} concours</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        <div class="footer">
            Document généré le {{ $dateGeneration }}<br>
            Liste officielle des concours
        </div>
    </div>
</body>

</html>

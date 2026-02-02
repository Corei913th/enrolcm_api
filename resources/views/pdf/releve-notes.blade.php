<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Relevé de Notes - {{ $code_candidat }}</title>
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
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #059669;
        }

        .header h1 {
            color: #059669;
            font-size: 24pt;
            margin-bottom: 10px;
        }

        .info-box {
            background: #f0fdf4;
            border-left: 4px solid #059669;
            padding: 15px;
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            margin: 8px 0;
        }

        .info-label {
            font-weight: bold;
            width: 180px;
        }

        .info-value {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th {
            background: #059669;
            color: white;
            padding: 12px;
            text-align: left;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        table tr:nth-child(even) {
            background: #f9fafb;
        }

        .total-row {
            background: #d1fae5 !important;
            font-weight: bold;
            font-size: 12pt;
        }

        .result-box {
            background: #d1fae5;
            border: 2px solid #10b981;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }

        .result-box h2 {
            color: #065f46;
            font-size: 18pt;
            margin-bottom: 10px;
        }

        .result-box .moyenne {
            font-size: 32pt;
            color: #059669;
            font-weight: bold;
        }

        .result-box .rang {
            font-size: 16pt;
            color: #065f46;
            margin-top: 10px;
        }

        .decision {
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
        }

        .decision.admis {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }

        .decision.attente {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #f59e0b;
        }

        .decision.non-admis {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .signature-box {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 40px 0 5px;
        }
    </style>
</head>

<body>
    @if (isset($ecoleHeader))
        @include('pdf.includes.header')
    @endif

    <div class="header">
        @if ($ecole && $ecole->logo_full_path && !isset($ecoleHeader))
            <img src="{{ $ecole->logo_full_path }}" alt="Logo" style="max-height: 80px; margin-bottom: 10px;">
        @endif
        <h1>RELEVÉ DE NOTES</h1>
        <p>{{ $concours->libelle_concours }}</p>
        <p>Session {{ $concours->session->libelle_session ?? '' }}</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Code candidat :</span>
            <span class="info-value"><strong>{{ $code_candidat }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Nom et Prénom :</span>
            <span class="info-value">{{ $candidat->nom_cand }} {{ $candidat->prenom_cand }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de naissance :</span>
            <span
                class="info-value">{{ $candidat->date_naissance_cand ? \Carbon\Carbon::parse($candidat->date_naissance_cand)->format('d/m/Y') : '' }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th style="text-align: center;">Coefficient</th>
                <th style="text-align: center;">Note /20</th>
                <th style="text-align: center;">Note pondérée</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($notes as $note)
                <tr>
                    <td>{{ $note['matiere'] }}</td>
                    <td style="text-align: center;">{{ $note['coefficient'] }}</td>
                    <td style="text-align: center;">{{ number_format($note['note'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($note['note_ponderee'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>TOTAL</td>
                <td style="text-align: center;">{{ $total_coefficients }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="result-box">
        <h2>Résultat</h2>
        <div class="moyenne">{{ number_format($moyenne, 2) }}/20</div>
        @if ($resultat->rang)
            <div class="rang">Rang : {{ $resultat->rang }}</div>
        @endif
    </div>

    @if ($resultat->est_admis)
        <div class="decision admis">
            🎉 ADMIS(E)
        </div>
    @elseif($resultat->decision === 'LISTE_ATTENTE')
        <div class="decision attente">
            📋 LISTE D'ATTENTE
        </div>
    @else
        <div class="decision non-admis">
            NON ADMIS(E)
        </div>
    @endif

    @if ($resultat->observations)
        <div style="margin: 20px 0; padding: 15px; background: #f9fafb; border-left: 4px solid #6b7280;">
            <strong>Observations :</strong> {{ $resultat->observations }}
        </div>
    @endif

    <div class="signature-box">
        <div>
            <p>Date de publication :</p>
            <p><strong>{{ $resultat->date_publication ? \Carbon\Carbon::parse($resultat->date_publication)->format('d/m/Y') : '' }}</strong>
            </p>
        </div>
        <div style="text-align: center;">
            <div class="signature-line"></div>
            <p style="font-size: 10pt;">Le Directeur</p>
        </div>
    </div>

    <div class="footer">
        <p>{{ $ecole->nom_ecole ?? 'EnrolCM' }}</p>
        <p>Document généré le {{ $date_generation }}</p>
    </div>
</body>

</html>

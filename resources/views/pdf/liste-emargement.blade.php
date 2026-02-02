<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste d'émargement</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #059669;
        }

        .header h1 {
            color: #059669;
            font-size: 18pt;
            margin-bottom: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 15px 0;
        }

        .info-item {
            padding: 8px;
            background: #f9fafb;
            border-left: 3px solid #059669;
        }

        .info-label {
            font-weight: bold;
            font-size: 9pt;
            color: #666;
        }

        .info-value {
            font-size: 11pt;
            margin-top: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 9pt;
        }

        table th {
            background: #059669;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 9pt;
        }

        table td {
            padding: 8px 5px;
            border: 1px solid #ddd;
        }

        table tr:nth-child(even) {
            background: #f9fafb;
        }

        .signature-col {
            width: 150px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .signature-box {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature-line {
            border-top: 1px solid #333;
            width: 180px;
            margin: 30px 0 5px;
        }
    </style>
</head>

<body>
    @if (isset($ecoleHeader))
        @include('pdf.includes.header')
    @endif

    <div class="header">
        @if ($ecole && $ecole->logo_full_path && !isset($ecoleHeader))
            <img src="{{ $ecole->logo_full_path }}" alt="Logo" style="max-height: 60px; margin-bottom: 8px;">
        @endif
        <h1>LISTE D'ÉMARGEMENT</h1>
        <p style="font-size: 11pt;">{{ $concours->libelle_concours }}</p>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">ÉPREUVE</div>
            <div class="info-value">{{ $matiere->nom_matiere }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">DATE</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($date_epreuve)->format('d/m/Y') }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">HORAIRE</div>
            <div class="info-value">{{ substr($heure_debut, 0, 5) }} - {{ substr($heure_fin, 0, 5) }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">CENTRE</div>
            <div class="info-value">{{ $centre->nom_centre }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">SALLE</div>
            <div class="info-value">{{ $salle->nom_salle }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">CAPACITÉ</div>
            <div class="info-value">{{ $candidatures->count() }} / {{ $salle->capacite }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">N°</th>
                <th style="width: 100px;">Code</th>
                <th>Nom et Prénom</th>
                <th style="width: 80px;">Présent</th>
                <th class="signature-col">Signature</th>
                <th style="width: 100px;">Observations</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($candidatures as $index => $candidature)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $candidature->code_cand_def ?? $candidature->code_cand_temp }}</td>
                    <td>{{ $candidature->candidat->nom_cand }} {{ $candidature->candidat->prenom_cand }}</td>
                    <td style="text-align: center;">☐</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin: 20px 0; padding: 10px; background: #f9fafb; border-left: 3px solid #059669;">
        <strong>Total candidats :</strong> {{ $candidatures->count() }}
        <span style="margin-left: 30px;"><strong>Présents :</strong> _____ </span>
        <span style="margin-left: 30px;"><strong>Absents :</strong> _____ </span>
    </div>

    <div class="signature-box">
        <div>
            <p style="font-weight: bold; margin-bottom: 5px;">Surveillant(s)</p>
            <div class="signature-line"></div>
            <p style="font-size: 9pt;">Nom et signature</p>
        </div>
        <div>
            <p style="font-weight: bold; margin-bottom: 5px;">Responsable du centre</p>
            <div class="signature-line"></div>
            <p style="font-size: 9pt;">Nom et signature</p>
        </div>
    </div>

    <div class="footer">
        <p style="text-align: center; font-size: 8pt; color: #666;">
            Document généré le {{ $date_generation }}
        </p>
    </div>
</body>

</html>

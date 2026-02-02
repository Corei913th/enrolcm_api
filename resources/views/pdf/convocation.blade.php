<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Convocation - {{ $code_candidat }}</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .header-wrapper {
            margin-bottom: 20px;
        }

        .title-box {
            border: 2px solid #003366;
            background-color: #f0f4f8;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }

        .title-box h1 {
            color: #003366;
            font-size: 20pt;
            margin: 0;
            letter-spacing: 2px;
        }

        .section-title {
            background-color: #003366;
            color: #fff;
            padding: 4px 10px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }

        .label { font-weight: normal; color: #555; width: 150px; display: inline-block; }
        .value { font-weight: bold; color: #000; }

        .planning-table th {
            background-color: #f0f4f8;
            color: #003366;
            border: 1px solid #003366;
            padding: 8px;
            font-size: 9pt;
        }

        .planning-table td {
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 9pt;
        }

        .important-box {
            border: 1.5px dashed #cc0000;
            background-color: #fff5f5;
            padding: 10px;
            margin-top: 20px;
        }

        .important-box h4 {
            color: #cc0000;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .footer-signatures {
            margin-top: 30px;
            width: 100%;
        }

        .signature-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-line {
            margin-top: 40px;
            border-bottom: 1px solid #000;
            width: 150px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <div class="header-wrapper">
        @if(isset($ecoleHeader) && $ecoleHeader)
            {!! $ecoleHeader !!}
        @else
            @include('pdf.includes.header')
        @endif
    </div>

    <div style="position: relative; min-height: 80px; margin-top: 10px;">
        <div class="title-box" style="margin-right: 90px;">
            <h1>CONVOCATION</h1>
            <div class="font-bold">{{ $concours->libelle_concours }}</div>
            <div>Session {{ $session->libelle_session ?? $concours->session->libelle_session ?? '' }}</div>
        </div>

        @if($qrCode)
        <div style="position: absolute; right: 0; top: 0; width: 75px; height: 75px; border: 1px solid #eee; padding: 2px;">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width:100%; height:100%;">
        </div>
        @endif
    </div>

    <div class="section-title">Informations du Candidat</div>
    <table class="info-table">
        <tr>
            <td width="50%"><span class="label">Code Candidat:</span> <span class="value text-red">{{ $code_candidat }}</span></td>
            <td width="50%"><span class="label">Email:</span> <span class="value">{{ $candidat->utilisateur->email }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Nom & Prénom:</span> <span class="value uppercase">{{ $candidat->nom_cand }} {{ $candidat->prenom_cand }}</span></td>
            <td><span class="label">Date de Naissance:</span> <span class="value">{{ $candidat->date_naissance_cand ? \Carbon\Carbon::parse($candidat->date_naissance_cand)->format('d/m/Y') : '-' }}</span></td>
        </tr>
    </table>

    <div class="section-title">Lieu d'Examen</div>
    <table class="info-table">
        <tr>
            <td width="50%"><span class="label">Centre d'Examen:</span> <span class="value uppercase">{{ $centre->nom_centre ?? $centre->libelle_centre ?? 'N/A' }}</span></td>
            <td width="50%"><span class="label">Ville / Région:</span> <span class="value">{{ $centre->ville_centre ?? 'N/A' }} / {{ $centre->region?->libelle?->label() ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Adresse:</span> <span class="value">{{ $centre->adresse_centre ?? 'Voir affichage au centre' }}</span></td>
        </tr>
    </table>

    <div class="section-title">Planning des Épreuves</div>
    <table class="planning-table">
        <thead>
            <tr>
                <th width="20%">Date</th>
                <th width="25%">Heure</th>
                <th width="35%">Épreuve</th>
                <th width="20%">Salle</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($epreuves as $planning)
            <tr>
                <td class="text-center">{{ $planning->date_epreuve ? \Carbon\Carbon::parse($planning->date_epreuve)->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ $planning->heure_debut ? \Carbon\Carbon::parse($planning->heure_debut)->format('H:i') : '-' }} - {{ $planning->heure_fin ? \Carbon\Carbon::parse($planning->heure_fin)->format('H:i') : '-' }}</td>
                <td>{{ $planning->epreuve->intitule ?? 'N/A' }}</td>
                <td class="text-center">{{ $planning->salleExamen?->nom_salle ?? $planning->salleExamen?->libelle_salle ?? 'À définir' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="important-box">
        <h4>⚠️ Instructions aux Candidats</h4>
        <ul style="margin: 0; padding-left: 20px; font-size: 8.5pt;">
            <li>Présentation obligatoire <strong>30 minutes avant</strong> le début des épreuves.</li>
            <li>Munissez-vous de votre <strong>pièce d'identité originale</strong> et de cette convocation.</li>
            <li>Téléphones portables et sacs strictement interdits en salle d'examen.</li>
            <li>Matériel autorisé : stylos bleus/noirs, crayons, gomme, règle graduée.</li>
        </ul>
    </div>

    <table class="footer-signatures">
        <tr>
            <td class="signature-cell">
                <div style="font-size: 9pt;">Fait le {{ $date_generation }}</div>
            </td>
            <td class="signature-cell">
                <div class="font-bold">L'Administration</div>
                <div class="signature-line"></div>
                <div style="font-size: 8pt; margin-top: 5px;">Cachet et Signature</div>
            </td>
        </tr>
    </table>

</body>

</html>

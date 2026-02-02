<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fiche d'Inscription - {{ $candidature->numero_candidature }}</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
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
        .text-red { color: #cc0000; }
        .uppercase { text-transform: uppercase; }

        /* Header Section */
        .header-wrapper {
            margin-bottom: 20px;
        }

        /* Title Block */
        .title-box {
            border: 2px solid #003366;
            background-color: #f0f4f8;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }

        .title-box h1 {
            color: #003366;
            font-size: 16pt;
            margin: 0;
            letter-spacing: 1px;
        }

        .title-sub {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 5px;
            color: #003366;
        }

        .inscription-row {
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #003366;
            display: table;
            width: 100%;
        }

        .inscription-num-cell {
            display: table-cell;
            width: 70%;
            font-size: 11pt;
            text-align: center;
            vertical-align: middle;
        }

        .timbre-cell {
            display: table-cell;
            width: 30%;
            font-size: 8pt;
            font-style: italic;
            text-align: center;
            border-left: 1px solid #003366;
            color: #666;
        }

        /* Section Titles - Matching Convocation */
        .section-title {
            background-color: #003366;
            color: #fff;
            padding: 4px 10px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 9.5pt;
        }

        /* Info Table - Matching Convocation */
        .info-table td {
            padding: 5px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }

        .label { font-weight: normal; color: #555; width: 140px; display: inline-block; }
        .value { font-weight: bold; color: #000; }

        /* Documents Section */
        .docs-list {
            margin-top: 5px;
            font-size: 8pt;
            columns: 2;
            -webkit-columns: 2;
        }
        .docs-list div {
            margin-bottom: 2px;
            break-inside: avoid;
            padding-left: 5px;
        }

        /* Footer */
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

    <div style="position: relative; min-height: 90px; margin-top: 10px;">
        <div class="title-box" style="margin-right: 90px;">
            <h1 class="uppercase">Fiche d'Inscription - Session {{ $session->libelle_session ?? $session->annee ?? date('Y') }}</h1>
            <div class="title-sub uppercase">{{ $concours->libelle_concours }}</div>
            
            <div class="inscription-row">
                <div class="inscription-num-cell">
                    INSCRIPTION N° <span class="text-red font-bold">{{ $candidature->numero_candidature }}</span>
                </div>
                <div class="timbre-cell">
                    Timbre Fiscal<br>Stamp here
                </div>
            </div>
        </div>

        @if($qrCode)
        <div style="position: absolute; right: 0; top: 0; width: 80px; height: 80px; border: 1px solid #003366; padding: 3px; background: white;">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width:100%; height:100%;">
            <div style="font-size: 6pt; margin-top: 3px; text-align: center; font-weight: bold; color: #003366;">{{ $candidature->numero_candidature }}</div>
        </div>
        @endif
    </div>

    <div class="section-title">Informations Personnelles / Personal Informations</div>
    <table class="info-table">
        <tr>
            <td width="50%"><span class="label">Nom:</span> <span class="value uppercase">{{ $candidat->nom_cand }}</span></td>
            <td width="50%"><span class="label">Prénom:</span> <span class="value uppercase">{{ $candidat->prenom_cand }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Date naissance:</span> <span class="value">{{ $candidat->date_naissance_cand?->format('d/m/Y') }}</span></td>
            <td><span class="label">Lieu de naissance:</span> <span class="value uppercase">{{ $candidat->lieu_naissance_cand }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Sexe:</span> <span class="value">{{ $candidat->sexe_cand === 'M' ? 'Masculin' : 'Féminin' }}</span></td>
            <td><span class="label">Nationalité:</span> <span class="value">{{ $candidat->nationalite_cand }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Région d'origine:</span> <span class="value uppercase">{{ $candidat->region?->label() ?? 'N/A' }}</span></td>
            <td><span class="label">Département:</span> <span class="value uppercase">{{ $candidat->departement ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">CNI N°:</span> <span class="value">{{ $candidat->numero_cni }}</span></td>
            <td><span class="label">Téléphone:</span> <span class="value">{{ $candidat->utilisateur->telephone }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Email:</span> <span class="value">{{ $candidat->utilisateur->email }}</span></td>
            <td><span class="label">1ère Langue:</span> <span class="value">{{ $candidat->premiere_langue?->label() ?? 'Français' }}</span></td>
        </tr>
    </table>

    <div class="section-title">Informations Académiques / Academic Informations</div>
    <table class="info-table">
        <tr>
            <td width="50%"><span class="label">Filière:</span> <span class="value uppercase">{{ $candidat->filiere->libelle_filiere ?? 'N/A' }}</span></td>
            <td width="50%"><span class="label">Diplôme admission:</span> <span class="value uppercase">{{ $candidat->diplome_admission ?? 'BAC' }} {{ $candidat->serie_bac }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Année obtention:</span> <span class="value">{{ $candidat->annee_obtention_bac }}</span></td>
            <td><span class="label">Mention:</span> <span class="value uppercase">{{ $candidat->mention ?? 'Passable' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Centre examen:</span> <span class="value uppercase">{{ $candidature->centreExamen->libelle_centre ?? '-' }}</span></td>
            <td><span class="label">Centre de dépôt:</span> <span class="value uppercase">{{ $candidature->centreDepot->libelle_centre ?? '-' }}</span></td>
        </tr>
    </table>

    <div class="section-title">Autres Informations / Others Informations</div>
    <table class="info-table">
        <tr>
            <td width="50%"><span class="label">Nom du père:</span> <span class="value uppercase">{{ $candidat->nom_pere ?? '-' }}</span></td>
            <td width="50%"><span class="label">Tél du père:</span> <span class="value">{{ $candidat->telephone_pere ?? '-' }}</span></td>
        </tr>
        <tr>
            <td width="50%"><span class="label">Nom de la mère:</span> <span class="value uppercase">{{ $candidat->nom_parent ?? '-' }}</span></td>
            <td width="50%"><span class="label">Tél de la mère:</span> <span class="value">{{ $candidat->telephone_parent ?? '-' }}</span></td>
        </tr>
    </table>

    <div class="section-title" style="background-color: #cc0000;">Documents Nécessaires / Necessary Documents</div>
    <div class="docs-list">
        @if($concours->documentsRequis->count() > 0)
            @foreach($concours->documentsRequis as $doc)
                <div>• {{ $doc->nom_document }} {{ $doc->est_obligatoire ? '(*)' : '' }}</div>
            @endforeach
        @else
            <div>• Acte de naissance certifié (-3 mois) / <span style="color:#555">Certified birth certificate</span></div>
            <div>• Photocole certifiée du diplôme requis / <span style="color:#555">Certified true copy of diploma</span></div>
            <div>• Certificat médical d'aptitude / <span style="color:#555">Medical certificate</span></div>
            <div>• 04 Photos d'identité 4x4 / <span style="color:#555">04 Identity photos 4x4</span></div>
            <div>• Reçu de versement bancaire ({{ number_format($concours->frais_inscription, 0, ',', ' ') }} FCFA) / <span style="color:#555">Bank deposit receipt</span></div>
            <div>• Enveloppe A4 timbrée à l'adresse du candidat / <span style="color:#555">Stamped A4 envelope</span></div>
        @endif
    </div>

    <div style="margin-top: 20px;">
        <table width="100%">
            <tr>
                <td width="50%" style="font-size: 11pt; font-weight: bold;">
                    CODE CANDIDAT: <span style="color: #003366; font-size: 14pt;">{{ $candidature->code_cand_def ?? $candidature->numero_inscription }}</span>
                </td>
                <td width="50%" style="text-align: right; font-style: italic; font-size: 8pt; vertical-align: bottom;">
                    Fiche générée le {{ $dateGeneration }}
                </td>
            </tr>
        </table>
    </div>

    <table class="footer-signatures">
        <tr>
            <td class="signature-cell">
                <div style="font-size: 9pt;">Signature du Candidat</div>
                <div class="signature-line"></div>
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

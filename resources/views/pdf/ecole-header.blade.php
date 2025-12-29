<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
        }
        /* Bordure double élégante */
        .header-container {
            width: 765px;
            border: 4px double #003366;
            padding: 10px;
            margin: 0;
            box-sizing: border-box;
        }
        .inner-border {
            border: 2px solid #003366;
            padding: 12px 8px;
            box-sizing: border-box;
        }
        .header-table {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .header-column {
            display: table-cell;
            vertical-align: top;
            text-align: center; /* Tout le texte est centré dans les colonnes */
            width: 33.33%;
        }

        /* Styles de texte */
        .republique {
            font-size: 10px;
            font-weight: bold;
            line-height: 1.2;
            color: #003366;
        }
        .devise {
            font-size: 9px;
            font-weight: normal;
            color: #666;
            font-style: italic;
        }
        .separator {
            font-size: 9px;
            margin: 3px 0;
            letter-spacing: 2px;
            color: #003366;
            font-weight: bold;
        }
        .university {
            font-size: 11px;
            font-weight: bold;
            margin: 3px 0;
            color: #003366;
        }
        .school {
            font-size: 10px;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 5px;
            color: #000;
        }
        .contact-info {
            font-size: 8.5px;
            line-height: 1.3;
        }

        /* Section Centrale (Logos) */
        .center-column {
            vertical-align: middle;
            text-align: center;
        }
        /* Emblème en haut */
        .embleme-img {
            max-width: 180px;
            max-height: 180px;
            height: auto;
            margin: 0 auto 10px auto;
            display: block;
        }
        /* Logo école en bas */
        .logo-estic-img {
            max-width: 180px;
            max-height: 180px;
            height: auto;
            margin: 10px auto 5px auto;
            display: block;
        }
        /* Devise de l'école */
        .ecole-devise {
            font-size: 10px;
            font-style: italic;
            color: #003366;
            font-weight: bold;
            margin-top: 8px;
            line-height: 1.2;
        }
    </style>
</head>
<body>

<div class="header-container">
    <div class="inner-border">
        <div class="header-table">
        
        <div class="header-column">
            <div class="republique">
                REPUBLIQUE DU CAMEROUN<br>
                <span class="devise">Paix – Travail – Patrie</span>
            </div>
            <div class="separator">----------</div>
           
            <div class="school">
                {{ strtoupper($ecole->libelle_ecole ?? "ECOLE SUPERIEURE DE TRANSPORT, DE LOGISTIQUE ET DE COMMERCE") }}
            </div>
            <div class="separator">----------</div>
            <div class="contact-info">
                BP. {{ $ecole->bp_ecole ?? '22' }} <br>
                Tél.: {{ $ecole->telephone_ecole ?? '(+237) 222 482 412' }}<br>
                E-Mail: {{ $ecole->email_ecole ?? 'estic@estic.unv-ebolowa.cm' }}<br>
                Site web: {{ $ecole->siteweb_ecole ?? 'www.estic.unv-ebolowa.cm' }}
            </div>
        </div>

        <div class="header-column center-column">
            @if($embleme_path)
                <img src="{{ $embleme_path }}" class="embleme-img" alt="Emblème">
            @endif
            
            @if($logo_path)
                <img src="{{ $logo_path }}" class="logo-estic-img" alt="Logo École">
            @endif
            
            @if($ecole->devise)
                <div class="ecole-devise">"{{ $ecole->devise }}"</div>
            @endif
        </div>

        <div class="header-column">
            <div class="republique">
                REPUBLIC OF CAMEROON<br>
                <span class="devise">Peace – Work – Fatherland</span>
            </div>
            <div class="separator">----------</div>
          
            <div class="school">
                {{ strtoupper($ecole->libelle_ecole_en ?? "HIGHER INSTITUTE OF TRANSPORT, LOGISTICS AND COMMERCE") }}
            </div>
            <div class="separator">----------</div>
            <div class="contact-info">
                PO Box: {{ $ecole->bp_ecole ?? '22' }} <br>
                Phone: {{ $ecole->telephone_ecole ?? '(+237) 222 482 412' }}<br>
                E-Mail: {{ $ecole->email_ecole ?? 'estic@estic.unv-ebolowa.cm' }}<br>
                Website: {{ $ecole->siteweb_ecole ?? 'www.estic.unv-ebolowa.cm' }}
            </div>
        </div>

        </div>
    </div>
</div>

</body>
</html>
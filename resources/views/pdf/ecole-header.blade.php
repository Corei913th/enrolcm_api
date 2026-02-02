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

        .header-container {
            width: 100%;
            padding: 10px;
            margin: 0 0 15px 0;
            box-sizing: border-box;
        }

        .inner-border {
            border: none;
            padding: 12px 8px;
            box-sizing: border-box;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: none;
        }

        .header-column {
            vertical-align: top;
            text-align: center;
            width: 33.33%;
            padding: 0 5px;
            border: none;
        }

        .center-column {
            vertical-align: middle;
        }

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

        .embleme-img {
            width: 100px;
            height: 100px;
            margin: 0 auto 8px auto;
            display: block;
        }

        .logo-estic-img {
            width: 100px;
            height: 100px;
            margin: 8px auto 5px auto;
            display: block;
        }

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
            <table class="header-table" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="header-column">
                        <div class="republique">
                            REPUBLIQUE DU CAMEROUN<br>
                            <span class="devise">Paix – Travail – Patrie</span>
                        </div>
                        <div class="separator">* * * * *</div>
                        <div class="school">
                            {{ strtoupper($ecole->libelle_ecole ?? 'ECOLE SUPERIEURE') }}
                        </div>
                        <div class="separator">* * * * *</div>
                        <div class="contact-info">
                            BP: {{ $ecole->bp_ecole ?? 'N/A' }}<br>
                            Tél: {{ $ecole->telephone_ecole ?? 'N/A' }}<br>
                            E-Mail: {{ $ecole->email_ecole ?? '' }}<br>
                            Site web: {{ $ecole->siteweb_ecole ?? '' }}
                        </div>
                    </td>

                    <td class="header-column center-column">
                        @if ($embleme_path)
                            <img src="{{ $embleme_path }}" class="embleme-img" alt="Emblème">
                        @endif

                        @if ($logo_path)
                            <img src="{{ $logo_path }}" class="logo-estic-img" alt="Logo École">
                        @endif

                        @if ($ecole->devise)
                            <div class="ecole-devise">"{{ $ecole->devise }}"</div>
                        @endif
                    </td>

                    <td class="header-column">
                        <div class="republique">
                            REPUBLIC OF CAMEROON<br>
                            <span class="devise">Peace – Work – Fatherland</span>
                        </div>
                        <div class="separator">* * * * *</div>
                        <div class="school">
                            {{ strtoupper($ecole->libelle_ecole_en ?? 'HIGHER INSTITUTE') }}
                        </div>
                        <div class="separator">* * * * *</div>
                        <div class="contact-info">
                            PO Box: {{ $ecole->bp_ecole ?? 'N/A' }}<br>
                            Phone: {{ $ecole->telephone_ecole ?? 'N/A' }}<br>
                            E-Mail: {{ $ecole->email_ecole ?? '' }}<br>
                            Website: {{ $ecole->siteweb_ecole ?? '' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>

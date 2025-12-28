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
        }
        .official-header {
            width: 100%;
            border: 3px solid #1a5490;
            padding: 15px;
            margin-bottom: 20px;
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        }
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .logo-section {
            display: table-cell;
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }
        .logo-section img {
            max-width: 70px;
            max-height: 70px;
        }
        .center-section {
            display: table-cell;
            text-align: center;
            padding: 0 20px;
            vertical-align: middle;
        }
        .republique {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a5490;
            margin-bottom: 3px;
            line-height: 1.3;
        }
        .ecole-name {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .ecole-name-en {
            font-size: 12px;
            font-style: italic;
            color: #555;
            margin-bottom: 5px;
        }
        .devise {
            font-size: 10px;
            font-style: italic;
            color: #666;
            margin-top: 5px;
        }
        .contact-info {
            font-size: 9px;
            color: #444;
            margin-top: 8px;
            line-height: 1.4;
        }
        .header-frame {
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, #1a5490, #28a745, #ffc107);
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="official-header">
        <div class="header-top">
            <!-- Logo République / Emblème -->
            <div class="logo-section">
                @if($embleme_path && file_exists($embleme_path))
                    <img src="{{ $embleme_path }}" alt="Emblème">
                @endif
            </div>

            <!-- Informations centrales -->
            <div class="center-section">
                <div class="republique">
                    République du Cameroun<br>
                    Paix - Travail - Patrie<br>
                    ********
                </div>
                <div class="ecole-name">{{ $ecole->libelle_ecole }}</div>
                @if($ecole->libelle_ecole_en)
                    <div class="ecole-name-en">{{ $ecole->libelle_ecole_en }}</div>
                @endif
                @if($ecole->devise)
                    <div class="devise">"{{ $ecole->devise }}"</div>
                @endif
                <div class="contact-info">
                    @if($ecole->bp_ecole) BP: {{ $ecole->bp_ecole }} - @endif
                    @if($ecole->localisation) {{ $ecole->localisation }} @endif
                    @if($ecole->region) ({{ $ecole->region_label }}) @endif
                    <br>
                    @if($ecole->telephone_ecole) Tél: {{ $ecole->telephone_ecole }} @endif
                    @if($ecole->email_ecole) - Email: {{ $ecole->email_ecole }} @endif
                    @if($ecole->siteweb_ecole) <br> Web: {{ $ecole->siteweb_ecole }} @endif
                </div>
            </div>

            <!-- Logo École -->
            <div class="logo-section">
                @if($logo_path && file_exists($logo_path))
                    <img src="{{ $logo_path }}" alt="Logo">
                @endif
            </div>
        </div>

        <!-- Cadre décoratif -->
        <div class="header-frame"></div>
    </div>
</body>
</html>

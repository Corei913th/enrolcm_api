<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Liste d'émargement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 10px;
        }

        th {
            background-color: #f2f2f2;
        }

        .signature {
            width: 150px;
        }
    </style>
</head>

<body>
    <!-- En-tête officielle de l'école -->
    @if (isset($ecoleHeader))
        @include('pdf.includes.header')
    @endif

    <div class="header">
        <h2>LISTE D'ÉMARGEMENT</h2>
        <p>{{ $epreuve->intitule }}</p>
        <p>Date: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>Code</th>
                <th>Nom et Prénom</th>
                <th class="signature">Signature</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($candidatures as $index => $candidature)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $candidature->code_cand_def ?? $candidature->code_cand_temp ?? $candidature->numero_candidature ?? '' }}</td>
                    <td>{{ $candidature->candidat->nom_cand ?? '' }} {{ $candidature->candidat->prenom_cand ?? '' }}</td>
                    <td class="signature"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 30px;">Total: {{ $total }} candidats</p>
</body>

</html>

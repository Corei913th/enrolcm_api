<div style="margin-top: 40px;">
    <p style="text-align: center; margin-bottom: 30px;">
        <strong>N° {{ $numero ?? 'ATT-' . date('Y') . '-XXX' }}</strong>
    </p>

    <p style="margin-bottom: 20px;">
        Le Directeur de <strong>{{ $ecole->libelle_ecole }}</strong>,
    </p>

    <p style="margin-bottom: 30px; text-align: center; font-size: 18px;">
        <strong>ATTESTE QUE</strong>
    </p>

    <p style="margin-bottom: 20px;">
        M./Mme <strong>{{ $etudiant_nom ?? '[NOM DE L\'ÉTUDIANT]' }}</strong>,
        né(e) le {{ $date_naissance ?? '[DATE]' }} à {{ $lieu_naissance ?? '[LIEU]' }},
    </p>

    <p style="margin-bottom: 20px;">
        {{ $contenu ?? 'a été régulièrement inscrit(e) et a suivi avec assiduité les cours de l\'année académique ' . (date('Y')-1) . '-' . date('Y') . '.' }}
    </p>

    <p style="margin-bottom: 40px;">
        En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.
    </p>

    <div style="margin-top: 60px; text-align: right;">
        <p>Fait à {{ $ecole->localisation ?? 'Yaoundé' }}, le {{ $date ?? date('d/m/Y') }}</p>
        <p style="margin-top: 40px;">
            <strong>Le Directeur</strong><br>
            {{ $ecole->directeur_nom ?? '[Signature]' }}
        </p>
    </div>
</div>

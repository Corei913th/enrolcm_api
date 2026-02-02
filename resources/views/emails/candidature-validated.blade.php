@extends('emails.layout')

@section('title', 'Candidature validée')

@section('content')
    @php
        $concours = $candidature->concours;
        $nomConcours = $concours ? $concours->libelle_concours : 'Concours';
        $codeCandidatDef = $candidature->code_cand_def ?? $candidature->code_cand_temp;
        $numeroInscription = $candidature->numero_inscription;
    @endphp

    <h2>🎉 Félicitations ! Votre candidature est validée</h2>

    <p>Bonjour {{ $candidat->prenom_cand }} {{ $candidat->nom_cand }},</p>

    <p>
        Nous avons le plaisir de vous informer que votre candidature au concours
        <strong>{{ $nomConcours }}</strong> a été validée avec succès.
    </p>

    <div class="info-box" style="background-color: #d1fae5; border-left: 4px solid #10b981;">
        <p style="margin: 0;">
            <strong>✅ Statut :</strong> Candidature validée
        </p>
        @if ($codeCandidatDef)
            <p style="margin: 8px 0 0 0;">
                <strong>🔢 Code candidat :</strong> {{ $codeCandidatDef }}
            </p>
        @endif
        @if ($numeroInscription)
            <p style="margin: 8px 0 0 0;">
                <strong>📋 Numéro d'inscription :</strong> {{ $numeroInscription }}
            </p>
        @endif
    </div>

    <p>
        Votre dossier a été examiné et tous les documents requis ont été validés.
        Vous êtes maintenant officiellement inscrit(e) au concours.
    </p>

    <div class="info-box">
        <p>
            <strong>📋 Prochaines étapes :</strong>
        </p>
        <ul style="margin-left: 20px; margin-bottom: 0; color: #4b5563;">
            <li style="margin-bottom: 8px;">Téléchargez votre fiche d'inscription (jointe à cet email)</li>
            <li style="margin-bottom: 8px;">Conservez précieusement votre code candidat</li>
            <li style="margin-bottom: 8px;">Vous recevrez votre convocation quelques jours avant l'examen</li>
            <li style="margin-bottom: 8px;">Consultez régulièrement votre espace candidat pour les mises à jour</li>
        </ul>
    </div>

    @if ($concours)
        <div class="info-box" style="background-color: #fef3c7; border-left: 4px solid #f59e0b;">
            <p style="margin: 0;">
                <strong>📅 Informations sur le concours :</strong>
            </p>
            @if ($concours->date_debut_epreuve)
                <p style="margin: 8px 0 0 0;">
                    <strong>Date des épreuves :</strong>
                    {{ \Carbon\Carbon::parse($concours->date_debut_epreuve)->format('d/m/Y') }}
                    @if ($concours->date_fin_epreuve && $concours->date_debut_epreuve !== $concours->date_fin_epreuve)
                        au {{ \Carbon\Carbon::parse($concours->date_fin_epreuve)->format('d/m/Y') }}
                    @endif
                </p>
            @endif
            @if ($candidature->centre_examen_id && $candidature->centreExamen)
                <p style="margin: 8px 0 0 0;">
                    <strong>Centre d'examen :</strong> {{ $candidature->centreExamen->nom_centre }}
                </p>
            @endif
        </div>
    @endif

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/candidatures/{{ $candidature->id }}" class="button">
            Voir ma candidature
        </a>
    </div>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        <strong>💡 Conseils pour le jour de l'examen :</strong>
    </p>
    <ul style="margin-left: 20px; margin-bottom: 16px; color: #6b7280; font-size: 14px;">
        <li style="margin-bottom: 8px;">Présentez-vous avec votre pièce d'identité originale</li>
        <li style="margin-bottom: 8px;">Apportez votre convocation imprimée</li>
        <li style="margin-bottom: 8px;">Arrivez au moins 30 minutes avant le début des épreuves</li>
        <li style="margin-bottom: 8px;">Préparez le matériel autorisé (stylos, calculatrice si autorisée, etc.)</li>
    </ul>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        Des questions ? N'hésitez pas à
        <a href="mailto:support@enrolcm.com" style="color: #059669;">contacter notre support</a>.
    </p>

    <p style="font-size: 14px; color: #6b7280; margin-top: 16px;">
        Nous vous souhaitons bonne chance pour votre concours ! 🍀
    </p>
@endsection

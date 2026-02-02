@extends('emails.layout')

@section('title', 'Candidature rejetée')

@section('content')
    @php
        $concours = $candidature->concours;
        $nomConcours = $concours ? $concours->libelle_concours : 'Concours';
    @endphp

    <h2>❌ Candidature rejetée</h2>

    <p>Bonjour {{ $candidat->prenom_cand }} {{ $candidat->nom_cand }},</p>

    <p>
        Nous vous informons que votre candidature au concours <strong>{{ $nomConcours }}</strong>
        n'a malheureusement pas pu être validée.
    </p>

    <div class="info-box" style="background-color: #fee2e2; border-left: 4px solid #ef4444;">
        <p style="margin: 0;">
            <strong>❌ Statut :</strong> Candidature rejetée
        </p>
        <p style="margin: 8px 0 0 0;">
            <strong>🎓 Concours :</strong> {{ $nomConcours }}
        </p>
    </div>

    <div class="info-box" style="background-color: #fef3c7; border-left: 4px solid #f59e0b;">
        <p style="margin: 0;">
            <strong>⚠️ Motif du rejet :</strong>
        </p>
        <p style="margin: 8px 0 0 0; color: #92400e;">
            {{ $motif }}
        </p>
    </div>

    <p>
        Nous comprenons que cette nouvelle puisse être décevante. Voici quelques informations qui pourraient vous aider :
    </p>

    <div class="info-box">
        <p>
            <strong>📋 Que faire maintenant ?</strong>
        </p>
        <ul style="margin-left: 20px; margin-bottom: 0; color: #4b5563;">
            <li style="margin-bottom: 8px;">Vérifiez le motif du rejet ci-dessus</li>
            <li style="margin-bottom: 8px;">Consultez votre espace candidat pour plus de détails</li>
            <li style="margin-bottom: 8px;">Si le motif est corrigible, vous pourrez peut-être soumettre une nouvelle
                candidature</li>
            <li style="margin-bottom: 8px;">Contactez notre support si vous avez des questions</li>
        </ul>
    </div>

    @if ($concours && $concours->date_limite_depot && \Carbon\Carbon::parse($concours->date_limite_depot)->isFuture())
        <div class="info-box" style="background-color: #dbeafe; border-left: 4px solid #3b82f6;">
            <p style="margin: 0; color: #1e40af;">
                <strong>💡 Bonne nouvelle :</strong> La date limite d'inscription n'est pas encore passée
                ({{ \Carbon\Carbon::parse($concours->date_limite_depot)->format('d/m/Y') }}).
                Si le problème peut être corrigé, vous pouvez soumettre une nouvelle candidature.
            </p>
        </div>
    @endif

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/candidatures/{{ $candidature->id }}" class="button"
            style="background-color: #6b7280;">
            Voir ma candidature
        </a>
    </div>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        <strong>💡 Conseils pour une future candidature :</strong>
    </p>
    <ul style="margin-left: 20px; margin-bottom: 16px; color: #6b7280; font-size: 14px;">
        <li style="margin-bottom: 8px;">Assurez-vous de remplir tous les critères d'éligibilité</li>
        <li style="margin-bottom: 8px;">Soumettez des documents de qualité et conformes aux exigences</li>
        <li style="margin-bottom: 8px;">Vérifiez que votre paiement est valide et complet</li>
        <li style="margin-bottom: 8px;">Respectez les délais de dépôt</li>
    </ul>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        Des questions sur ce rejet ? N'hésitez pas à
        <a href="mailto:support@enrolcm.com" style="color: #059669;">contacter notre support</a>.
        Notre équipe se fera un plaisir de vous aider.
    </p>

    <p style="font-size: 14px; color: #6b7280; margin-top: 16px;">
        Nous vous encourageons à persévérer et vous souhaitons bonne chance pour vos futures candidatures.
    </p>
@endsection

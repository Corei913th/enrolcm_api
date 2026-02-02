@extends('emails.layout')

@section('title', 'Paiement validé')

@section('content')
    @php
        $concours = $paiement->concours;
        $nomConcours = $concours ? $concours->libelle_concours : 'Concours';
    @endphp

    <h2>✅ Paiement validé avec succès</h2>

    <p>Bonjour {{ $candidat->prenom_cand }} {{ $candidat->nom_cand }},</p>

    <p>
        Excellente nouvelle ! Votre paiement pour le concours <strong>{{ $nomConcours }}</strong>
        a été validé avec succès par notre équipe.
    </p>

    <div class="info-box" style="background-color: #d1fae5; border-left: 4px solid #10b981;">
        <p style="margin: 0;">
            <strong>✅ Statut :</strong> Paiement validé
        </p>
        <p style="margin: 8px 0 0 0;">
            <strong>💳 Référence :</strong> {{ $paiement->reference }}
        </p>
        <p style="margin: 8px 0 0 0;">
            <strong>💰 Montant :</strong> {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
        </p>
        @if ($concours)
            <p style="margin: 8px 0 0 0;">
                <strong>🎓 Concours :</strong> {{ $nomConcours }}
            </p>
        @endif
        @if ($paiement->date_verification)
            <p style="margin: 8px 0 0 0;">
                <strong>📅 Date de validation :</strong>
                {{ \Carbon\Carbon::parse($paiement->date_verification)->format('d/m/Y à H:i') }}
            </p>
        @endif
    </div>

    <p>
        Votre paiement a été vérifié et approuvé. Vous pouvez maintenant continuer votre processus d'inscription
        et soumettre les documents requis.
    </p>

    <div class="info-box">
        <p>
            <strong>📋 Prochaines étapes :</strong>
        </p>
        <ul style="margin-left: 20px; margin-bottom: 0; color: #4b5563;">
            <li style="margin-bottom: 8px;">Connectez-vous à votre espace candidat</li>
            <li style="margin-bottom: 8px;">Complétez votre dossier en soumettant tous les documents requis</li>
            <li style="margin-bottom: 8px;">Vérifiez que toutes les informations sont correctes</li>
            <li style="margin-bottom: 8px;">Suivez l'état de votre candidature en temps réel</li>
        </ul>
    </div>

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/candidatures" class="button">
            Continuer mon inscription
        </a>
    </div>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        <strong>💡 Important :</strong> Conservez cette confirmation de paiement et votre référence
        <strong>{{ $paiement->reference }}</strong> pour vos dossiers.
    </p>

    @if ($concours && $concours->date_limite_depot)
        <div class="info-box" style="background-color: #fef3c7; border-left: 4px solid #f59e0b;">
            <p style="margin: 0; color: #92400e;">
                <strong>⏰ Rappel :</strong> La date limite de dépôt des dossiers est le
                <strong>{{ \Carbon\Carbon::parse($concours->date_limite_depot)->format('d/m/Y') }}</strong>.
                Assurez-vous de soumettre tous vos documents avant cette date.
            </p>
        </div>
    @endif

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        Des questions ? N'hésitez pas à
        <a href="mailto:support@enrolcm.com" style="color: #059669;">contacter notre support</a>.
    </p>

    <p style="font-size: 14px; color: #6b7280; margin-top: 16px;">
        Bonne continuation dans votre processus d'inscription ! 🎓
    </p>
@endsection

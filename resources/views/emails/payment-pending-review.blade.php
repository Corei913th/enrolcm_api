@extends('emails.layout')

@section('title', 'Paiement en attente de validation')

@section('content')
    @php
        $concours = $paiement->concours;
        $nomConcours = $concours ? $concours->libelle_concours : 'Concours';
    @endphp

    <h2>⏳ Paiement en attente de validation</h2>

    <p>Bonjour {{ $candidat->prenom_cand }} {{ $candidat->nom_cand }},</p>

    <p>
        Nous avons bien reçu votre paiement pour le concours <strong>{{ $nomConcours }}</strong>.
        Votre paiement est actuellement en cours de vérification par notre équipe.
    </p>

    <div class="info-box" style="background-color: #fef3c7; border-left: 4px solid #f59e0b;">
        <p style="margin: 0;">
            <strong>💳 Référence du paiement :</strong> {{ $paiement->reference }}
        </p>
        <p style="margin: 8px 0 0 0;">
            <strong>💰 Montant :</strong> {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
        </p>
        @if ($concours)
            <p style="margin: 8px 0 0 0;">
                <strong>🎓 Concours :</strong> {{ $nomConcours }}
            </p>
        @endif
        <p style="margin: 8px 0 0 0;">
            <strong>📅 Date de soumission :</strong> {{ $paiement->created_at->format('d/m/Y à H:i') }}
        </p>
    </div>

    <p>
        <strong>⏱️ Délai de traitement :</strong> Notre équipe vérifie généralement les paiements sous 24 à 48 heures
        ouvrables. Vous recevrez une notification dès que votre paiement sera validé.
    </p>

    <div class="info-box">
        <p>
            <strong>📋 Que se passe-t-il maintenant ?</strong>
        </p>
        <ul style="margin-left: 20px; margin-bottom: 0; color: #4b5563;">
            <li style="margin-bottom: 8px;">Notre équipe vérifie les informations de votre paiement</li>
            <li style="margin-bottom: 8px;">Vous recevrez une notification par email et sur votre espace candidat</li>
            <li style="margin-bottom: 8px;">Une fois validé, vous pourrez continuer votre inscription</li>
            <li style="margin-bottom: 8px;">En cas de problème, nous vous contacterons avec les détails</li>
        </ul>
    </div>

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/paiements/{{ $paiement->id }}" class="button"
            style="background-color: #f59e0b;">
            Suivre mon paiement
        </a>
    </div>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        <strong>💡 Conseil :</strong> Conservez votre référence de paiement <strong>{{ $paiement->reference }}</strong>
        pour toute correspondance avec notre service.
    </p>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        Des questions sur votre paiement ? N'hésitez pas à
        <a href="mailto:support@enrolcm.com" style="color: #059669;">contacter notre support</a>
        en mentionnant votre référence de paiement.
    </p>
@endsection

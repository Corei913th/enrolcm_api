@extends('emails.layout')

@section('title', 'Paiement rejeté')

@section('content')
    @php
        $concours = $paiement->concours;
        $nomConcours = $concours ? $concours->libelle_concours : 'Concours';
    @endphp

    <h2>❌ Paiement rejeté</h2>

    <p>Bonjour {{ $candidat->prenom_cand }} {{ $candidat->nom_cand }},</p>

    <p>
        Nous vous informons que votre paiement pour le concours <strong>{{ $nomConcours }}</strong>
        n'a malheureusement pas pu être validé.
    </p>

    <div class="info-box" style="background-color: #fee2e2; border-left: 4px solid #ef4444;">
        <p style="margin: 0;">
            <strong>❌ Statut :</strong> Paiement rejeté
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
        <strong>📋 Action requise :</strong> Pour continuer votre inscription, vous devez soumettre un nouveau
        paiement valide. Veuillez corriger le problème mentionné ci-dessus avant de soumettre à nouveau.
    </p>

    <div class="info-box">
        <p>
            <strong>💡 Conseils pour un paiement valide :</strong>
        </p>
        <ul style="margin-left: 20px; margin-bottom: 0; color: #4b5563;">
            <li style="margin-bottom: 8px;">Vérifiez que le montant correspond exactement aux frais du concours</li>
            <li style="margin-bottom: 8px;">Assurez-vous que la preuve de paiement est claire et lisible</li>
            <li style="margin-bottom: 8px;">Vérifiez que toutes les informations sont visibles (date, montant, référence)
            </li>
            <li style="margin-bottom: 8px;">Utilisez un mode de paiement accepté par la plateforme</li>
            <li style="margin-bottom: 8px;">Assurez-vous que le fichier est au bon format (PDF, JPG, PNG)</li>
        </ul>
    </div>

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/paiements/nouveau" class="button" style="background-color: #ef4444;">
            Soumettre un nouveau paiement
        </a>
    </div>

    @if ($concours && $concours->date_limite_depot)
        <div class="info-box" style="background-color: #fee2e2; border-left: 4px solid #dc2626;">
            <p style="margin: 0; color: #991b1b;">
                <strong>⏰ Attention :</strong> La date limite de dépôt des dossiers est le
                <strong>{{ \Carbon\Carbon::parse($concours->date_limite_depot)->format('d/m/Y') }}</strong>.
                Veuillez soumettre un nouveau paiement rapidement pour ne pas manquer cette échéance.
            </p>
        </div>
    @endif

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        <strong>💳 Modes de paiement acceptés :</strong>
    </p>
    <ul style="margin-left: 20px; margin-bottom: 16px; color: #6b7280; font-size: 14px;">
        <li style="margin-bottom: 8px;">Virement bancaire</li>
        <li style="margin-bottom: 8px;">Mobile Money (Orange Money, MTN Mobile Money, etc.)</li>
        <li style="margin-bottom: 8px;">Paiement en espèces (avec reçu officiel)</li>
    </ul>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        Des questions sur ce rejet ou besoin d'aide pour soumettre un nouveau paiement ?
        N'hésitez pas à <a href="mailto:support@enrolcm.com" style="color: #059669;">contacter notre support</a>
        en mentionnant votre référence <strong>{{ $paiement->reference }}</strong>.
    </p>
@endsection

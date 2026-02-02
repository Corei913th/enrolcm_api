@extends('emails.layout')

@section('title', 'Document validé')

@section('content')
    @php
        $documentRequis = $document->documentRequis;
        $nomDocument = $documentRequis ? $documentRequis->nom_document : 'Document';
        $candidature = $document->candidature;
        $concours = $candidature ? $candidature->concours : null;
    @endphp

    <h2>✅ Document validé avec succès</h2>

    <p>Bonjour {{ $candidat->prenom_cand }} {{ $candidat->nom_cand }},</p>

    <p>
        Nous avons le plaisir de vous informer que votre document a été validé par notre équipe.
    </p>

    <div class="info-box" style="background-color: #d1fae5; border-left: 4px solid #10b981;">
        <p style="margin: 0;">
            <strong>📄 Document validé :</strong> {{ $nomDocument }}
        </p>
        @if ($concours)
            <p style="margin: 8px 0 0 0;">
                <strong>🎓 Concours :</strong> {{ $concours->libelle_concours }}
            </p>
        @endif
    </div>

    <p>
        Votre document a été vérifié et approuvé. Il fait maintenant partie de votre dossier de candidature.
    </p>

    @if ($candidature)
        <div class="info-box">
            <p>
                <strong>📋 Prochaines étapes :</strong>
            </p>
            <ul style="margin-left: 20px; margin-bottom: 0; color: #4b5563;">
                <li style="margin-bottom: 8px;">Vérifiez que tous vos documents requis sont soumis</li>
                <li style="margin-bottom: 8px;">Assurez-vous que votre paiement est validé</li>
                <li style="margin-bottom: 8px;">Suivez l'état de votre candidature sur votre tableau de bord</li>
            </ul>
        </div>
    @endif

    <div class="button-container">
        <a href="{{ config('app.frontend_url') }}/candidatures/{{ $candidature?->id }}" class="button">
            Voir ma candidature
        </a>
    </div>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        Si vous avez des questions, n'hésitez pas à
        <a href="mailto:support@enrolcm.com" style="color: #059669;">contacter notre support</a>.
    </p>
@endsection

@extends('emails.layout')

@section('title', 'Document rejeté')

@section('content')
    @php
        $documentRequis = $document->documentRequis;
        $nomDocument = $documentRequis ? $documentRequis->nom_document : 'Document';
        $candidature = $document->candidature;
        $concours = $candidature ? $candidature->concours : null;
    @endphp

    <h2>❌ Document rejeté</h2>

    <p>Bonjour {{ $candidat->prenom_cand }} {{ $candidat->nom_cand }},</p>

    <p>
        Nous vous informons que votre document a été examiné par notre équipe et n'a malheureusement pas pu être validé.
    </p>

    <div class="info-box" style="background-color: #fee2e2; border-left: 4px solid #ef4444;">
        <p style="margin: 0;">
            <strong>📄 Document rejeté :</strong> {{ $nomDocument }}
        </p>
        @if ($concours)
            <p style="margin: 8px 0 0 0;">
                <strong>🎓 Concours :</strong> {{ $concours->libelle_concours }}
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
        <strong>📋 Action requise :</strong> Veuillez soumettre un nouveau document conforme aux exigences.
    </p>

    <div class="info-box">
        <p>
            <strong>💡 Conseils pour soumettre un document valide :</strong>
        </p>
        <ul style="margin-left: 20px; margin-bottom: 0; color: #4b5563;">
            <li style="margin-bottom: 8px;">Assurez-vous que le document est lisible et de bonne qualité</li>
            <li style="margin-bottom: 8px;">Vérifiez que toutes les informations sont visibles</li>
            <li style="margin-bottom: 8px;">Respectez le format et la taille maximale autorisés</li>
            <li style="margin-bottom: 8px;">Assurez-vous que le document correspond au type demandé</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ config('app.frontend_url') }}/candidatures/{{ $candidature?->id }}" class="button"
            style="background-color: #ef4444;">
            Soumettre un nouveau document
        </a>
    </div>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        Si vous avez des questions concernant ce rejet, n'hésitez pas à
        <a href="mailto:support@enrolcm.com" style="color: #059669;">contacter notre support</a>.
    </p>
@endsection

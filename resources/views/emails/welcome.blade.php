@extends('emails.layout')

@section('title', 'Bienvenue sur EnrolCM')

@section('content')
    <h2>Bienvenue sur EnrolCM !</h2>

    <p>Bonjour,</p>

    <p>
        Nous sommes ravis de vous accueillir sur <strong>EnrolCM</strong>, votre plateforme de gestion
        des inscriptions aux concours.
    </p>

    <p>
        Votre compte a été créé avec succès avec l'adresse email :
        <strong>{{ $utilisateur->email }}</strong>
    </p>

    @if ($concours)
        <div class="info-box">
            <p>
                <strong>📝 Votre inscription :</strong> Vous êtes inscrit au concours
                <strong>{{ $concours->libelle_concours }}</strong>.
                Votre dossier est en cours de traitement et vous recevrez des notifications
                sur l'avancement de votre candidature.
            </p>
        </div>
    @endif

    <div class="info-box">
        <p>
            <strong>📧 Prochaine étape :</strong> Vous allez recevoir un email de vérification.
            Veuillez cliquer sur le lien de vérification pour activer votre compte et accéder
            à toutes les fonctionnalités de la plateforme.
        </p>
    </div>

    <p><strong>Avec EnrolCM, vous pouvez :</strong></p>
    <ul style="margin-left: 20px; margin-bottom: 16px; color: #4b5563;">
        <li style="margin-bottom: 8px;">✓ Vérifier votre éligibilité aux concours</li>
        <li style="margin-bottom: 8px;">✓ Soumettre vos documents en ligne</li>
        <li style="margin-bottom: 8px;">✓ Suivre l'état de votre candidature en temps réel</li>
        <li style="margin-bottom: 8px;">✓ Télécharger vos documents officiels</li>
    </ul>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        Si vous n'avez pas créé ce compte, veuillez ignorer cet email ou
        <a href="mailto:support@enrolcm.com" style="color: #059669;">contacter notre support</a>.
    </p>
@endsection

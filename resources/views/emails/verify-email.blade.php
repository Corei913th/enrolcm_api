@extends('emails.layout')

@section('title', 'Vérification de votre adresse email')

@section('content')
<h2>Vérifiez votre adresse email</h2>

<p>Bonjour {{ $utilisateur->user_name }},</p>

<p>
    Merci de vous être inscrit sur EnrolCM. Pour accéder à toutes les fonctionnalités et recevoir les notifications importantes, veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous.
</p>

<div class="info-box" style="background-color: #eff6ff; border-left-color: #3b82f6;">
    <p style="color: #1e40af;">
        <strong>ℹ️ Pourquoi vérifier ?</strong><br>
        La vérification nous permet de vous contacter en cas de problème avec votre dossier (documents manquants, date d'examen, résultats...).
    </p>
</div>

<a href="{{ $verificationUrl }}" class="button">
    Vérifier mon adresse email
</a>

<p style="margin-top: 24px;">
    Si le bouton ne fonctionne pas, copiez et collez le lien suivant dans votre navigateur :<br>
    <span style="font-size: 14px; color: #6b7280; word-break: break-all;">{{ $verificationUrl }}</span>
</p>

<div class="divider"></div>

<p style="font-size: 14px; color: #6b7280;">
    Si vous n'avez pas créé de compte sur EnrolCM, aucune action n'est requise de votre part.
</p>
@endsection
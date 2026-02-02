@extends('emails.layout')

@section('title', $alert->title)

@section('content')
    <h2>{{ $alert->title }}</h2>

    <p>Bonjour {{ $candidat->prenom_cand }} {{ $candidat->nom_cand }},</p>

    <p>{{ $alert->message }}</p>

    @if ($alert->severity === 'critical')
        <div class="info-box" style="background-color: #fef2f2; border-left-color: #dc2626;">
            <p style="color: #991b1b;">
                <strong>⚠️ Action urgente requise</strong><br>
                Cette alerte nécessite votre attention immédiate pour éviter tout problème avec votre candidature.
            </p>
        </div>
    @elseif ($alert->severity === 'warning')
        <div class="info-box" style="background-color: #fffbeb; border-left-color: #f59e0b;">
            <p style="color: #92400e;">
                <strong>⚠️ Attention</strong><br>
                Nous vous recommandons de traiter cette alerte rapidement.
            </p>
        </div>
    @endif

    <a href="{{ config('app.frontend_url') }}/candidate/dashboard" class="button">
        Accéder à mon espace candidat
    </a>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        <strong>Détails de l'alerte :</strong><br>
        Type : {{ $alert->type }}<br>
        Date : {{ $alert->created_at->format('d/m/Y à H:i') }}
    </p>

    @if ($alert->candidature)
        <p style="font-size: 14px; color: #6b7280;">
            Concours : {{ $alert->candidature->concours->libelle_concours ?? 'N/A' }}<br>
            @if ($alert->candidature->concours && $alert->candidature->concours->date_limite_depot)
                Date limite : {{ \Carbon\Carbon::parse($alert->candidature->concours->date_limite_depot)->format('d/m/Y') }}
            @endif
        </p>
    @endif

    <div class="info-box">
        <p>
            <strong>💡 Conseil :</strong><br>
            Connectez-vous régulièrement à votre espace candidat pour suivre l'évolution de votre dossier et ne manquer
            aucune information importante.
        </p>
    </div>
@endsection

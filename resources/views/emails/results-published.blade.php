@extends('emails.layout')

@section('title', 'Résultats disponibles')

@section('content')
    @php
        $concours = $candidature->concours;
        $nomConcours = $concours ? $concours->libelle_concours : 'Concours';
        $estAdmis = $resultat->est_admis;
        $decision = $resultat->decision;
        $moyenne = $resultat->moyenne_generale;
        $rang = $resultat->rang;
    @endphp

    @if ($estAdmis)
        <h2>🎉 Félicitations ! Vous êtes admis(e)</h2>
    @elseif ($decision === 'LISTE_ATTENTE')
        <h2>📋 Liste d'attente</h2>
    @else
        <h2>📊 Résultats disponibles</h2>
    @endif

    <p>Bonjour {{ $candidat->prenom_cand }} {{ $candidat->nom_cand }},</p>

    <p>
        Les résultats du concours <strong>{{ $nomConcours }}</strong> sont maintenant disponibles.
    </p>

    @if ($estAdmis)
        <div class="info-box" style="background-color: #d1fae5; border-left: 4px solid #10b981;">
            <p style="margin: 0; font-size: 18px; color: #065f46;">
                <strong>🎊 Félicitations ! Vous êtes ADMIS(E)</strong>
            </p>
            @if ($moyenne)
                <p style="margin: 8px 0 0 0; color: #065f46;">
                    <strong>📈 Moyenne générale :</strong> {{ number_format($moyenne, 2) }}/20
                </p>
            @endif
            @if ($rang)
                <p style="margin: 8px 0 0 0; color: #065f46;">
                    <strong>🏆 Rang :</strong> {{ $rang }}
                </p>
            @endif
        </div>

        <p>
            Nous sommes ravis de vous annoncer que vous avez réussi le concours avec succès.
            Toutes nos félicitations pour ce bel accomplissement !
        </p>

        <div class="info-box">
            <p>
                <strong>📋 Prochaines étapes :</strong>
            </p>
            <ul style="margin-left: 20px; margin-bottom: 0; color: #4b5563;">
                <li style="margin-bottom: 8px;">Consultez votre relevé de notes détaillé sur votre espace candidat</li>
                <li style="margin-bottom: 8px;">Vous recevrez prochainement les instructions pour la suite du processus</li>
                <li style="margin-bottom: 8px;">Conservez précieusement tous vos documents officiels</li>
                <li style="margin-bottom: 8px;">Restez attentif aux communications officielles</li>
            </ul>
        </div>
    @elseif ($decision === 'LISTE_ATTENTE')
        <div class="info-box" style="background-color: #fef3c7; border-left: 4px solid #f59e0b;">
            <p style="margin: 0; font-size: 18px; color: #92400e;">
                <strong>📋 Vous êtes sur la LISTE D'ATTENTE</strong>
            </p>
            @if ($moyenne)
                <p style="margin: 8px 0 0 0; color: #92400e;">
                    <strong>📈 Moyenne générale :</strong> {{ number_format($moyenne, 2) }}/20
                </p>
            @endif
            @if ($rang)
                <p style="margin: 8px 0 0 0; color: #92400e;">
                    <strong>🏆 Rang :</strong> {{ $rang }}
                </p>
            @endif
        </div>

        <p>
            Vous êtes actuellement sur la liste d'attente pour ce concours. Cela signifie que vous pourriez
            être admis(e) si des places se libèrent.
        </p>

        <div class="info-box">
            <p>
                <strong>📋 Ce que cela signifie :</strong>
            </p>
            <ul style="margin-left: 20px; margin-bottom: 0; color: #4b5563;">
                <li style="margin-bottom: 8px;">Vous n'êtes pas encore admis(e), mais vous êtes en bonne position</li>
                <li style="margin-bottom: 8px;">Des places peuvent se libérer si des candidats admis se désistent</li>
                <li style="margin-bottom: 8px;">Nous vous tiendrons informé(e) de l'évolution de votre situation</li>
                <li style="margin-bottom: 8px;">Restez attentif à vos notifications et emails</li>
            </ul>
        </div>
    @else
        <div class="info-box" style="background-color: #fee2e2; border-left: 4px solid #ef4444;">
            <p style="margin: 0; font-size: 18px; color: #991b1b;">
                <strong>📊 Résultats</strong>
            </p>
            @if ($moyenne)
                <p style="margin: 8px 0 0 0; color: #991b1b;">
                    <strong>📈 Moyenne générale :</strong> {{ number_format($moyenne, 2) }}/20
                </p>
            @endif
            @if ($rang)
                <p style="margin: 8px 0 0 0; color: #991b1b;">
                    <strong>🏆 Rang :</strong> {{ $rang }}
                </p>
            @endif
        </div>

        <p>
            Malheureusement, vous n'avez pas été admis(e) à ce concours. Nous comprenons que cette nouvelle
            puisse être décevante.
        </p>

        <div class="info-box">
            <p>
                <strong>💡 Conseils pour l'avenir :</strong>
            </p>
            <ul style="margin-left: 20px; margin-bottom: 0; color: #4b5563;">
                <li style="margin-bottom: 8px;">Consultez votre relevé de notes pour identifier vos points forts et axes
                    d'amélioration</li>
                <li style="margin-bottom: 8px;">Ne vous découragez pas - de nombreux candidats réussissent après plusieurs
                    tentatives</li>
                <li style="margin-bottom: 8px;">Préparez-vous pour les prochaines sessions</li>
                <li style="margin-bottom: 8px;">N'hésitez pas à demander conseil à notre équipe</li>
            </ul>
        </div>
    @endif

    <div style="text-align: center;">
        <a href="{{ config('app.frontend_url') }}/candidatures/{{ $candidature->id }}/resultats" class="button">
            Voir mes résultats détaillés
        </a>
    </div>

    @if ($resultat->date_publication)
        <div class="divider"></div>
        <p style="font-size: 14px; color: #6b7280;">
            <strong>📅 Date de publication :</strong>
            {{ \Carbon\Carbon::parse($resultat->date_publication)->format('d/m/Y à H:i') }}
        </p>
    @endif

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        Des questions sur vos résultats ? N'hésitez pas à
        <a href="mailto:support@enrolcm.com" style="color: #059669;">contacter notre support</a>.
    </p>

    @if ($estAdmis)
        <p style="font-size: 14px; color: #6b7280; margin-top: 16px;">
            Encore une fois, toutes nos félicitations ! 🎊
        </p>
    @elseif ($decision === 'LISTE_ATTENTE')
        <p style="font-size: 14px; color: #6b7280; margin-top: 16px;">
            Nous vous souhaitons bonne chance ! 🍀
        </p>
    @else
        <p style="font-size: 14px; color: #6b7280; margin-top: 16px;">
            Nous vous encourageons à persévérer et vous souhaitons bonne chance pour vos futurs projets.
        </p>
    @endif
@endsection

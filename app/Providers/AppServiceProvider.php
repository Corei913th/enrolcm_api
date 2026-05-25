<?php

namespace App\Providers;

use App\Models\Alert;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Document;
use App\Models\Note;
use App\Models\Paiement;
use App\Models\PlanningEpreuve;
use App\Models\ResultatFinal;
use App\Models\ResultatPublication;
use App\Models\Utilisateur;
use App\Observers\AlertObserver;
use App\Observers\CandidatObserver;
use App\Observers\CandidatureObserver;
use App\Observers\DocumentObserver;
use App\Observers\NoteObserver;
use App\Observers\PaiementObserver;
use App\Observers\PlanningEpreuveObserver;
use App\Observers\ResultatFinalObserver;
use App\Observers\ResultatPublicationObserver;
use App\Observers\UtilisateurObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        PlanningEpreuve::observe(PlanningEpreuveObserver::class);
        // Observers
        Utilisateur::observe(UtilisateurObserver::class);
        Candidature::observe(CandidatureObserver::class);
        Candidat::observe(CandidatObserver::class);
        Alert::observe(AlertObserver::class);
        Paiement::observe(PaiementObserver::class);
        Document::observe(DocumentObserver::class);
        ResultatFinal::observe(ResultatFinalObserver::class);
        ResultatPublication::observe(ResultatPublicationObserver::class);
        Note::observe(NoteObserver::class);

        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('CONFIRMATION REQUISE - Vérification de votre email')
                ->view('emails.verify-email', ['url' => $url, 'user' => $notifiable]);
        });
    }
}

<?php

namespace App\Providers;


use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Utilisateur;
use App\Models\Alert;
use App\Observers\CandidatObserver;
use App\Observers\CandidatureObserver;
use App\Observers\UtilisateurObserver;
use App\Observers\AlertObserver;

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
        \App\Models\PlanningEpreuve::observe(\App\Observers\PlanningEpreuveObserver::class);
        // Observers
        Utilisateur::observe(UtilisateurObserver::class);
        Candidature::observe(CandidatureObserver::class);
        Candidat::observe(CandidatObserver::class);
        Alert::observe(AlertObserver::class);
        \App\Models\Paiement::observe(\App\Observers\PaiementObserver::class);
        \App\Models\Document::observe(\App\Observers\DocumentObserver::class);
        \App\Models\ResultatFinal::observe(\App\Observers\ResultatFinalObserver::class);
        \App\Models\ResultatPublication::observe(\App\Observers\ResultatPublicationObserver::class);
        \App\Models\Note::observe(\App\Observers\NoteObserver::class);


        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('CONFIRMATION REQUISE - Vérification de votre email')
                ->view('emails.verify-email', ['url' => $url, 'user' => $notifiable]);
        });
    }
}

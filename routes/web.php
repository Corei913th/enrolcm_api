<?php

use App\Http\Controllers\Test\ReceiptTestController;
use App\Mail\AlertNotificationMail;
use App\Mail\VerifyEmailMail;
use App\Mail\WelcomeMail;
use App\Models\Alert;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// Routes de prévisualisation des emails (à désactiver en production)
if (app()->environment('local', 'development')) {
    Route::get('/email-preview/welcome', function () {
        $utilisateur = Utilisateur::factory()->make([
            'email' => 'candidat@example.com',
        ]);

        $concours = Concours::factory()->make([
            'libelle_concours' => 'Concours d\'entrée à l\'ENSPY 2026',
        ]);

        return new WelcomeMail($utilisateur, null, $concours);
    })->name('email.preview.welcome');

    Route::get('/email-preview/verify', function () {
        $utilisateur = Utilisateur::factory()->make([
            'email' => 'candidat@example.com',
        ]);

        return new VerifyEmailMail($utilisateur);
    })->name('email.preview.verify');

    Route::get('/email-preview/alert', function () {
        $candidat = Candidat::factory()->make([
            'nom_cand' => 'Dupont',
            'prenom_cand' => 'Jean',
        ]);

        $candidat->setRelation('utilisateur', Utilisateur::factory()->make([
            'email' => 'candidat@example.com',
        ]));

        $concours = Concours::factory()->make([
            'libelle_concours' => 'Concours d\'entrée à l\'ENSPY 2026',
            'date_limite_depot' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $candidature = Candidature::factory()->make();
        $candidature->setRelation('concours', $concours);

        $alert = Alert::factory()->make([
            'type' => 'missing_documents',
            'severity' => 'critical',
            'title' => 'Documents requis manquants',
            'message' => 'Certains documents requis n\'ont pas encore été déposés. Veuillez les télécharger avant la date limite.',
        ]);
        $alert->setRelation('candidature', $candidature);

        return new AlertNotificationMail($alert, $candidat);
    })->name('email.preview.alert');
}

/*Route::get('/test/receipt-form', [ReceiptTestController::class, 'showForm'])
    ->name('test.receipt.form');

Route::match(['GET', 'POST'], '/test/generate-receipt', [ReceiptTestController::class, 'apiGenerateReceipt'])
    ->name('test.receipt.generate');*/

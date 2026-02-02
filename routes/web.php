<?php

use App\Http\Controllers\Test\ReceiptTestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// Routes de prévisualisation des emails (à désactiver en production)
if (app()->environment('local', 'development')) {
    Route::get('/email-preview/welcome', function () {
        $utilisateur = \App\Models\Utilisateur::factory()->make([
            'email' => 'candidat@example.com'
        ]);

        $concours = \App\Models\Concours::factory()->make([
            'libelle_concours' => 'Concours d\'entrée à l\'ENSPY 2026'
        ]);

        return new \App\Mail\WelcomeMail($utilisateur, null, $concours);
    })->name('email.preview.welcome');

    Route::get('/email-preview/verify', function () {
        $utilisateur = \App\Models\Utilisateur::factory()->make([
            'email' => 'candidat@example.com'
        ]);

        return new \App\Mail\VerifyEmailMail($utilisateur);
    })->name('email.preview.verify');

    Route::get('/email-preview/alert', function () {
        $candidat = \App\Models\Candidat::factory()->make([
            'nom_cand' => 'Dupont',
            'prenom_cand' => 'Jean',
        ]);

        $candidat->setRelation('utilisateur', \App\Models\Utilisateur::factory()->make([
            'email' => 'candidat@example.com'
        ]));

        $concours = \App\Models\Concours::factory()->make([
            'libelle_concours' => 'Concours d\'entrée à l\'ENSPY 2026',
            'date_limite_depot' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $candidature = \App\Models\Candidature::factory()->make();
        $candidature->setRelation('concours', $concours);

        $alert = \App\Models\Alert::factory()->make([
            'type' => 'missing_documents',
            'severity' => 'critical',
            'title' => 'Documents requis manquants',
            'message' => 'Certains documents requis n\'ont pas encore été déposés. Veuillez les télécharger avant la date limite.',
        ]);
        $alert->setRelation('candidature', $candidature);

        return new \App\Mail\AlertNotificationMail($alert, $candidat);
    })->name('email.preview.alert');
}

/*Route::get('/test/receipt-form', [ReceiptTestController::class, 'showForm'])
    ->name('test.receipt.form');

Route::match(['GET', 'POST'], '/test/generate-receipt', [ReceiptTestController::class, 'apiGenerateReceipt'])
    ->name('test.receipt.generate');*/

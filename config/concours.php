<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Frais d'inscription
    |--------------------------------------------------------------------------
    |
    | Montant des frais d'inscription au concours en FCFA
    |
    */
    'frais_inscription' => env('CONCOURS_FRAIS_INSCRIPTION', 5000),

    /*
    |--------------------------------------------------------------------------
    | Banques acceptées
    |--------------------------------------------------------------------------
    |
    | Liste des banques et services de paiement acceptés au Cameroun
    |
    */
    'banques_acceptees' => [
        'BICEC',
        'UBA',
        'SGBC',
        'Afriland First Bank',
        'Ecobank',
        'SCB Cameroun',
        'Express Union',
        'Orange Money',
        'MTN Mobile Money',
    ],

    /*
    |--------------------------------------------------------------------------
    | Délai de vérification
    |--------------------------------------------------------------------------
    |
    | Délai en heures pour la vérification des reçus
    |
    */
    'delai_verification_heures' => 48,
];

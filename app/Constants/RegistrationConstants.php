<?php

namespace App\Constants;

class RegistrationConstants
{
    // Validation fichiers
    public const ALLOWED_PAYMENT_PROOF_MIMES = ['jpg', 'jpeg', 'png', 'pdf'];

    public const MAX_PAYMENT_PROOF_SIZE = 5 * 1024 * 1024; // 5MB

    // Codes temporaires
    public const TEMP_CODE_PREFIX = 'TEMP-';

    public const TEMP_CODE_LENGTH = 8;

    // Messages
    public const MSG_PAYMENT_PENDING_REVIEW = 'Paiement enregistré avec succès. Votre paiement sera validé par un administrateur dans les plus brefs délais.';

    public const MSG_MANUAL_ENTRY_NOTES = 'Paiement créé avec saisie manuelle - En attente de validation administrative';

    // Étapes du workflow
    public const STEP_ELIGIBILITY = 'eligibility_check';

    public const STEP_PAYMENT_UPLOAD = 'payment_upload';

    public const STEP_MANUAL_ENTRY = 'manual_entry';

    public const STEP_ACCOUNT_CREATION = 'account_creation';
}

<?php declare(strict_types = 1);

// ftm-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Domain\Notification\NotificationService.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.2',
   'data' => 
  array (
    0 => 
    array (
      '456e87279aaf19b2ea383b193f24684d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '582c9281ebe10b7f2cfc7c3507eae8e3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => '__construct',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '705fd3c39bff59fd7a2e4751c623d699' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'sendEmailVerification',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'b86482d816c973bfb1913258f8d22a94' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'sendWelcomeEmail',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '7805e0376fa2c941e58575ea355eef76' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'notifyPaymentPendingReview',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '2d0758beccea0bb5205232e0fcbaafcd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'notifyPaymentVerified',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '0cb9a114cd665f8743b5c9bc606b584c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'notifyPaymentRejected',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '433f90c817a30fe23828f69730da5bf9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'canSendEmail',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '5f04f0b0a66029f6d7a508323c808ed5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'getActiveAlerts',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '20e81a66796d263c286421a3828acd0a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'createPaymentPendingAlert',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '9cdefd3561d17eded54bbe7d074180e9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'createPaymentRejectedAlert',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '2446676cb8d31ad874444810fa89fba4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'notifyDocumentVerified',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'cdb53d7902852f56b5a761637f1e7194' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'notifyDocumentRejected',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '4717c31fa2d75bbf5de7524f586b5bf7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'notifyCandidatureValidated',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'e917ee0e0e9c7889d73c63347101eae3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'notifyCandidatureRejected',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '22fa077385b574bd3425dc599d22e150' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Services\\Domain\\Notification',
         'uses' => 
        array (
          'canalnotification' => 'App\\Enums\\CanalNotification',
          'prioritenotification' => 'App\\Enums\\PrioriteNotification',
          'typenotification' => 'App\\Enums\\TypeNotification',
          'verifyemailmail' => 'App\\Mail\\VerifyEmailMail',
          'welcomemail' => 'App\\Mail\\WelcomeMail',
          'documentverifiedmail' => 'App\\Mail\\DocumentVerifiedMail',
          'documentrejectedmail' => 'App\\Mail\\DocumentRejectedMail',
          'resultspublishedmail' => 'App\\Mail\\ResultsPublishedMail',
          'candidaturevalidatedmail' => 'App\\Mail\\CandidatureValidatedMail',
          'candidaturerejectedmail' => 'App\\Mail\\CandidatureRejectedMail',
          'paymentpendingreviewmail' => 'App\\Mail\\PaymentPendingReviewMail',
          'paymentrejectedmail' => 'App\\Mail\\PaymentRejectedMail',
          'paymentverifiedmail' => 'App\\Mail\\PaymentVerifiedMail',
          'alert' => 'App\\Models\\Alert',
          'candidat' => 'App\\Models\\Candidat',
          'candidature' => 'App\\Models\\Candidature',
          'concours' => 'App\\Models\\Concours',
          'notification' => 'App\\Models\\Notification',
          'paiement' => 'App\\Models\\Paiement',
          'utilisateur' => 'App\\Models\\Utilisateur',
          'activityloggerservice' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
          'collection' => 'Illuminate\\Support\\Collection',
          'mail' => 'Illuminate\\Support\\Facades\\Mail',
          'log' => 'Illuminate\\Support\\Facades\\Log',
        ),
         'className' => 'App\\Services\\Domain\\Notification\\NotificationService',
         'functionName' => 'notifyResultsPublished',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
    ),
    1 => 
    array (
      'D:\\_CORE\\KNOWLEDGE\\COURSES\\IUC_CS2I\\CDWFS\\SN_TRIM-2\\EC06\\enrolcm_api\\app\\Services\\Domain\\Notification\\NotificationService.php' => '2dd50231f6c88c793bc846c34cf91a456a3fd462292536dd5d87494fd690d98f',
    ),
  ),
));
<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Domain\Notification\Notifiers\AlertEmailService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Domain\Notification\Notifiers\AlertEmailService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-803917adc78b5cb30a43ab490faa453539861ad6b3a647623f1b93d790b43158',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Notification/Notifiers/AlertEmailService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Domain\\Notification\\Notifiers',
    'name' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
    'shortName' => 'AlertEmailService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Service d\'envoi d\'emails pour les alertes
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 154,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'EMAIL_ALERT_TYPES' => 
      array (
        'declaringClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'implementingClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'name' => 'EMAIL_ALERT_TYPES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'missing_documents\', \'payment_pending\', \'deadline_approaching\', \'deadline_passed\', \'convocation_available\', \'result_available\', \'profile_incomplete\', \'missing_centers\', \'account_not_verified\']',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 26,
            'startTokenPos' => 48,
            'startFilePos' => 360,
            'endTokenPos' => 77,
            'endFilePos' => 593,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 26,
        'startColumn' => 3,
        'endColumn' => 4,
      ),
      'EMAIL_SEVERITIES' => 
      array (
        'declaringClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'implementingClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'name' => 'EMAIL_SEVERITIES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'critical\', \'warning\']',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 88,
            'startFilePos' => 632,
            'endTokenPos' => 93,
            'endFilePos' => 654,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 3,
        'endColumn' => 59,
      ),
    ),
    'immediateProperties' => 
    array (
      'logger' => 
      array (
        'declaringClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'implementingClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'name' => 'logger',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 50,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'logger' => 
          array (
            'name' => 'logger',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 5,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 30,
        'endLine' => 32,
        'startColumn' => 3,
        'endColumn' => 6,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Notification\\Notifiers',
        'declaringClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'implementingClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'currentClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'aliasName' => NULL,
      ),
      'sendAlertEmail' => 
      array (
        'name' => 'sendAlertEmail',
        'parameters' => 
        array (
          'alert' => 
          array (
            'name' => 'alert',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Alert',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 34,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'candidat' => 
          array (
            'name' => 'candidat',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Candidat',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 48,
            'endColumn' => 65,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Envoyer une notification par email pour une alerte si applicable
 *
 * @param Alert $alert
 * @param Candidat $candidat
 * @return bool True si l\'email a été envoyé, false sinon
 */',
        'startLine' => 41,
        'endLine' => 90,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Notification\\Notifiers',
        'declaringClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'implementingClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'currentClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'aliasName' => NULL,
      ),
      'sendDailySummary' => 
      array (
        'name' => 'sendDailySummary',
        'parameters' => 
        array (
          'candidat' => 
          array (
            'name' => 'candidat',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Candidat',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 98,
            'endLine' => 98,
            'startColumn' => 36,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Envoyer un résumé quotidien par email avec toutes les alertes actives
 *
 * @param Candidat $candidat
 * @return bool True si le résumé a été envoyé, false sinon
 */',
        'startLine' => 98,
        'endLine' => 134,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Notification\\Notifiers',
        'declaringClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'implementingClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'currentClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'aliasName' => NULL,
      ),
      'shouldSendEmail' => 
      array (
        'name' => 'shouldSendEmail',
        'parameters' => 
        array (
          'alert' => 
          array (
            'name' => 'alert',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Alert',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 142,
            'endLine' => 142,
            'startColumn' => 36,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Déterminer si un email doit être envoyé pour cette alerte
 *
 * @param Alert $alert
 * @return bool
 */',
        'startLine' => 142,
        'endLine' => 153,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Notification\\Notifiers',
        'declaringClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'implementingClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'currentClassName' => 'App\\Services\\Domain\\Notification\\Notifiers\\AlertEmailService',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));
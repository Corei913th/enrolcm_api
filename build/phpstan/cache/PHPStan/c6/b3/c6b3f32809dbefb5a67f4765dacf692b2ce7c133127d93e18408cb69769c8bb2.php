<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Domain\Candidature\Validators\CandidatureValidationService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Domain\Candidature\Validators\CandidatureValidationService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-d329bb18c126e214517c07dc158e53e4808ad666ad8c6587fda2b318435dd9e3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Candidature/Validators/CandidatureValidationService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Domain\\Candidature\\Validators',
    'name' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
    'shortName' => 'CandidatureValidationService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 243,
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
    ),
    'immediateProperties' => 
    array (
      'qrCodeService' => 
      array (
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'name' => 'qrCodeService',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Infrastructure\\QrCode\\QrCodeService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'notificationService' => 
      array (
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'name' => 'notificationService',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Domain\\Notification\\NotificationService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 52,
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
          'qrCodeService' => 
          array (
            'name' => 'qrCodeService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Infrastructure\\QrCode\\QrCodeService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 5,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'notificationService' => 
          array (
            'name' => 'notificationService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Domain\\Notification\\NotificationService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 17,
            'endLine' => 17,
            'startColumn' => 5,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 15,
        'endLine' => 18,
        'startColumn' => 3,
        'endColumn' => 6,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'aliasName' => NULL,
      ),
      'checkAndValidateIfReady' => 
      array (
        'name' => 'checkAndValidateIfReady',
        'parameters' => 
        array (
          'candidature' => 
          array (
            'name' => 'candidature',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Candidature',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 43,
            'endColumn' => 66,
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
 * Check if candidature is ready for validation and validate if criteria are met
 * 
 * @param Candidature $candidature
 * @return bool True if candidature was validated, false if not ready
 */',
        'startLine' => 26,
        'endLine' => 65,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'aliasName' => NULL,
      ),
      'validateCandidature' => 
      array (
        'name' => 'validateCandidature',
        'parameters' => 
        array (
          'candidature' => 
          array (
            'name' => 'candidature',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Candidature',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 72,
            'endLine' => 72,
            'startColumn' => 40,
            'endColumn' => 63,
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
 * Validate candidature and generate QR code
 * @param Candidature $candidature
 * @return bool
 */',
        'startLine' => 72,
        'endLine' => 117,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'aliasName' => NULL,
      ),
      'generateNextNumeroCandidature' => 
      array (
        'name' => 'generateNextNumeroCandidature',
        'parameters' => 
        array (
          'candidature' => 
          array (
            'name' => 'candidature',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Candidature',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 122,
            'endLine' => 122,
            'startColumn' => 50,
            'endColumn' => 73,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Generate next available sequence number for this concours/session
 */',
        'startLine' => 122,
        'endLine' => 134,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'aliasName' => NULL,
      ),
      'generateCodeCandDef' => 
      array (
        'name' => 'generateCodeCandDef',
        'parameters' => 
        array (
          'candidature' => 
          array (
            'name' => 'candidature',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Candidature',
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
            'startColumn' => 40,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'numero' => 
          array (
            'name' => 'numero',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
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
            'startColumn' => 66,
            'endColumn' => 79,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Generate definitive candidate code
 * @param Candidature $candidature
 * @param string $numero
 * @return string
 */',
        'startLine' => 142,
        'endLine' => 146,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'aliasName' => NULL,
      ),
      'generateQrCode' => 
      array (
        'name' => 'generateQrCode',
        'parameters' => 
        array (
          'candidature' => 
          array (
            'name' => 'candidature',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Candidature',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 155,
            'endLine' => 155,
            'startColumn' => 35,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'codeCandDef' => 
          array (
            'name' => 'codeCandDef',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 155,
            'endLine' => 155,
            'startColumn' => 61,
            'endColumn' => 79,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'numeroCandidature' => 
          array (
            'name' => 'numeroCandidature',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 155,
            'endLine' => 155,
            'startColumn' => 82,
            'endColumn' => 99,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Generate QR code for candidature
 * @param Candidature $candidature
 * @param string $codeCandDef
 * @param int|string $numeroCandidature
 * @return string Base64 encoded QR code
 */',
        'startLine' => 155,
        'endLine' => 167,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'aliasName' => NULL,
      ),
      'dismissPaymentPendingAlert' => 
      array (
        'name' => 'dismissPaymentPendingAlert',
        'parameters' => 
        array (
          'candidature' => 
          array (
            'name' => 'candidature',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Candidature',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 174,
            'endLine' => 174,
            'startColumn' => 47,
            'endColumn' => 70,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Dismiss payment pending alert
 * 
 * @param Candidature $candidature
 */',
        'startLine' => 174,
        'endLine' => 183,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'aliasName' => NULL,
      ),
      'sendValidationNotifications' => 
      array (
        'name' => 'sendValidationNotifications',
        'parameters' => 
        array (
          'candidature' => 
          array (
            'name' => 'candidature',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Candidature',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 189,
            'endLine' => 189,
            'startColumn' => 48,
            'endColumn' => 71,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Send validation notifications
 * @param Candidature $candidature
 */',
        'startLine' => 189,
        'endLine' => 242,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
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
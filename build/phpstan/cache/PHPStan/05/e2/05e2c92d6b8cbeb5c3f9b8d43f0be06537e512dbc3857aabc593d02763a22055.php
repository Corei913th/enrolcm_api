<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Domain\Candidature\DocumentService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Domain\Candidature\DocumentService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-3855c093f8a6c6f2023ff9d59a1a8775eb491637ac389fcce06496bed4245232',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Candidature/DocumentService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Domain\\Candidature',
    'name' => 'App\\Services\\Domain\\Candidature\\DocumentService',
    'shortName' => 'DocumentService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 31,
    'endLine' => 607,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Traits\\HasSmartCache',
      1 => 'App\\Traits\\HasAdvancedSearch',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'logger' => 
      array (
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
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
        'startLine' => 36,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 50,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'candidatureValidationService' => 
      array (
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'name' => 'candidatureValidationService',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 79,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'notificationService' => 
      array (
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'name' => 'notificationService',
        'modifiers' => 132,
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
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 61,
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
            'startLine' => 36,
            'endLine' => 36,
            'startColumn' => 5,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'candidatureValidationService' => 
          array (
            'name' => 'candidatureValidationService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Domain\\Candidature\\Validators\\CandidatureValidationService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 37,
            'endLine' => 37,
            'startColumn' => 5,
            'endColumn' => 79,
            'parameterIndex' => 1,
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
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 5,
            'endColumn' => 61,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 35,
        'endLine' => 39,
        'startColumn' => 3,
        'endColumn' => 6,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'getModelTags' => 
      array (
        'name' => 'getModelTags',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 41,
        'endLine' => 44,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'createDocumentRequis' => 
      array (
        'name' => 'createDocumentRequis',
        'parameters' => 
        array (
          'dto' => 
          array (
            'name' => 'dto',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\DTOs\\Documents\\CreateDocumentRequisDTO',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 54,
            'endLine' => 54,
            'startColumn' => 40,
            'endColumn' => 67,
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
            'name' => 'App\\Models\\DocumentRequis',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Crée un document requis pour un concours donné.
 * @param CreateDocumentRequisDTO $dto Données validées du document requis
 *
 * @return DocumentRequis Le document requis nouvellement créé
 *
 * @throws \\Throwable En cas d\'échec de la transaction
 */',
        'startLine' => 54,
        'endLine' => 78,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'updateDocumentRequis' => 
      array (
        'name' => 'updateDocumentRequis',
        'parameters' => 
        array (
          'documentRequis' => 
          array (
            'name' => 'documentRequis',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\DocumentRequis',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 91,
            'endLine' => 91,
            'startColumn' => 40,
            'endColumn' => 69,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'dto' => 
          array (
            'name' => 'dto',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\DTOs\\Documents\\UpdateDocumentRequisDTO',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 91,
            'endLine' => 91,
            'startColumn' => 72,
            'endColumn' => 99,
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
            'name' => 'App\\Models\\DocumentRequis',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Met à jour un document requis existant.
 * @param DocumentRequis $documentRequis Document requis à modifier
 * @param UpdateDocumentRequisDTO $dto Données de mise à jour validées
 *
 * @return DocumentRequis Le document requis mis à jour
 *
 * @throws \\DomainException Si le document est déjà utilisé
 * @throws \\Throwable En cas d\'échec de transaction
 */',
        'startLine' => 91,
        'endLine' => 111,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'deleteDocumentRequis' => 
      array (
        'name' => 'deleteDocumentRequis',
        'parameters' => 
        array (
          'documentRequis' => 
          array (
            'name' => 'documentRequis',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\DocumentRequis',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 121,
            'endLine' => 121,
            'startColumn' => 40,
            'endColumn' => 69,
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
 * Supprime définitivement un document requis.
 * @param DocumentRequis $documentRequis Document requis à supprimer
 *
 * @return void
 *
 * @throws \\DomainException Si des documents sont déjà associés
 */',
        'startLine' => 121,
        'endLine' => 130,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'submitDocument' => 
      array (
        'name' => 'submitDocument',
        'parameters' => 
        array (
          'dto' => 
          array (
            'name' => 'dto',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\DTOs\\Documents\\SubmitDocumentDTO',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 144,
            'endLine' => 144,
            'startColumn' => 34,
            'endColumn' => 55,
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
            'name' => 'App\\Models\\Document',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Soumet ou remplace un document pour une candidature donnée.
 * Les documents sont automatiquement validés par défaut.
 * 
 * @param SubmitDocumentDTO $dto Données de soumission validées
 *
 * @return Document Le document soumis ou mis à jour
 *
 * @throws \\DomainException Si les règles métier sont violées
 * @throws \\Throwable En cas d\'échec de transaction
 */',
        'startLine' => 144,
        'endLine' => 202,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'validateDocument' => 
      array (
        'name' => 'validateDocument',
        'parameters' => 
        array (
          'document' => 
          array (
            'name' => 'document',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Document',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 218,
            'endLine' => 218,
            'startColumn' => 5,
            'endColumn' => 22,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'dto' => 
          array (
            'name' => 'dto',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\DTOs\\Documents\\ValidateDocumentDTO',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 219,
            'endLine' => 219,
            'startColumn' => 5,
            'endColumn' => 28,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'userId' => 
          array (
            'name' => 'userId',
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
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 5,
            'endColumn' => 18,
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
            'name' => 'App\\Models\\Document',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Valide ou rejette un document soumis.
 * Si un document est rejeté, la candidature est révoquée (retour à EN_COURS).
 * 
 * @param Document $document Document à valider ou rejeter
 * @param ValidateDocumentDTO $dto Données de validation
 * @param int $userId ID de l\'utilisateur effectuant la validation
 *
 * @return Document Le document mis à jour
 *
 * @throws \\DomainException Si le document n\'est pas en attente
 * @throws \\Throwable En cas d\'échec de transaction
 */',
        'startLine' => 217,
        'endLine' => 286,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'areDocumentsComplete' => 
      array (
        'name' => 'areDocumentsComplete',
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
            'startLine' => 296,
            'endLine' => 296,
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
 * Vérifie si tous les documents obligatoires d\'une candidature sont présents et validés.
 * @param Candidature $candidature Candidature à vérifier
 * @return bool True si tous les documents obligatoires sont validés
 */',
        'startLine' => 296,
        'endLine' => 305,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'assertDocumentBelongsToConcours' => 
      array (
        'name' => 'assertDocumentBelongsToConcours',
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
            'startLine' => 319,
            'endLine' => 319,
            'startColumn' => 5,
            'endColumn' => 28,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'documentRequis' => 
          array (
            'name' => 'documentRequis',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\DocumentRequis',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 320,
            'endLine' => 320,
            'startColumn' => 5,
            'endColumn' => 34,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie qu\'un document requis appartient bien au concours de la candidature.
 * @param Candidature $candidature Candidature concernée
 * @param DocumentRequis $documentRequis Document requis ciblé
 *
 * @return void
 *
 * @throws \\DomainException Si les concours ne correspondent pas
 */',
        'startLine' => 318,
        'endLine' => 327,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'assertFileIsValid' => 
      array (
        'name' => 'assertFileIsValid',
        'parameters' => 
        array (
          'file' => 
          array (
            'name' => 'file',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\UploadedFile',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 339,
            'endLine' => 339,
            'startColumn' => 5,
            'endColumn' => 22,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'documentRequis' => 
          array (
            'name' => 'documentRequis',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\DocumentRequis',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 340,
            'endLine' => 340,
            'startColumn' => 5,
            'endColumn' => 34,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Valide un fichier uploadé selon les contraintes du document requis.
 * @param UploadedFile $file Fichier soumis
 * @param DocumentRequis $documentRequis Contraintes applicables
 *
 * @return void
 *
 * @throws \\DomainException Si le fichier est invalide
 */',
        'startLine' => 338,
        'endLine' => 353,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'storeFile' => 
      array (
        'name' => 'storeFile',
        'parameters' => 
        array (
          'file' => 
          array (
            'name' => 'file',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\UploadedFile',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 363,
            'endLine' => 363,
            'startColumn' => 30,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Stocke physiquement un fichier uploadé.
 * @param UploadedFile $file Fichier à stocker
 *
 * @return string Chemin public du fichier stocké
 *
 * @throws \\RuntimeException En cas d\'échec de stockage
 */',
        'startLine' => 363,
        'endLine' => 370,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'getDocumentsRequisForConcours' => 
      array (
        'name' => 'getDocumentsRequisForConcours',
        'parameters' => 
        array (
          'concoursId' => 
          array (
            'name' => 'concoursId',
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
            'startLine' => 380,
            'endLine' => 380,
            'startColumn' => 49,
            'endColumn' => 66,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Récupère les documents requis pour un concours donné.
 *
 * @param string $concoursId ID du concours
 *
 * @return \\Illuminate\\Database\\Eloquent\\Collection Liste des documents requis
 */',
        'startLine' => 380,
        'endLine' => 385,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'getDocumentsEnAttente' => 
      array (
        'name' => 'getDocumentsEnAttente',
        'parameters' => 
        array (
          'perPage' => 
          array (
            'name' => 'perPage',
            'default' => 
            array (
              'code' => '20',
              'attributes' => 
              array (
                'startLine' => 393,
                'endLine' => 393,
                'startTokenPos' => 1827,
                'startFilePos' => 12762,
                'endTokenPos' => 1827,
                'endFilePos' => 12763,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 393,
            'endLine' => 393,
            'startColumn' => 41,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'concoursId' => 
          array (
            'name' => 'concoursId',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 393,
                'endLine' => 393,
                'startTokenPos' => 1837,
                'startFilePos' => 12788,
                'endTokenPos' => 1837,
                'endFilePos' => 12791,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 393,
            'endLine' => 393,
            'startColumn' => 60,
            'endColumn' => 85,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Pagination\\LengthAwarePaginator',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get pending documents for validation (OPTIMISÉ avec cache et colonnes spécifiques).
 *
 * @param integer $perPage
 * @return LengthAwarePaginator
 */',
        'startLine' => 393,
        'endLine' => 432,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'getAllForValidation' => 
      array (
        'name' => 'getAllForValidation',
        'parameters' => 
        array (
          'perPage' => 
          array (
            'name' => 'perPage',
            'default' => 
            array (
              'code' => '100',
              'attributes' => 
              array (
                'startLine' => 441,
                'endLine' => 441,
                'startTokenPos' => 2069,
                'startFilePos' => 14369,
                'endTokenPos' => 2069,
                'endFilePos' => 14371,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 441,
            'endLine' => 441,
            'startColumn' => 39,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'concoursId' => 
          array (
            'name' => 'concoursId',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 441,
                'endLine' => 441,
                'startTokenPos' => 2079,
                'startFilePos' => 14396,
                'endTokenPos' => 2079,
                'endFilePos' => 14399,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 441,
            'endLine' => 441,
            'startColumn' => 59,
            'endColumn' => 84,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Pagination\\LengthAwarePaginator',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all documents for validation (not just pending)
 *
 * @param integer $perPage
 * @param string|null $concoursId
 * @return LengthAwarePaginator
 */',
        'startLine' => 441,
        'endLine' => 480,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'getValidationStats' => 
      array (
        'name' => 'getValidationStats',
        'parameters' => 
        array (
          'concoursId' => 
          array (
            'name' => 'concoursId',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 488,
                'endLine' => 488,
                'startTokenPos' => 2302,
                'startFilePos' => 15869,
                'endTokenPos' => 2302,
                'endFilePos' => 15872,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 488,
            'endLine' => 488,
            'startColumn' => 38,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get validation statistics
 *
 * @param string|null $concoursId
 * @return array
 */',
        'startLine' => 488,
        'endLine' => 509,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'getDocumentById' => 
      array (
        'name' => 'getDocumentById',
        'parameters' => 
        array (
          'documentId' => 
          array (
            'name' => 'documentId',
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
            'startLine' => 522,
            'endLine' => 522,
            'startColumn' => 35,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'relations' => 
          array (
            'name' => 'relations',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 522,
                'endLine' => 522,
                'startTokenPos' => 2509,
                'startFilePos' => 16929,
                'endTokenPos' => 2510,
                'endFilePos' => 16930,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 522,
            'endLine' => 522,
            'startColumn' => 55,
            'endColumn' => 75,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\Document',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Récupère un document par son ID avec les relations optionnelles.
 *
 * @param string $documentId ID du document
 * @param array $relations Relations à charger
 *
 * @return Document
 *
 * @throws \\DomainException Si le document n\'est pas trouvé
 */',
        'startLine' => 522,
        'endLine' => 529,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'getDocumentRequisByIdAndConcours' => 
      array (
        'name' => 'getDocumentRequisByIdAndConcours',
        'parameters' => 
        array (
          'documentId' => 
          array (
            'name' => 'documentId',
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
            'startLine' => 541,
            'endLine' => 541,
            'startColumn' => 52,
            'endColumn' => 69,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'concoursId' => 
          array (
            'name' => 'concoursId',
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
            'startLine' => 541,
            'endLine' => 541,
            'startColumn' => 72,
            'endColumn' => 89,
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
            'name' => 'App\\Models\\DocumentRequis',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Récupère un document requis par son ID et le concours associé.
 *
 * @param string $documentId ID du document requis
 * @param string $concoursId ID du concours
 *
 * @return DocumentRequis
 *
 * @throws \\DomainException Si le document n\'est pas trouvé
 */',
        'startLine' => 541,
        'endLine' => 550,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'getRequiredDocumentsStatusForCandidature' => 
      array (
        'name' => 'getRequiredDocumentsStatusForCandidature',
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
            'startLine' => 558,
            'endLine' => 558,
            'startColumn' => 60,
            'endColumn' => 83,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get required documents status for candidature
 *
 * @param Candidature $candidature
 * @return array[]
 */',
        'startLine' => 558,
        'endLine' => 583,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'aliasName' => NULL,
      ),
      'getValidatedPhotoPath' => 
      array (
        'name' => 'getValidatedPhotoPath',
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
            'startLine' => 591,
            'endLine' => 591,
            'startColumn' => 41,
            'endColumn' => 64,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get validated photo path for a candidature
 *
 * @param Candidature $candidature
 * @return string|null
 */',
        'startLine' => 591,
        'endLine' => 606,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\DocumentService',
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
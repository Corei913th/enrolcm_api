<?php declare(strict_types = 1);

// osfsl-D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Candidature/Checkers/EligibilityChecker.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Domain\Candidature\Checkers\EligibilityChecker
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-c063b356b9d8cc4947a63884ee80fc3adabfe11fc623e5ca2504d957680128ae-8.2.30-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Candidature/Checkers/EligibilityChecker.php',
      ),
    ),
    'namespace' => 'App\\Services\\Domain\\Candidature\\Checkers',
    'name' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
    'shortName' => 'EligibilityChecker',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 335,
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
      'documentService' => 
      array (
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'name' => 'documentService',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Domain\\Candidature\\DocumentService',
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
        'endColumn' => 53,
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
          'documentService' => 
          array (
            'name' => 'documentService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Domain\\Candidature\\DocumentService',
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
            'endColumn' => 53,
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
        'startLine' => 16,
        'endLine' => 18,
        'startColumn' => 3,
        'endColumn' => 6,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Checkers',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'aliasName' => NULL,
      ),
      'checkFullEligibility' => 
      array (
        'name' => 'checkFullEligibility',
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
            'startLine' => 25,
            'endLine' => 25,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Check complete eligibility of a candidature
 * @param Candidature $candidature
 * @return array [\'eligible\' => bool, \'reasons\' => array]
 */',
        'startLine' => 25,
        'endLine' => 64,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Checkers',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'aliasName' => NULL,
      ),
      'checkPreRegistrationEligibility' => 
      array (
        'name' => 'checkPreRegistrationEligibility',
        'parameters' => 
        array (
          'eligibilityData' => 
          array (
            'name' => 'eligibilityData',
            'default' => NULL,
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
            'startLine' => 74,
            'endLine' => 74,
            'startColumn' => 51,
            'endColumn' => 72,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'spec' => 
          array (
            'name' => 'spec',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\SpecConcours',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 74,
            'endLine' => 74,
            'startColumn' => 75,
            'endColumn' => 92,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie l\'éligibilité avant l\'inscription (sans Candidat existant)
 * Utilisé pour la validation publique avant création de compte
 * 
 * @param array $eligibilityData [\'date_naissance\' => string, \'serie_bac\' => string, \'nationalite\' => string]
 * @param SpecConcours $spec
 * @return array [\'eligible\' => bool, \'reasons\' => array]
 */',
        'startLine' => 74,
        'endLine' => 125,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Checkers',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'aliasName' => NULL,
      ),
      'checkAcademicEligibility' => 
      array (
        'name' => 'checkAcademicEligibility',
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
            'startLine' => 134,
            'endLine' => 134,
            'startColumn' => 44,
            'endColumn' => 61,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'spec' => 
          array (
            'name' => 'spec',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\SpecConcours',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 134,
            'endLine' => 134,
            'startColumn' => 64,
            'endColumn' => 81,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie les critères académiques (âge, bac, nationalité)
 * 
 * @param Candidat $candidat
 * @param SpecConcours $spec
 * @return array [\'eligible\' => bool, \'reasons\' => array]
 */',
        'startLine' => 134,
        'endLine' => 189,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Checkers',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'aliasName' => NULL,
      ),
      'checkPaymentStatus' => 
      array (
        'name' => 'checkPaymentStatus',
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
            'startLine' => 197,
            'endLine' => 197,
            'startColumn' => 38,
            'endColumn' => 61,
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
 * Vérifie le statut du paiement
 * 
 * @param Candidature $candidature
 * @return array [\'valid\' => bool, \'status\' => string, \'reason\' => string]
 */',
        'startLine' => 197,
        'endLine' => 230,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Checkers',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'aliasName' => NULL,
      ),
      'checkDocumentsStatus' => 
      array (
        'name' => 'checkDocumentsStatus',
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
            'startLine' => 240,
            'endLine' => 240,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie le statut des documents
 * Utilise DocumentService.areDocumentsComplete() pour vérifier que tous les documents
 * obligatoires sont soumis et validés (statut VALIDE)
 * 
 * @param Candidature $candidature
 * @return array [\'valid\' => bool, \'missing\' => array, \'pending\' => array, \'rejected\' => array]
 */',
        'startLine' => 240,
        'endLine' => 278,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Checkers',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'aliasName' => NULL,
      ),
      'canGenerateConvocation' => 
      array (
        'name' => 'canGenerateConvocation',
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
            'startLine' => 285,
            'endLine' => 285,
            'startColumn' => 42,
            'endColumn' => 65,
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
 * Vérifie si une candidature peut générer une convocation
 * @param Candidature $candidature
 * @return array [\'eligible\' => bool, \'reasons\' => array]
 */',
        'startLine' => 285,
        'endLine' => 323,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Checkers',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'aliasName' => NULL,
      ),
      'canGenerateFicheInscription' => 
      array (
        'name' => 'canGenerateFicheInscription',
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
            'startLine' => 331,
            'endLine' => 331,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifie si une candidature peut générer une fiche d\'inscription
 * 
 * @param Candidature $candidature
 * @return array [\'can_generate\' => bool, \'reasons\' => array]
 */',
        'startLine' => 331,
        'endLine' => 334,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Candidature\\Checkers',
        'declaringClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
        'currentClassName' => 'App\\Services\\Domain\\Candidature\\Checkers\\EligibilityChecker',
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
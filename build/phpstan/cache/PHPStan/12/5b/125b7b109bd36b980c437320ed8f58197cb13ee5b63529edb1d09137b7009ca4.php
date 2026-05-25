<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Infrastructure\Pdf\EmargementPdfService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Infrastructure\Pdf\EmargementPdfService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-5f702b7a968bf5d10b44cd3e2bb0f844327d597c9328371f41e23c650e4719e8',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Infrastructure/Pdf/EmargementPdfService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Infrastructure\\Pdf',
    'name' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
    'shortName' => 'EmargementPdfService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 130,
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
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 12,
        'endLine' => 12,
        'startColumn' => 3,
        'endColumn' => 34,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'aliasName' => NULL,
      ),
      'genererListeEmargement' => 
      array (
        'name' => 'genererListeEmargement',
        'parameters' => 
        array (
          'salleId' => 
          array (
            'name' => 'salleId',
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
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 42,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'planningEpreuveId' => 
          array (
            'name' => 'planningEpreuveId',
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
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 59,
            'endColumn' => 83,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Générer une liste d\'émargement pour une salle et une épreuve
 * 
 * @param string $salleId
 * @param string $planningEpreuveId
 * @return \\Spatie\\LaravelPdf\\PdfBuilder
 */',
        'startLine' => 21,
        'endLine' => 52,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'aliasName' => NULL,
      ),
      'genererListesEmargementCentre' => 
      array (
        'name' => 'genererListesEmargementCentre',
        'parameters' => 
        array (
          'centreId' => 
          array (
            'name' => 'centreId',
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
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 49,
            'endColumn' => 64,
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
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 67,
            'endColumn' => 84,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Générer toutes les listes d\'émargement pour un centre
 * 
 * @param string $centreId
 * @param string $concoursId
 * @return \\Spatie\\LaravelPdf\\PdfBuilder
 */',
        'startLine' => 61,
        'endLine' => 95,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'aliasName' => NULL,
      ),
      'genererFeuilleEmargementVierge' => 
      array (
        'name' => 'genererFeuilleEmargementVierge',
        'parameters' => 
        array (
          'salleId' => 
          array (
            'name' => 'salleId',
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
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 50,
            'endColumn' => 64,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'planningEpreuveId' => 
          array (
            'name' => 'planningEpreuveId',
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
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 67,
            'endColumn' => 91,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'nombreLignes' => 
          array (
            'name' => 'nombreLignes',
            'default' => 
            array (
              'code' => '50',
              'attributes' => 
              array (
                'startLine' => 105,
                'endLine' => 105,
                'startTokenPos' => 600,
                'startFilePos' => 3278,
                'endTokenPos' => 600,
                'endFilePos' => 3279,
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
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 94,
            'endColumn' => 115,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Générer une feuille d\'émargement vierge (sans noms)
 * 
 * @param string $salleId
 * @param string $planningEpreuveId
 * @param int $nombreLignes
 * @return \\Spatie\\LaravelPdf\\PdfBuilder
 */',
        'startLine' => 105,
        'endLine' => 129,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\EmargementPdfService',
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
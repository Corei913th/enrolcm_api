<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Infrastructure\Pdf\RelevePdfService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Infrastructure\Pdf\RelevePdfService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-5baec9480de7dc11db5d616703c02166e045205ffebc9630897c7af1fe73cbd9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Infrastructure/Pdf/RelevePdfService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Infrastructure\\Pdf',
    'name' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
    'shortName' => 'RelevePdfService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 8,
    'endLine' => 115,
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
        'startLine' => 10,
        'endLine' => 10,
        'startColumn' => 3,
        'endColumn' => 34,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
        'aliasName' => NULL,
      ),
      'genererReleveNotes' => 
      array (
        'name' => 'genererReleveNotes',
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
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 38,
            'endColumn' => 61,
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
 * Générer le relevé de notes d\'un candidat
 * 
 * @param Candidature $candidature
 * @return \\Spatie\\LaravelPdf\\PdfBuilder|null
 */',
        'startLine' => 18,
        'endLine' => 59,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
        'aliasName' => NULL,
      ),
      'genererRelevesGroupes' => 
      array (
        'name' => 'genererRelevesGroupes',
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
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 41,
            'endColumn' => 58,
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
 * Générer les relevés de notes pour tous les candidats admis d\'un concours
 * 
 * @param string $concoursId
 * @return \\Spatie\\LaravelPdf\\PdfBuilder
 */',
        'startLine' => 67,
        'endLine' => 114,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\RelevePdfService',
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
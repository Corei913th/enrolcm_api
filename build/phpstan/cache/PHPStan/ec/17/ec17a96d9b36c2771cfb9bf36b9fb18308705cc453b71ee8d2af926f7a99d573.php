<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Infrastructure\Pdf\ConvocationPdfService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Infrastructure\Pdf\ConvocationPdfService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-a7df2d80e7aea94c113cd0240903f8d6e1bc8cc104492ddfa013f7bd4eb38996',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Infrastructure/Pdf/ConvocationPdfService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Infrastructure\\Pdf',
    'name' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
    'shortName' => 'ConvocationPdfService',
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
    'endLine' => 129,
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
      'ecoleDocumentService' => 
      array (
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'name' => 'ecoleDocumentService',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 11,
        'endLine' => 11,
        'startColumn' => 5,
        'endColumn' => 66,
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
          'ecoleDocumentService' => 
          array (
            'name' => 'ecoleDocumentService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 11,
            'endLine' => 11,
            'startColumn' => 5,
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
        'docComment' => NULL,
        'startLine' => 10,
        'endLine' => 12,
        'startColumn' => 3,
        'endColumn' => 6,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'aliasName' => NULL,
      ),
      'genererConvocation' => 
      array (
        'name' => 'genererConvocation',
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
            'startLine' => 20,
            'endLine' => 20,
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
 * Générer une convocation individuelle pour un candidat
 * 
 * @param Candidature $candidature
 * @return \\Spatie\\LaravelPdf\\PdfBuilder
 */',
        'startLine' => 20,
        'endLine' => 56,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'aliasName' => NULL,
      ),
      'genererConvocationsGroupees' => 
      array (
        'name' => 'genererConvocationsGroupees',
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
            'startLine' => 64,
            'endLine' => 64,
            'startColumn' => 47,
            'endColumn' => 64,
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
 * Générer les convocations pour tous les candidats d\'un concours
 * 
 * @param string $concoursId
 * @return \\Spatie\\LaravelPdf\\PdfBuilder
 */',
        'startLine' => 64,
        'endLine' => 90,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'aliasName' => NULL,
      ),
      'genererConvocationsParCentre' => 
      array (
        'name' => 'genererConvocationsParCentre',
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
            'startLine' => 99,
            'endLine' => 99,
            'startColumn' => 48,
            'endColumn' => 63,
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
            'startLine' => 99,
            'endLine' => 99,
            'startColumn' => 66,
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
 * Générer les convocations par centre d\'examen
 * 
 * @param string $centreId
 * @param string $concoursId
 * @return \\Spatie\\LaravelPdf\\PdfBuilder
 */',
        'startLine' => 99,
        'endLine' => 128,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\ConvocationPdfService',
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
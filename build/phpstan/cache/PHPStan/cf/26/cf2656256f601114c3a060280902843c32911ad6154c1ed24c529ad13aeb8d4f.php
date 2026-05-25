<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Infrastructure\Pdf\EcoleDocumentPdfService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Infrastructure\Pdf\EcoleDocumentPdfService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-a1d3c4741b12473b9504a302c956d742990a3af81dd20c3bf1d2f99928f5d37c',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Infrastructure/Pdf/EcoleDocumentPdfService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Infrastructure\\Pdf',
    'name' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
    'shortName' => 'EcoleDocumentPdfService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 100,
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
      'generateOfficialHeader' => 
      array (
        'name' => 'generateOfficialHeader',
        'parameters' => 
        array (
          'ecole' => 
          array (
            'name' => 'ecole',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Ecole',
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
            'startColumn' => 42,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'contentOnly' => 
          array (
            'name' => 'contentOnly',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 47,
                'startFilePos' => 508,
                'endTokenPos' => 47,
                'endFilePos' => 511,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
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
            'startColumn' => 56,
            'endColumn' => 79,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Générer l\'en-tête officielle d\'une école
 * 
 * @param Ecole $ecole École concernée
 * @param bool $contentOnly Si true, retourne uniquement le contenu sans balises HTML complètes
 * @return string HTML de l\'en-tête rendu
 */',
        'startLine' => 18,
        'endLine' => 28,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'aliasName' => NULL,
      ),
      'generateDocument' => 
      array (
        'name' => 'generateDocument',
        'parameters' => 
        array (
          'ecole' => 
          array (
            'name' => 'ecole',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Ecole',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 36,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'title' => 
          array (
            'name' => 'title',
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
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 50,
            'endColumn' => 62,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'content' => 
          array (
            'name' => 'content',
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
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 65,
            'endColumn' => 79,
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
            'name' => 'Spatie\\LaravelPdf\\PdfBuilder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Générer un document PDF avec en-tête officielle
 * 
 * @param Ecole $ecole École concernée
 * @param string $title Titre du document
 * @param string $content Contenu HTML du document
 * @return \\Spatie\\LaravelPdf\\PdfBuilder Document PDF généré
 */',
        'startLine' => 38,
        'endLine' => 48,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'aliasName' => NULL,
      ),
      'generateAttestation' => 
      array (
        'name' => 'generateAttestation',
        'parameters' => 
        array (
          'ecole' => 
          array (
            'name' => 'ecole',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Ecole',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 57,
            'endLine' => 57,
            'startColumn' => 39,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 57,
            'endLine' => 57,
            'startColumn' => 53,
            'endColumn' => 63,
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
            'name' => 'Spatie\\LaravelPdf\\PdfBuilder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Générer une attestation PDF
 * 
 * @param Ecole $ecole École concernée
 * @param array $data Données de l\'attestation (nom, prénom, etc.)
 * @return \\Spatie\\LaravelPdf\\PdfBuilder Document PDF d\'attestation
 */',
        'startLine' => 57,
        'endLine' => 64,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'aliasName' => NULL,
      ),
      'generateReleveNotes' => 
      array (
        'name' => 'generateReleveNotes',
        'parameters' => 
        array (
          'ecole' => 
          array (
            'name' => 'ecole',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Ecole',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 73,
            'endLine' => 73,
            'startColumn' => 39,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 73,
            'endLine' => 73,
            'startColumn' => 53,
            'endColumn' => 63,
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
            'name' => 'Spatie\\LaravelPdf\\PdfBuilder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Générer un relevé de notes PDF
 * 
 * @param Ecole $ecole École concernée
 * @param array $data Données du relevé (notes, matières, etc.)
 * @return \\Spatie\\LaravelPdf\\PdfBuilder Document PDF de relevé de notes
 */',
        'startLine' => 73,
        'endLine' => 80,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'aliasName' => NULL,
      ),
      'generateAdministrativeDocument' => 
      array (
        'name' => 'generateAdministrativeDocument',
        'parameters' => 
        array (
          'ecole' => 
          array (
            'name' => 'ecole',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Ecole',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 90,
            'endLine' => 90,
            'startColumn' => 50,
            'endColumn' => 61,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'title' => 
          array (
            'name' => 'title',
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
            'startLine' => 90,
            'endLine' => 90,
            'startColumn' => 64,
            'endColumn' => 76,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 90,
            'endLine' => 90,
            'startColumn' => 79,
            'endColumn' => 89,
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
            'name' => 'Spatie\\LaravelPdf\\PdfBuilder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Générer un document administratif générique
 * 
 * @param Ecole $ecole École concernée
 * @param string $title Titre du document
 * @param array $data Données du document
 * @return \\Spatie\\LaravelPdf\\PdfBuilder Document PDF administratif
 */',
        'startLine' => 90,
        'endLine' => 99,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Pdf',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Pdf\\EcoleDocumentPdfService',
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
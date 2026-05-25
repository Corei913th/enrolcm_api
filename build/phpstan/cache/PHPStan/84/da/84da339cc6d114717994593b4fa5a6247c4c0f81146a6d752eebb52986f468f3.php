<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Infrastructure\Storage\EcoleFileStorageService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Infrastructure\Storage\EcoleFileStorageService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-2dcbbc352ff1ae67ad25c2121af51cfbdf2da437bdb16c9a9c4fdf3b110856f5',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Infrastructure/Storage/EcoleFileStorageService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Infrastructure\\Storage',
    'name' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
    'shortName' => 'EcoleFileStorageService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Service de gestion des fichiers des écoles
 * 
 * Service technique spécialisé pour l\'upload, la validation et la suppression 
 * des fichiers des écoles (logo, emblème, header_frame).
 * 
 * Responsabilités :
 * - Upload de fichiers avec validation
 * - Suppression de fichiers
 * - Génération de noms de fichiers uniques
 * - Gestion du stockage des fichiers
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 23,
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
      'ALLOWED_TYPES' => 
      array (
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'name' => 'ALLOWED_TYPES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'logo\' => [\'image/jpeg\', \'image/png\', \'image/jpg\', \'image/svg+xml\'], \'embleme\' => [\'image/jpeg\', \'image/png\', \'image/jpg\', \'image/svg+xml\'], \'header_frame\' => [\'image/jpeg\', \'image/png\', \'image/jpg\']]',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 32,
            'startTokenPos' => 50,
            'startFilePos' => 723,
            'endTokenPos' => 103,
            'endFilePos' => 940,
          ),
        ),
        'docComment' => '/**
 * Types de fichiers autorisés pour les écoles
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 32,
        'startColumn' => 3,
        'endColumn' => 4,
      ),
      'MAX_SIZES' => 
      array (
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'name' => 'MAX_SIZES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[
    \'logo\' => 2048,
    // 2MB
    \'embleme\' => 2048,
    // 2MB
    \'header_frame\' => 5120,
]',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 41,
            'startTokenPos' => 116,
            'startFilePos' => 1013,
            'endTokenPos' => 145,
            'endFilePos' => 1120,
          ),
        ),
        'docComment' => '/**
 * Tailles maximales en Ko
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 41,
        'startColumn' => 3,
        'endColumn' => 4,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'uploadFile' => 
      array (
        'name' => 'uploadFile',
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
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 30,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 44,
            'endColumn' => 61,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'type' => 
          array (
            'name' => 'type',
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
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 64,
            'endColumn' => 75,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Upload un fichier pour une école
 * 
 * @param Ecole $ecole École concernée
 * @param UploadedFile $file Fichier uploadé
 * @param string $type Type de fichier: \'logo\', \'embleme\', \'header_frame\'
 * @return array Informations du fichier uploadé
 * @throws \\InvalidArgumentException Si le fichier n\'est pas valide
 */',
        'startLine' => 52,
        'endLine' => 96,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'aliasName' => NULL,
      ),
      'deleteFile' => 
      array (
        'name' => 'deleteFile',
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
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 30,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'type' => 
          array (
            'name' => 'type',
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
            'startColumn' => 44,
            'endColumn' => 55,
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
 * Supprimer un fichier d\'une école
 * 
 * @param Ecole $ecole École concernée
 * @param string $type Type de fichier: \'logo\', \'embleme\', \'header_frame\'
 * @return bool True si la suppression a réussi
 */',
        'startLine' => 105,
        'endLine' => 130,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'aliasName' => NULL,
      ),
      'deleteAllFiles' => 
      array (
        'name' => 'deleteAllFiles',
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
            'startLine' => 138,
            'endLine' => 138,
            'startColumn' => 34,
            'endColumn' => 45,
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
 * Supprimer tous les fichiers d\'une école
 * 
 * @param Ecole $ecole École concernée
 * @return void
 */',
        'startLine' => 138,
        'endLine' => 152,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'aliasName' => NULL,
      ),
      'validateFile' => 
      array (
        'name' => 'validateFile',
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
            'startLine' => 162,
            'endLine' => 162,
            'startColumn' => 33,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'type' => 
          array (
            'name' => 'type',
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
            'startLine' => 162,
            'endLine' => 162,
            'startColumn' => 53,
            'endColumn' => 64,
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
 * Valider un fichier uploadé
 * 
 * @param UploadedFile $file Fichier à valider
 * @param string $type Type de fichier
 * @return void
 * @throws \\InvalidArgumentException Si le fichier n\'est pas valide
 */',
        'startLine' => 162,
        'endLine' => 185,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'aliasName' => NULL,
      ),
      'deleteOldFile' => 
      array (
        'name' => 'deleteOldFile',
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
            'startLine' => 194,
            'endLine' => 194,
            'startColumn' => 34,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'type' => 
          array (
            'name' => 'type',
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
            'startLine' => 194,
            'endLine' => 194,
            'startColumn' => 48,
            'endColumn' => 59,
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
 * Supprimer l\'ancien fichier avant d\'uploader le nouveau
 * 
 * @param Ecole $ecole École concernée
 * @param string $type Type de fichier
 * @return void
 */',
        'startLine' => 194,
        'endLine' => 197,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'aliasName' => NULL,
      ),
      'generateFilename' => 
      array (
        'name' => 'generateFilename',
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
            'startLine' => 206,
            'endLine' => 206,
            'startColumn' => 37,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'type' => 
          array (
            'name' => 'type',
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
            'startLine' => 206,
            'endLine' => 206,
            'startColumn' => 57,
            'endColumn' => 68,
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
 * Générer un nom de fichier unique
 * 
 * @param UploadedFile $file Fichier uploadé
 * @param string $type Type de fichier
 * @return string Nom de fichier unique
 */',
        'startLine' => 206,
        'endLine' => 213,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'aliasName' => NULL,
      ),
      'getFileInfo' => 
      array (
        'name' => 'getFileInfo',
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
            'startLine' => 222,
            'endLine' => 222,
            'startColumn' => 31,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'type' => 
          array (
            'name' => 'type',
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
            'startLine' => 222,
            'endLine' => 222,
            'startColumn' => 45,
            'endColumn' => 56,
            'parameterIndex' => 1,
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
                  'name' => 'array',
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
 * Obtenir les informations d\'un fichier
 * 
 * @param Ecole $ecole École concernée
 * @param string $type Type de fichier
 * @return array|null Informations du fichier ou null si inexistant
 */',
        'startLine' => 222,
        'endLine' => 242,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\EcoleFileStorageService',
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
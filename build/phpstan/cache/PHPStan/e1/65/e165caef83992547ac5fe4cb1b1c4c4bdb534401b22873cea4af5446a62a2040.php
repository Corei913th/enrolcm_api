<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Infrastructure\Storage\TemporaryFileService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Infrastructure\Storage\TemporaryFileService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-a741ba78832c0a30aeaecbd6e30dad93602989946859cddae2f3af7057b84307',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Infrastructure/Storage/TemporaryFileService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Infrastructure\\Storage',
    'name' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
    'shortName' => 'TemporaryFileService',
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
    'endLine' => 153,
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
      'TEMP_BASE_PATH' => 
      array (
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'name' => 'TEMP_BASE_PATH',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'temp/uploads\'',
          'attributes' => 
          array (
            'startLine' => 15,
            'endLine' => 15,
            'startTokenPos' => 43,
            'startFilePos' => 318,
            'endTokenPos' => 43,
            'endFilePos' => 331,
          ),
        ),
        'docComment' => '/**
 * Chemin de base pour les fichiers temporaires
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 15,
        'startColumn' => 3,
        'endColumn' => 48,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'storeTemporary' => 
      array (
        'name' => 'storeTemporary',
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
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 34,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'prefix' => 
          array (
            'name' => 'prefix',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 24,
                'endLine' => 24,
                'startTokenPos' => 65,
                'startFilePos' => 622,
                'endTokenPos' => 65,
                'endFilePos' => 623,
              ),
            ),
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
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 54,
            'endColumn' => 72,
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
 * Stocker un fichier temporairement
 *
 * @param UploadedFile $file Le fichier à stocker
 * @param string $prefix Préfixe pour le nom du fichier
 * @return string Le chemin du fichier stocké
 */',
        'startLine' => 24,
        'endLine' => 44,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'aliasName' => NULL,
      ),
      'moveToPermament' => 
      array (
        'name' => 'moveToPermament',
        'parameters' => 
        array (
          'tempPath' => 
          array (
            'name' => 'tempPath',
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
            'startLine' => 54,
            'endLine' => 54,
            'startColumn' => 35,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'permanentPath' => 
          array (
            'name' => 'permanentPath',
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
            'startLine' => 54,
            'endLine' => 54,
            'startColumn' => 53,
            'endColumn' => 73,
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
 * Déplacer un fichier temporaire vers un emplacement permanent
 *
 * @param string $tempPath Chemin du fichier temporaire
 * @param string $permanentPath Chemin de destination permanent
 * @return string Le chemin permanent du fichier
 * @throws \\Exception Si le fichier temporaire n\'existe pas
 */',
        'startLine' => 54,
        'endLine' => 75,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'aliasName' => NULL,
      ),
      'cleanupExpired' => 
      array (
        'name' => 'cleanupExpired',
        'parameters' => 
        array (
          'hours' => 
          array (
            'name' => 'hours',
            'default' => 
            array (
              'code' => '24',
              'attributes' => 
              array (
                'startLine' => 83,
                'endLine' => 83,
                'startTokenPos' => 373,
                'startFilePos' => 2459,
                'endTokenPos' => 373,
                'endFilePos' => 2460,
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
            'startLine' => 83,
            'endLine' => 83,
            'startColumn' => 34,
            'endColumn' => 48,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Nettoyer les fichiers temporaires expirés
 *
 * @param int $hours Nombre d\'heures après lesquelles un fichier est considéré comme expiré
 * @return int Nombre de fichiers supprimés
 */',
        'startLine' => 83,
        'endLine' => 115,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'aliasName' => NULL,
      ),
      'getTempPath' => 
      array (
        'name' => 'getTempPath',
        'parameters' => 
        array (
          'filename' => 
          array (
            'name' => 'filename',
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
            'startLine' => 123,
            'endLine' => 123,
            'startColumn' => 31,
            'endColumn' => 46,
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
 * Obtenir le chemin complet d\'un fichier temporaire
 *
 * @param string $filename Nom du fichier
 * @return string Chemin complet
 */',
        'startLine' => 123,
        'endLine' => 126,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'aliasName' => NULL,
      ),
      'exists' => 
      array (
        'name' => 'exists',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 134,
            'endLine' => 134,
            'startColumn' => 26,
            'endColumn' => 37,
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
 * Vérifier si un fichier temporaire existe
 *
 * @param string $path Chemin du fichier
 * @return bool
 */',
        'startLine' => 134,
        'endLine' => 137,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'aliasName' => NULL,
      ),
      'delete' => 
      array (
        'name' => 'delete',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 145,
            'endLine' => 145,
            'startColumn' => 26,
            'endColumn' => 37,
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
 * Supprimer un fichier temporaire spécifique
 *
 * @param string $path Chemin du fichier
 * @return bool
 */',
        'startLine' => 145,
        'endLine' => 152,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Infrastructure\\Storage',
        'declaringClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'implementingClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
        'currentClassName' => 'App\\Services\\Infrastructure\\Storage\\TemporaryFileService',
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
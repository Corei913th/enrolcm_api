<?php declare(strict_types = 1);

// osfsl-D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Models/ConcoursFiliere.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\ConcoursFiliere
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-dc105f1dd04ab454569ca49cebc71a88d72f4db137065f07e5acdc868a2d9bb0-8.2.30-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\ConcoursFiliere',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Models/ConcoursFiliere.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\ConcoursFiliere',
    'shortName' => 'ConcoursFiliere',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @mixin IdeHelperConcoursFiliere
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 111,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
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
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'concours_filiere\'',
          'attributes' => 
          array (
            'startLine' => 12,
            'endLine' => 12,
            'startTokenPos' => 30,
            'startFilePos' => 185,
            'endTokenPos' => 30,
            'endFilePos' => 202,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 12,
        'endLine' => 12,
        'startColumn' => 5,
        'endColumn' => 42,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'incrementing' => 
      array (
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'name' => 'incrementing',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 13,
            'endLine' => 13,
            'startTokenPos' => 39,
            'startFilePos' => 232,
            'endTokenPos' => 39,
            'endFilePos' => 236,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 13,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'primaryKey' => 
      array (
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'name' => 'primaryKey',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'concours_id\', \'session_id\', \'filiere_id\']',
          'attributes' => 
          array (
            'startLine' => 14,
            'endLine' => 14,
            'startTokenPos' => 48,
            'startFilePos' => 267,
            'endTokenPos' => 56,
            'endFilePos' => 309,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 14,
        'endLine' => 14,
        'startColumn' => 5,
        'endColumn' => 72,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'concours_id\', \'session_id\', \'filiere_id\', \'nombre_places\']',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 21,
            'startTokenPos' => 65,
            'startFilePos' => 339,
            'endTokenPos' => 79,
            'endFilePos' => 437,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'nombre_places\' => \'integer\', \'created_at\' => \'datetime\', \'updated_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 27,
            'startTokenPos' => 88,
            'startFilePos' => 464,
            'endTokenPos' => 111,
            'endFilePos' => 580,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'concours' => 
      array (
        'name' => 'concours',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 30,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'currentClassName' => 'App\\Models\\ConcoursFiliere',
        'aliasName' => NULL,
      ),
      'filiere' => 
      array (
        'name' => 'filiere',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 35,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'currentClassName' => 'App\\Models\\ConcoursFiliere',
        'aliasName' => NULL,
      ),
      'session' => 
      array (
        'name' => 'session',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 40,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'currentClassName' => 'App\\Models\\ConcoursFiliere',
        'aliasName' => NULL,
      ),
      'hasPlacesDisponibles' => 
      array (
        'name' => 'hasPlacesDisponibles',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 46,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'currentClassName' => 'App\\Models\\ConcoursFiliere',
        'aliasName' => NULL,
      ),
      'getNombreCandidatures' => 
      array (
        'name' => 'getNombreCandidatures',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 51,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'currentClassName' => 'App\\Models\\ConcoursFiliere',
        'aliasName' => NULL,
      ),
      'getPlacesRestantes' => 
      array (
        'name' => 'getPlacesRestantes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 62,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'currentClassName' => 'App\\Models\\ConcoursFiliere',
        'aliasName' => NULL,
      ),
      'peutAccepterCandidature' => 
      array (
        'name' => 'peutAccepterCandidature',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 67,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'currentClassName' => 'App\\Models\\ConcoursFiliere',
        'aliasName' => NULL,
      ),
      'setKeysForSaveQuery' => 
      array (
        'name' => 'setKeysForSaveQuery',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 79,
            'endLine' => 79,
            'startColumn' => 44,
            'endColumn' => 49,
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
 * Set the keys for a save update query.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder  $query
 * @return \\Illuminate\\Database\\Eloquent\\Builder
 */',
        'startLine' => 79,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'currentClassName' => 'App\\Models\\ConcoursFiliere',
        'aliasName' => NULL,
      ),
      'getKeyForSaveQuery' => 
      array (
        'name' => 'getKeyForSaveQuery',
        'parameters' => 
        array (
          'keyName' => 
          array (
            'name' => 'keyName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 99,
                'endLine' => 99,
                'startTokenPos' => 474,
                'startFilePos' => 2453,
                'endTokenPos' => 474,
                'endFilePos' => 2456,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 99,
            'endLine' => 99,
            'startColumn' => 43,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the primary key value for a save query.
 *
 * @param mixed $keyName
 * @return mixed
 */',
        'startLine' => 99,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConcoursFiliere',
        'implementingClassName' => 'App\\Models\\ConcoursFiliere',
        'currentClassName' => 'App\\Models\\ConcoursFiliere',
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
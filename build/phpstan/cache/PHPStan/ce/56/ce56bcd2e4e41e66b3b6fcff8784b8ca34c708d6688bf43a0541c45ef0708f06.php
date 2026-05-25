<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Domain\Examen\AffectationService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Domain\Examen\AffectationService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-c1f0387cbd2716ffe88ddc5ca8fcf495ce117da67c1c25abceb89adf722c7838',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Examen/AffectationService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Domain\\Examen',
    'name' => 'App\\Services\\Domain\\Examen\\AffectationService',
    'shortName' => 'AffectationService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 308,
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
      'affecterCandidatsSalle' => 
      array (
        'name' => 'affecterCandidatsSalle',
        'parameters' => 
        array (
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
            'startLine' => 25,
            'endLine' => 25,
            'startColumn' => 42,
            'endColumn' => 66,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'ordreAffectation' => 
          array (
            'name' => 'ordreAffectation',
            'default' => 
            array (
              'code' => '\'alphabetique\'',
              'attributes' => 
              array (
                'startLine' => 25,
                'endLine' => 25,
                'startTokenPos' => 72,
                'startFilePos' => 729,
                'endTokenPos' => 72,
                'endFilePos' => 742,
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
            'startLine' => 25,
            'endLine' => 25,
            'startColumn' => 69,
            'endColumn' => 109,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Affecter automatiquement tous les candidats aux salles pour une épreuve.
 *
 * @param string $planningEpreuveId ID du planning d\'épreuve
 * @param string $ordreAffectation Ordre (\'alphabetique\' ou \'moyenne\')
 *
 * @return array Statistiques de l\'affectation
 */',
        'startLine' => 25,
        'endLine' => 39,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'getPlanningEpreuveAvecRelations' => 
      array (
        'name' => 'getPlanningEpreuveAvecRelations',
        'parameters' => 
        array (
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
            'startLine' => 44,
            'endLine' => 44,
            'startColumn' => 52,
            'endColumn' => 76,
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
            'name' => 'App\\Models\\PlanningEpreuve',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Récupère le planning d\'épreuve avec ses relations.
 */',
        'startLine' => 44,
        'endLine' => 48,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'initialiserStatsAffectation' => 
      array (
        'name' => 'initialiserStatsAffectation',
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
        'docComment' => '/**
 * Initialise les statistiques d\'affectation.
 */',
        'startLine' => 53,
        'endLine' => 63,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'getCandidaturesPourPlanning' => 
      array (
        'name' => 'getCandidaturesPourPlanning',
        'parameters' => 
        array (
          'planning' => 
          array (
            'name' => 'planning',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\PlanningEpreuve',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 68,
            'endLine' => 68,
            'startColumn' => 48,
            'endColumn' => 72,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'ordreAffectation' => 
          array (
            'name' => 'ordreAffectation',
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
            'startLine' => 68,
            'endLine' => 68,
            'startColumn' => 75,
            'endColumn' => 98,
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
            'name' => 'Illuminate\\Database\\Eloquent\\Collection',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Récupère les candidatures pour ce planning selon l\'ordre demandé.
 */',
        'startLine' => 68,
        'endLine' => 89,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'getSallesDisponiblesPourCentre' => 
      array (
        'name' => 'getSallesDisponiblesPourCentre',
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
            'startLine' => 94,
            'endLine' => 94,
            'startColumn' => 51,
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
            'name' => 'Illuminate\\Database\\Eloquent\\Collection',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Récupère les salles disponibles pour un centre.
 */',
        'startLine' => 94,
        'endLine' => 100,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'affecterCandidaturesAuxSalles' => 
      array (
        'name' => 'affecterCandidaturesAuxSalles',
        'parameters' => 
        array (
          'candidatures' => 
          array (
            'name' => 'candidatures',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Collection',
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
            'startColumn' => 50,
            'endColumn' => 73,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'salles' => 
          array (
            'name' => 'salles',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Collection',
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
            'startColumn' => 76,
            'endColumn' => 93,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'planning' => 
          array (
            'name' => 'planning',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\PlanningEpreuve',
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
            'startColumn' => 96,
            'endColumn' => 120,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'stats' => 
          array (
            'name' => 'stats',
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
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 123,
            'endColumn' => 135,
            'parameterIndex' => 3,
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
 * Affecte les candidatures aux salles disponibles.
 */',
        'startLine' => 105,
        'endLine' => 136,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'affecterCandidatureASalle' => 
      array (
        'name' => 'affecterCandidatureASalle',
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
            'startColumn' => 5,
            'endColumn' => 28,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'salles' => 
          array (
            'name' => 'salles',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Collection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 143,
            'endLine' => 143,
            'startColumn' => 5,
            'endColumn' => 22,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'planning' => 
          array (
            'name' => 'planning',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\PlanningEpreuve',
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
            'startColumn' => 5,
            'endColumn' => 29,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'salleIndex' => 
          array (
            'name' => 'salleIndex',
            'default' => NULL,
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
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 145,
            'endLine' => 145,
            'startColumn' => 5,
            'endColumn' => 20,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'placeDansSalle' => 
          array (
            'name' => 'placeDansSalle',
            'default' => NULL,
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
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 146,
            'endLine' => 146,
            'startColumn' => 5,
            'endColumn' => 24,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'sallesUtilisees' => 
          array (
            'name' => 'sallesUtilisees',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Collection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 147,
            'endLine' => 147,
            'startColumn' => 5,
            'endColumn' => 32,
            'parameterIndex' => 5,
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
 * Affecte une candidature à une salle.
 */',
        'startLine' => 141,
        'endLine' => 177,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'calculerPlacesRestantes' => 
      array (
        'name' => 'calculerPlacesRestantes',
        'parameters' => 
        array (
          'salles' => 
          array (
            'name' => 'salles',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Collection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 182,
            'endLine' => 182,
            'startColumn' => 44,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Calcule le nombre total de places restantes dans toutes les salles.
 */',
        'startLine' => 182,
        'endLine' => 192,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'reaffecterCandidat' => 
      array (
        'name' => 'reaffecterCandidat',
        'parameters' => 
        array (
          'candidatureSalleId' => 
          array (
            'name' => 'candidatureSalleId',
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
            'startLine' => 203,
            'endLine' => 203,
            'startColumn' => 38,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'nouvelleSalleId' => 
          array (
            'name' => 'nouvelleSalleId',
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
            'startLine' => 203,
            'endLine' => 203,
            'startColumn' => 66,
            'endColumn' => 88,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'nouveauNumeroPlace' => 
          array (
            'name' => 'nouveauNumeroPlace',
            'default' => NULL,
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
            'startLine' => 203,
            'endLine' => 203,
            'startColumn' => 91,
            'endColumn' => 113,
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
            'name' => 'App\\Models\\CandidatureSalle',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Réaffecter un candidat à une autre salle.
 *
 * @param string $candidatureSalleId ID de l\'affectation actuelle
 * @param string $nouvelleSalleId ID de la nouvelle salle
 * @param int $nouveauNumeroPlace Nouveau numéro de place
 *
 * @return CandidatureSalle Affectation mise à jour
 */',
        'startLine' => 203,
        'endLine' => 231,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'marquerPresent' => 
      array (
        'name' => 'marquerPresent',
        'parameters' => 
        array (
          'candidatureSalleId' => 
          array (
            'name' => 'candidatureSalleId',
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
            'startLine' => 242,
            'endLine' => 242,
            'startColumn' => 34,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'heureArrivee' => 
          array (
            'name' => 'heureArrivee',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 242,
                'endLine' => 242,
                'startTokenPos' => 1220,
                'startFilePos' => 7353,
                'endTokenPos' => 1220,
                'endFilePos' => 7356,
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
            'startLine' => 242,
            'endLine' => 242,
            'startColumn' => 62,
            'endColumn' => 89,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'observations' => 
          array (
            'name' => 'observations',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 242,
                'endLine' => 242,
                'startTokenPos' => 1230,
                'startFilePos' => 7383,
                'endTokenPos' => 1230,
                'endFilePos' => 7386,
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
            'startLine' => 242,
            'endLine' => 242,
            'startColumn' => 92,
            'endColumn' => 119,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\CandidatureSalle',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Marquer un candidat comme présent à l\'examen.
 *
 * @param string $candidatureSalleId ID de l\'affectation
 * @param string $heureArrivee Heure d\'arrivée (optionnel)
 * @param string $observations Observations (optionnel)
 *
 * @return CandidatureSalle Affectation mise à jour
 */',
        'startLine' => 242,
        'endLine' => 253,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'getPlanSalle' => 
      array (
        'name' => 'getPlanSalle',
        'parameters' => 
        array (
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
            'startLine' => 262,
            'endLine' => 262,
            'startColumn' => 32,
            'endColumn' => 56,
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
            'name' => 'Illuminate\\Database\\Eloquent\\Collection',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir le plan de salle pour une épreuve.
 *
 * @param string $planningEpreuveId ID du planning d\'épreuve
 *
 * @return Collection Liste des affectations avec détails
 */',
        'startLine' => 262,
        'endLine' => 273,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'aliasName' => NULL,
      ),
      'getStatistiquesAffectation' => 
      array (
        'name' => 'getStatistiquesAffectation',
        'parameters' => 
        array (
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
            'startLine' => 282,
            'endLine' => 282,
            'startColumn' => 46,
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
 * Obtenir les statistiques d\'affectation pour une épreuve.
 *
 * @param string $planningEpreuveId ID du planning d\'épreuve
 *
 * @return array Statistiques détaillées
 */',
        'startLine' => 282,
        'endLine' => 307,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\AffectationService',
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
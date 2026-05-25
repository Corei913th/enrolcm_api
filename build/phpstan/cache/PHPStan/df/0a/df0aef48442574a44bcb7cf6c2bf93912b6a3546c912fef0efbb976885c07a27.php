<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Domain\Examen\NoteService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Domain\Examen\NoteService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-5a6ecca27130e028ee92aa3bdabd3bc2e489a3bcb21365a9c166c24d84d56a13',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Domain\\Examen\\NoteService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Examen/NoteService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Domain\\Examen',
    'name' => 'App\\Services\\Domain\\Examen\\NoteService',
    'shortName' => 'NoteService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 268,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Traits\\HasAdvancedSearch',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'statusChecker' => 
      array (
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'name' => 'statusChecker',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Domain\\Concours\\Checkers\\ConcoursStatusChecker',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 57,
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
          'statusChecker' => 
          array (
            'name' => 'statusChecker',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Domain\\Concours\\Checkers\\ConcoursStatusChecker',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 23,
            'endLine' => 23,
            'startColumn' => 5,
            'endColumn' => 57,
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
        'startLine' => 22,
        'endLine' => 24,
        'startColumn' => 3,
        'endColumn' => 6,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'aliasName' => NULL,
      ),
      'validateCandidatureEligiblePourNotes' => 
      array (
        'name' => 'validateCandidatureEligiblePourNotes',
        'parameters' => 
        array (
          'candidatureId' => 
          array (
            'name' => 'candidatureId',
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
            'startLine' => 32,
            'endLine' => 32,
            'startColumn' => 57,
            'endColumn' => 77,
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
 * Valider qu\'une candidature est éligible à la saisie de notes.
 *
 * @param string $candidatureId ID de la candidature
 * @throws ConcoursException Si la candidature n\'est pas éligible
 */',
        'startLine' => 32,
        'endLine' => 64,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'aliasName' => NULL,
      ),
      'saisirNote' => 
      array (
        'name' => 'saisirNote',
        'parameters' => 
        array (
          'candidatureId' => 
          array (
            'name' => 'candidatureId',
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
            'startLine' => 78,
            'endLine' => 78,
            'startColumn' => 30,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'epreuveId' => 
          array (
            'name' => 'epreuveId',
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
            'startLine' => 78,
            'endLine' => 78,
            'startColumn' => 53,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'valeur' => 
          array (
            'name' => 'valeur',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 78,
            'endLine' => 78,
            'startColumn' => 72,
            'endColumn' => 84,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'estEliminatoire' => 
          array (
            'name' => 'estEliminatoire',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 78,
                'endLine' => 78,
                'startTokenPos' => 250,
                'startFilePos' => 2387,
                'endTokenPos' => 250,
                'endFilePos' => 2391,
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
            'startLine' => 78,
            'endLine' => 78,
            'startColumn' => 87,
            'endColumn' => 115,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\Note',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Saisir une note pour une épreuve.
 *
 * @param string $candidatureId ID de la candidature
 * @param string $epreuveId ID de l\'épreuve
 * @param float $valeur Valeur de la note (0-20)
 * @param bool $estEliminatoire Si la note est éliminatoire
 *
 * @return Note Note créée
 *
 * @throws ConcoursException Si la valeur est invalide ou la note existe déjà
 */',
        'startLine' => 78,
        'endLine' => 110,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'aliasName' => NULL,
      ),
      'validerNote' => 
      array (
        'name' => 'validerNote',
        'parameters' => 
        array (
          'noteId' => 
          array (
            'name' => 'noteId',
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
            'startLine' => 121,
            'endLine' => 121,
            'startColumn' => 31,
            'endColumn' => 44,
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
            'name' => 'App\\Models\\Note',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Valider une note saisie.
 *
 * @param string $noteId ID de la note
 *
 * @return Note Note validée
 *
 * @throws ConcoursException Si la note n\'existe pas ou n\'est pas en statut SAISIE_TERMINEE
 */',
        'startLine' => 121,
        'endLine' => 135,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'aliasName' => NULL,
      ),
      'modifierNote' => 
      array (
        'name' => 'modifierNote',
        'parameters' => 
        array (
          'noteId' => 
          array (
            'name' => 'noteId',
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
            'startLine' => 148,
            'endLine' => 148,
            'startColumn' => 32,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'valeur' => 
          array (
            'name' => 'valeur',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 148,
            'endLine' => 148,
            'startColumn' => 48,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'estEliminatoire' => 
          array (
            'name' => 'estEliminatoire',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 148,
                'endLine' => 148,
                'startTokenPos' => 573,
                'startFilePos' => 4252,
                'endTokenPos' => 573,
                'endFilePos' => 4256,
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
            'startLine' => 148,
            'endLine' => 148,
            'startColumn' => 63,
            'endColumn' => 91,
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
            'name' => 'App\\Models\\Note',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Modifier une note (avant validation définitive).
 *
 * @param string $noteId ID de la note
 * @param float $valeur Nouvelle valeur
 * @param bool $estEliminatoire Si éliminatoire
 *
 * @return Note Note modifiée
 *
 * @throws ConcoursException Si la note est déjà validée définitivement
 */',
        'startLine' => 148,
        'endLine' => 167,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'aliasName' => NULL,
      ),
      'annulerNote' => 
      array (
        'name' => 'annulerNote',
        'parameters' => 
        array (
          'noteId' => 
          array (
            'name' => 'noteId',
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
            'startLine' => 178,
            'endLine' => 178,
            'startColumn' => 31,
            'endColumn' => 44,
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
 * Annuler une note.
 *
 * @param string $noteId ID de la note
 *
 * @return bool True si annulée
 *
 * @throws ConcoursException Si la note est déjà validée définitivement
 */',
        'startLine' => 178,
        'endLine' => 187,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'aliasName' => NULL,
      ),
      'getCandidatsEligiblesPourNotes' => 
      array (
        'name' => 'getCandidatsEligiblesPourNotes',
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
            'startLine' => 198,
            'endLine' => 198,
            'startColumn' => 50,
            'endColumn' => 67,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'sessionId' => 
          array (
            'name' => 'sessionId',
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
            'startLine' => 198,
            'endLine' => 198,
            'startColumn' => 70,
            'endColumn' => 86,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'filters' => 
          array (
            'name' => 'filters',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 198,
                'endLine' => 198,
                'startTokenPos' => 790,
                'startFilePos' => 5701,
                'endTokenPos' => 791,
                'endFilePos' => 5702,
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
            'startLine' => 198,
            'endLine' => 198,
            'startColumn' => 89,
            'endColumn' => 107,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'perPage' => 
          array (
            'name' => 'perPage',
            'default' => 
            array (
              'code' => '100',
              'attributes' => 
              array (
                'startLine' => 198,
                'endLine' => 198,
                'startTokenPos' => 800,
                'startFilePos' => 5720,
                'endTokenPos' => 800,
                'endFilePos' => 5722,
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
            'startLine' => 198,
            'endLine' => 198,
            'startColumn' => 110,
            'endColumn' => 127,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir les candidats éligibles à la saisie de notes pour un concours.
 * @param string $concoursId ID du concours
 * @param string $sessionId ID de la session
 * @param array $filters Filtres disponibles : search (recherche par code_cand_def ou numero_candidature)
 * @param int $perPage Nombre d\'éléments par page
 *
 * @return \\Illuminate\\Contracts\\Pagination\\LengthAwarePaginator Liste paginée des candidatures éligibles
 */',
        'startLine' => 198,
        'endLine' => 239,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'aliasName' => NULL,
      ),
      'getNotesCandidat' => 
      array (
        'name' => 'getNotesCandidat',
        'parameters' => 
        array (
          'candidatureId' => 
          array (
            'name' => 'candidatureId',
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
            'startLine' => 248,
            'endLine' => 248,
            'startColumn' => 36,
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
 * Obtenir toutes les notes d\'un candidat pour un concours.
 *
 * @param string $candidatureId ID de la candidature
 *
 * @return Collection Notes du candidat
 */',
        'startLine' => 248,
        'endLine' => 254,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'aliasName' => NULL,
      ),
      'calculateGeneralAverage' => 
      array (
        'name' => 'calculateGeneralAverage',
        'parameters' => 
        array (
          'candidatureId' => 
          array (
            'name' => 'candidatureId',
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
            'startColumn' => 43,
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
 * Calculate general average of a candidate with coefficients and eliminatory check
 *
 * @param string $candidatureId Candidature ID
 * @return array [\'average\' => float, \'total_points\' => float, \'total_coefficients\' => float, \'validated_count\' => int, \'is_eliminated\' => bool]
 */',
        'startLine' => 262,
        'endLine' => 266,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\NoteService',
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
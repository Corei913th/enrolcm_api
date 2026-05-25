<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Domain\Examen\Processors\AdmissionProcessor.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Domain\Examen\Processors\AdmissionProcessor
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-4e9bf62a3d74debfb698cd2905a7d6ec4d57b609a097bcc62176f356690fa14b',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Examen/Processors/AdmissionProcessor.php',
      ),
    ),
    'namespace' => 'App\\Services\\Domain\\Examen\\Processors',
    'name' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
    'shortName' => 'AdmissionProcessor',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Processeur de détermination des admissions
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 176,
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
      'determiner' => 
      array (
        'name' => 'determiner',
        'parameters' => 
        array (
          'resultats' => 
          array (
            'name' => 'resultats',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Support\\Collection',
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
            'startColumn' => 5,
            'endColumn' => 25,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'nombrePlaces' => 
          array (
            'name' => 'nombrePlaces',
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
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 5,
            'endColumn' => 21,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'concoursId' => 
          array (
            'name' => 'concoursId',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 27,
                'endLine' => 27,
                'startTokenPos' => 61,
                'startFilePos' => 648,
                'endTokenPos' => 61,
                'endFilePos' => 651,
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
            'startLine' => 27,
            'endLine' => 27,
            'startColumn' => 5,
            'endColumn' => 30,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'sessionId' => 
          array (
            'name' => 'sessionId',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 28,
                'endLine' => 28,
                'startTokenPos' => 71,
                'startFilePos' => 679,
                'endTokenPos' => 71,
                'endFilePos' => 682,
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
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 5,
            'endColumn' => 29,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine admissions for a list of results
 * 
 * @param Collection $resultats
 * @param int $nombrePlaces
 * @param string|null $concoursId For over-booking detection
 * @param string|null $sessionId For over-booking detection
 * @return array
 */',
        'startLine' => 24,
        'endLine' => 72,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen\\Processors',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'aliasName' => NULL,
      ),
      'determinerAvecQuotasRegion' => 
      array (
        'name' => 'determinerAvecQuotasRegion',
        'parameters' => 
        array (
          'resultats' => 
          array (
            'name' => 'resultats',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Support\\Collection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 46,
            'endColumn' => 66,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'nombrePlaces' => 
          array (
            'name' => 'nombrePlaces',
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
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 69,
            'endColumn' => 85,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'maxParRegion' => 
          array (
            'name' => 'maxParRegion',
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
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 88,
            'endColumn' => 106,
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
 * Déterminer les admissions avec une contrainte simple de quotas région.
 *
 * Règle (simple et explicable) :
 * - On conserve l\'ordre de mérite (résultats déjà triés par moyenne desc).
 * - Un candidat est ADMIS si :
 *   - il reste des places
 *   - ET sa région n\'a pas dépassé son plafond (max_par_region) si défini.
 * - Si la région est au plafond, le candidat n\'est pas admis même avec une bonne note.
 * - Ensuite on remplit une liste d\'attente (20% des places) dans le même ordre.
 *
 * @param Collection $resultats Collection<ResultatFinal>
 * @param int $nombrePlaces
 * @param array $maxParRegion ex: [\'CENTRE\' => 50, \'LITTORAL\' => 40]
 */',
        'startLine' => 89,
        'endLine' => 148,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Examen\\Processors',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'aliasName' => NULL,
      ),
      'marquerAdmis' => 
      array (
        'name' => 'marquerAdmis',
        'parameters' => 
        array (
          'resultat' => 
          array (
            'name' => 'resultat',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\ResultatFinal',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 153,
            'endLine' => 153,
            'startColumn' => 33,
            'endColumn' => 55,
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
 * Marquer un résultat comme admis
 */',
        'startLine' => 153,
        'endLine' => 157,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen\\Processors',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'aliasName' => NULL,
      ),
      'marquerListeAttente' => 
      array (
        'name' => 'marquerListeAttente',
        'parameters' => 
        array (
          'resultat' => 
          array (
            'name' => 'resultat',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\ResultatFinal',
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
            'startColumn' => 40,
            'endColumn' => 62,
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
 * Marquer un résultat en liste d\'attente
 */',
        'startLine' => 162,
        'endLine' => 166,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen\\Processors',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'aliasName' => NULL,
      ),
      'marquerRefuse' => 
      array (
        'name' => 'marquerRefuse',
        'parameters' => 
        array (
          'resultat' => 
          array (
            'name' => 'resultat',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\ResultatFinal',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 171,
            'endLine' => 171,
            'startColumn' => 34,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Marquer un résultat comme refusé
 */',
        'startLine' => 171,
        'endLine' => 175,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Examen\\Processors',
        'declaringClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'implementingClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
        'currentClassName' => 'App\\Services\\Domain\\Examen\\Processors\\AdmissionProcessor',
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
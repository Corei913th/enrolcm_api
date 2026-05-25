<?php declare(strict_types = 1);

// osfsl-D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Concours/Validators/PlanningEpreuveValidator.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Domain\Concours\Validators\PlanningEpreuveValidator
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-330cc6db023ca55099af2746d6f5d3cf210b4c0f81d6dfca171d79347f821b6d-8.2.30-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Concours/Validators/PlanningEpreuveValidator.php',
      ),
    ),
    'namespace' => 'App\\Services\\Domain\\Concours\\Validators',
    'name' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
    'shortName' => 'PlanningEpreuveValidator',
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
    'endLine' => 99,
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
      'validateBeforeSave' => 
      array (
        'name' => 'validateBeforeSave',
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
            'startLine' => 16,
            'endLine' => 16,
            'startColumn' => 38,
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
 * Validate planning before save
 * 
 * @param PlanningEpreuve $planning
 * @throws \\Exception If validation fails
 */',
        'startLine' => 16,
        'endLine' => 21,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Concours\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'implementingClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'currentClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'aliasName' => NULL,
      ),
      'validateCoefficient' => 
      array (
        'name' => 'validateCoefficient',
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
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 40,
            'endColumn' => 64,
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
 * Validate coefficient
 * 
 * @param PlanningEpreuve $planning
 * @throws \\Exception If coefficient is invalid
 */',
        'startLine' => 29,
        'endLine' => 49,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Concours\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'implementingClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'currentClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'aliasName' => NULL,
      ),
      'validateDuration' => 
      array (
        'name' => 'validateDuration',
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
            'startLine' => 57,
            'endLine' => 57,
            'startColumn' => 37,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Validate duration matches schedule
 * 
 * @param PlanningEpreuve $planning
 * @throws \\Exception If duration doesn\'t match
 */',
        'startLine' => 57,
        'endLine' => 76,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Concours\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'implementingClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'currentClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'aliasName' => NULL,
      ),
      'validateSchedule' => 
      array (
        'name' => 'validateSchedule',
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
            'startLine' => 84,
            'endLine' => 84,
            'startColumn' => 37,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Validate schedule times
 * 
 * @param PlanningEpreuve $planning
 * @throws \\Exception If schedule is invalid
 */',
        'startLine' => 84,
        'endLine' => 98,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Domain\\Concours\\Validators',
        'declaringClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'implementingClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
        'currentClassName' => 'App\\Services\\Domain\\Concours\\Validators\\PlanningEpreuveValidator',
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
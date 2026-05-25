<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Domain\Concours\Checkers\ConcoursReadinessChecker.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Domain\Concours\Checkers\ConcoursReadinessChecker
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-32921433a8ad3eea2452cb88debe7c29c00e899ee22424f9bf41fbde4faef509',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Domain\\Concours\\Checkers\\ConcoursReadinessChecker',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Concours/Checkers/ConcoursReadinessChecker.php',
      ),
    ),
    'namespace' => 'App\\Services\\Domain\\Concours\\Checkers',
    'name' => 'App\\Services\\Domain\\Concours\\Checkers\\ConcoursReadinessChecker',
    'shortName' => 'ConcoursReadinessChecker',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Vérifie qu\'un concours est prêt pour l\'inscription des candidats
 * 
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 111,
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
      'check' => 
      array (
        'name' => 'check',
        'parameters' => 
        array (
          'concours' => 
          array (
            'name' => 'concours',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Concours',
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
            'startColumn' => 25,
            'endColumn' => 42,
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
 * Vérifie si un concours est prêt pour l\'inscription
 * 
 * @param Concours $concours
 * @return array [\'ready\' => bool, \'reasons\' => array]
 */',
        'startLine' => 20,
        'endLine' => 91,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Concours\\Checkers',
        'declaringClassName' => 'App\\Services\\Domain\\Concours\\Checkers\\ConcoursReadinessChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Concours\\Checkers\\ConcoursReadinessChecker',
        'currentClassName' => 'App\\Services\\Domain\\Concours\\Checkers\\ConcoursReadinessChecker',
        'aliasName' => NULL,
      ),
      'ensureReady' => 
      array (
        'name' => 'ensureReady',
        'parameters' => 
        array (
          'concours' => 
          array (
            'name' => 'concours',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Concours',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 100,
            'endLine' => 100,
            'startColumn' => 31,
            'endColumn' => 48,
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
 * Vérifie si un concours est prêt et lance une exception si non
 * 
 * @param Concours $concours
 * @throws \\DomainException
 * @return void
 */',
        'startLine' => 100,
        'endLine' => 110,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Concours\\Checkers',
        'declaringClassName' => 'App\\Services\\Domain\\Concours\\Checkers\\ConcoursReadinessChecker',
        'implementingClassName' => 'App\\Services\\Domain\\Concours\\Checkers\\ConcoursReadinessChecker',
        'currentClassName' => 'App\\Services\\Domain\\Concours\\Checkers\\ConcoursReadinessChecker',
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
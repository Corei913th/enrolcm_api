<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Observers\PlanningEpreuveObserver.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Observers\PlanningEpreuveObserver
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-20d7cd44e47c3b33427eb7b8916dce891f751c497cd88ed345f382e05f27929c',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Observers\\PlanningEpreuveObserver',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Observers/PlanningEpreuveObserver.php',
      ),
    ),
    'namespace' => 'App\\Observers',
    'name' => 'App\\Observers\\PlanningEpreuveObserver',
    'shortName' => 'PlanningEpreuveObserver',
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
    'endLine' => 80,
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
      'created' => 
      array (
        'name' => 'created',
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
            'startLine' => 15,
            'endLine' => 15,
            'startColumn' => 27,
            'endColumn' => 51,
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
 * Handle the PlanningEpreuve "created" event.
 * Synchronise date_examen du concours avec la première épreuve
 */',
        'startLine' => 15,
        'endLine' => 18,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Observers',
        'declaringClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'implementingClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'currentClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'aliasName' => NULL,
      ),
      'updated' => 
      array (
        'name' => 'updated',
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
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 27,
            'endColumn' => 51,
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
 * Handle the PlanningEpreuve "updated" event.
 * Synchronise date_examen du concours si la date a changé
 */',
        'startLine' => 24,
        'endLine' => 29,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Observers',
        'declaringClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'implementingClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'currentClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'aliasName' => NULL,
      ),
      'deleted' => 
      array (
        'name' => 'deleted',
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
            'startLine' => 35,
            'endLine' => 35,
            'startColumn' => 27,
            'endColumn' => 51,
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
 * Handle the PlanningEpreuve "deleted" event.
 * Synchronise date_examen du concours après suppression
 */',
        'startLine' => 35,
        'endLine' => 38,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Observers',
        'declaringClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'implementingClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'currentClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'aliasName' => NULL,
      ),
      'synchronizeDateExamen' => 
      array (
        'name' => 'synchronizeDateExamen',
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
            'startLine' => 43,
            'endLine' => 43,
            'startColumn' => 42,
            'endColumn' => 59,
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
 * Synchroniser date_examen du concours avec la première épreuve planifiée
 */',
        'startLine' => 43,
        'endLine' => 79,
        'startColumn' => 3,
        'endColumn' => 3,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Observers',
        'declaringClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'implementingClassName' => 'App\\Observers\\PlanningEpreuveObserver',
        'currentClassName' => 'App\\Observers\\PlanningEpreuveObserver',
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
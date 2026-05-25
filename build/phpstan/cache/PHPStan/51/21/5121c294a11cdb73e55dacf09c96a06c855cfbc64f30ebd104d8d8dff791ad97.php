<?php declare(strict_types = 1);

// odsl-D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Helpers/helpers.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-verifyStaffCredentials
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-b9da1eed0df59fbe3ea8fa03050717243136add0396db981ee93d578e3315899',
   'data' => 
  array (
    'name' => 'verifyStaffCredentials',
    'parameters' => 
    array (
      'username' => 
      array (
        'name' => 'username',
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
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 37,
        'endColumn' => 52,
        'parameterIndex' => 0,
        'isOptional' => false,
      ),
      'password' => 
      array (
        'name' => 'password',
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
        'startLine' => 107,
        'endLine' => 107,
        'startColumn' => 55,
        'endColumn' => 70,
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
        'name' => 'App\\Models\\Utilisateur',
        'isIdentifier' => false,
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Vérifier les credentials d\'un staff (non-candidat)
 * 
 * @param string $username
 * @param string $password
 * @return \\App\\Models\\Utilisateur
 * @throws \\Illuminate\\Validation\\ValidationException
 */',
    'startLine' => 107,
    'endLine' => 110,
    'startColumn' => 5,
    'endColumn' => 5,
    'couldThrow' => false,
    'isClosure' => false,
    'isGenerator' => false,
    'isVariadic' => false,
    'isStatic' => false,
    'namespace' => NULL,
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'verifyStaffCredentials',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Helpers/helpers.php',
      ),
    ),
  ),
));
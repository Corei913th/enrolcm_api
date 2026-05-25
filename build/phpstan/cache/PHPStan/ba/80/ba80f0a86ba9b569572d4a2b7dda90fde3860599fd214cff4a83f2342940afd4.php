<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Helpers\helpers.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-api_forbidden
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-b9da1eed0df59fbe3ea8fa03050717243136add0396db981ee93d578e3315899',
   'data' => 
  array (
    'name' => 'api_forbidden',
    'parameters' => 
    array (
      'message' => 
      array (
        'name' => 'message',
        'default' => 
        array (
          'code' => '\'Accès interdit\'',
          'attributes' => 
          array (
            'startLine' => 254,
            'endLine' => 254,
            'startTokenPos' => 822,
            'startFilePos' => 7603,
            'endTokenPos' => 822,
            'endFilePos' => 7619,
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
        'startLine' => 254,
        'endLine' => 254,
        'startColumn' => 28,
        'endColumn' => 62,
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
        'name' => 'Illuminate\\Http\\JsonResponse',
        'isIdentifier' => false,
      ),
    ),
    'attributes' => 
    array (
    ),
    'docComment' => '/**
 * Crée une réponse standardisée 403 Forbidden.
 *
 * @param string $message Message d\'erreur (par défaut "Accès interdit")
 *
 * @return JsonResponse
 *
 * @example
 * return api_forbidden(\'Permissions insuffisantes\');
 */',
    'startLine' => 254,
    'endLine' => 257,
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
        'name' => 'api_forbidden',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Helpers/helpers.php',
      ),
    ),
  ),
));
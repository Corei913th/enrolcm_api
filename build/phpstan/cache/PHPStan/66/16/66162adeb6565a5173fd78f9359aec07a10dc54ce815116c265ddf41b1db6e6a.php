<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Helpers\helpers.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-api_unauthorized
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-b9da1eed0df59fbe3ea8fa03050717243136add0396db981ee93d578e3315899',
   'data' => 
  array (
    'name' => 'api_unauthorized',
    'parameters' => 
    array (
      'message' => 
      array (
        'name' => 'message',
        'default' => 
        array (
          'code' => '\'Non autorisé\'',
          'attributes' => 
          array (
            'startLine' => 237,
            'endLine' => 237,
            'startTokenPos' => 776,
            'startFilePos' => 7143,
            'endTokenPos' => 776,
            'endFilePos' => 7157,
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
        'startLine' => 237,
        'endLine' => 237,
        'startColumn' => 31,
        'endColumn' => 63,
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
 * Crée une réponse standardisée 401 Unauthorized.
 *
 * @param string $message Message d\'erreur (par défaut "Non autorisé")
 *
 * @return JsonResponse
 *
 * @example
 * return api_unauthorized(\'Authentification requise\');
 */',
    'startLine' => 237,
    'endLine' => 240,
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
        'name' => 'api_unauthorized',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Helpers/helpers.php',
      ),
    ),
  ),
));
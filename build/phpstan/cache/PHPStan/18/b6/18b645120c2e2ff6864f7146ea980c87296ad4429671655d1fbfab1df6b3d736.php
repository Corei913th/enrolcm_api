<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Helpers\helpers.php-PHPStan\BetterReflection\Reflection\ReflectionFunction-api_not_found
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-b9da1eed0df59fbe3ea8fa03050717243136add0396db981ee93d578e3315899',
   'data' => 
  array (
    'name' => 'api_not_found',
    'parameters' => 
    array (
      'message' => 
      array (
        'name' => 'message',
        'default' => 
        array (
          'code' => '\'Ressource non trouvée\'',
          'attributes' => 
          array (
            'startLine' => 220,
            'endLine' => 220,
            'startTokenPos' => 730,
            'startFilePos' => 6669,
            'endTokenPos' => 730,
            'endFilePos' => 6692,
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
        'startLine' => 220,
        'endLine' => 220,
        'startColumn' => 28,
        'endColumn' => 69,
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
 * Crée une réponse standardisée 404 Not Found.
 *
 * @param string $message Message d\'erreur (par défaut "Ressource non trouvée")
 *
 * @return JsonResponse
 *
 * @example
 * return api_not_found(\'Département introuvable\');
 */',
    'startLine' => 220,
    'endLine' => 223,
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
        'name' => 'api_not_found',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Helpers/helpers.php',
      ),
    ),
  ),
));
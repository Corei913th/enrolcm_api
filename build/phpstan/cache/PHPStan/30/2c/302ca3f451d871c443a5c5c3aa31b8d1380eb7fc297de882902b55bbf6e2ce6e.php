<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Models\Ecole.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Ecole
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-dd2ceaa11f15e475fdce0c613fe29aeb9366f19ef10cc0bca7b931be638c5b1e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Ecole',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Models/Ecole.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\Ecole',
    'shortName' => 'Ecole',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @mixin IdeHelperEcole
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 199,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasUuids',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[
    \'code_ecole\',
    \'libelle_ecole\',
    \'libelle_ecole_en\',
    \'region\',
    \'localisation\',
    \'adresse_complete\',
    \'ville\',
    \'logo_url\',
    \'logo_institution_tutelle_url\',
    \'bp_ecole\',
    \'email_ecole\',
    \'siteweb_ecole\',
    \'telephone_ecole\',
    \'fax\',
    \'telephone_2\',
    \'devise\',
    \'slogan\',
    \'embleme_ecole\',
    \'mentions_legales\',
    \'est_actif\',
    // Champs pour les fichiers
    \'logo_path\',
    \'logo_original_name\',
    \'embleme_path\',
    \'embleme_original_name\',
    \'header_frame_path\',
    \'header_frame_original_name\',
]',
          'attributes' => 
          array (
            'startLine' => 17,
            'endLine' => 45,
            'startTokenPos' => 53,
            'startFilePos' => 326,
            'endTokenPos' => 135,
            'endFilePos' => 1008,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'date_creation\' => \'date\', \'est_actif\' => \'boolean\', \'region\' => \\App\\Enums\\RegionCameroun::class, \'created_at\' => \'datetime\', \'updated_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 53,
            'startTokenPos' => 144,
            'startFilePos' => 1035,
            'endTokenPos' => 183,
            'endFilePos' => 1225,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'concours' => 
      array (
        'name' => 'concours',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 55,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
        'aliasName' => NULL,
      ),
      'departements' => 
      array (
        'name' => 'departements',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 60,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
        'aliasName' => NULL,
      ),
      'getRegionLabel' => 
      array (
        'name' => 'getRegionLabel',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 65,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
        'aliasName' => NULL,
      ),
      'getAdresseComplete' => 
      array (
        'name' => 'getAdresseComplete',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 71,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
        'aliasName' => NULL,
      ),
      'getContactsComplets' => 
      array (
        'name' => 'getContactsComplets',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 83,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
        'aliasName' => NULL,
      ),
      'getLogoFullPathAttribute' => 
      array (
        'name' => 'getLogoFullPathAttribute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir le chemin complet du logo pour les PDFs
 */',
        'startLine' => 113,
        'endLine' => 130,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
        'aliasName' => NULL,
      ),
      'getEmblemeFullPathAttribute' => 
      array (
        'name' => 'getEmblemeFullPathAttribute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir le chemin complet de l\'emblème pour les PDFs
 */',
        'startLine' => 135,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
        'aliasName' => NULL,
      ),
      'getHeaderFrameFullPathAttribute' => 
      array (
        'name' => 'getHeaderFrameFullPathAttribute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir le chemin complet du header frame pour les PDFs
 */',
        'startLine' => 157,
        'endLine' => 174,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
        'aliasName' => NULL,
      ),
      'getLogoUrlAttribute' => 
      array (
        'name' => 'getLogoUrlAttribute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir l\'URL publique du logo
 */',
        'startLine' => 179,
        'endLine' => 182,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
        'aliasName' => NULL,
      ),
      'getEmblemeUrlAttribute' => 
      array (
        'name' => 'getEmblemeUrlAttribute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir l\'URL publique de l\'emblème
 */',
        'startLine' => 187,
        'endLine' => 190,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
        'aliasName' => NULL,
      ),
      'getHeaderFrameUrlAttribute' => 
      array (
        'name' => 'getHeaderFrameUrlAttribute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Obtenir l\'URL publique du header frame
 */',
        'startLine' => 195,
        'endLine' => 198,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Ecole',
        'implementingClassName' => 'App\\Models\\Ecole',
        'currentClassName' => 'App\\Models\\Ecole',
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
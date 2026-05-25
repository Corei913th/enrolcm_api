<?php declare(strict_types = 1);

// odsl-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Services\Domain\Paiement\ConcoursPaiementService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Domain\Paiement\ConcoursPaiementService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.1-8.2.30-3584237abaf690233c0c3c9dcd52f1ac1394fdf7ebc8d54efcaf5c91a8bdcc56',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'filename' => 'D:/_CORE/KNOWLEDGE/COURSES/IUC_CS2I/CDWFS/SN_TRIM-2/EC06/enrolcm_api/app/Services/Domain/Paiement/ConcoursPaiementService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Domain\\Paiement',
    'name' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
    'shortName' => 'ConcoursPaiementService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 269,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Traits\\HasActivityLogger',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'logger' => 
          array (
            'name' => 'logger',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Infrastructure\\Logger\\ActivityLoggerService',
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
            'startColumn' => 33,
            'endColumn' => 61,
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
        'startLine' => 15,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'configurerPaiement' => 
      array (
        'name' => 'configurerPaiement',
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
            'startLine' => 27,
            'endLine' => 27,
            'startColumn' => 40,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 27,
            'endLine' => 27,
            'startColumn' => 60,
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
            'name' => 'App\\Models\\ConcoursPaiement',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Créer ou mettre à jour la configuration de paiement d\'un concours.
 *
 * @param string $concoursId UUID du concours
 * @param array $data Données de configuration (banque, compte, montant, date_limite, etc.)
 *
 * @return ConcoursPaiement Configuration créée ou mise à jour
 */',
        'startLine' => 27,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'getConfiguration' => 
      array (
        'name' => 'getConfiguration',
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
            'startLine' => 53,
            'endLine' => 53,
            'startColumn' => 38,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
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
                  'name' => 'App\\Models\\ConcoursPaiement',
                  'isIdentifier' => false,
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
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Récupérer la configuration de paiement d\'un concours.
 *
 * @param string $concoursId UUID du concours
 *
 * @return ConcoursPaiement|null Configuration ou null si inexistante
 */',
        'startLine' => 53,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'getConfigurationsActives' => 
      array (
        'name' => 'getConfigurationsActives',
        'parameters' => 
        array (
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
 * Récupérer toutes les configurations actives et non expirées.
 *
 * @return Collection Liste des configurations actives
 */',
        'startLine' => 63,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'desactiver' => 
      array (
        'name' => 'desactiver',
        'parameters' => 
        array (
          'configId' => 
          array (
            'name' => 'configId',
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
            'startColumn' => 32,
            'endColumn' => 47,
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
            'name' => 'App\\Models\\ConcoursPaiement',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Désactiver la configuration de paiement d\'un concours.
 *
 * @param string $configId ID de la configuration
 *
 * @return ConcoursPaiement Configuration mise à jour
 */',
        'startLine' => 78,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'activer' => 
      array (
        'name' => 'activer',
        'parameters' => 
        array (
          'configId' => 
          array (
            'name' => 'configId',
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
            'startLine' => 93,
            'endLine' => 93,
            'startColumn' => 29,
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
            'name' => 'App\\Models\\ConcoursPaiement',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Activer la configuration de paiement d\'un concours.
 *
 * @param string $configId ID de la configuration
 *
 * @return ConcoursPaiement Configuration mise à jour
 */',
        'startLine' => 93,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'hasConfigurationValide' => 
      array (
        'name' => 'hasConfigurationValide',
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
            'startLine' => 108,
            'endLine' => 108,
            'startColumn' => 44,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifier si un concours a une configuration de paiement valide.
 *
 * @param string $concoursId UUID du concours
 *
 * @return bool True si configuration active et non expirée
 */',
        'startLine' => 108,
        'endLine' => 117,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'getConfigurationsExpirantBientot' => 
      array (
        'name' => 'getConfigurationsExpirantBientot',
        'parameters' => 
        array (
          'jours' => 
          array (
            'name' => 'jours',
            'default' => 
            array (
              'code' => '7',
              'attributes' => 
              array (
                'startLine' => 126,
                'endLine' => 126,
                'startTokenPos' => 528,
                'startFilePos' => 4006,
                'endTokenPos' => 528,
                'endFilePos' => 4006,
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
            'startLine' => 126,
            'endLine' => 126,
            'startColumn' => 54,
            'endColumn' => 67,
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
            'name' => 'Illuminate\\Database\\Eloquent\\Collection',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Récupérer les configurations expirant bientôt.
 *
 * @param int $jours Nombre de jours avant expiration (par défaut 7)
 *
 * @return Collection Liste des configurations expirant bientôt
 */',
        'startLine' => 126,
        'endLine' => 136,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'prolongerDateLimite' => 
      array (
        'name' => 'prolongerDateLimite',
        'parameters' => 
        array (
          'configId' => 
          array (
            'name' => 'configId',
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
            'startLine' => 146,
            'endLine' => 146,
            'startColumn' => 41,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'jours' => 
          array (
            'name' => 'jours',
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
            'startLine' => 146,
            'endLine' => 146,
            'startColumn' => 59,
            'endColumn' => 68,
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
            'name' => 'App\\Models\\ConcoursPaiement',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prolonger la date limite de paiement d\'une configuration.
 *
 * @param string $configId ID de la configuration
 * @param int $jours Nombre de jours à ajouter
 *
 * @return ConcoursPaiement Configuration mise à jour
 */',
        'startLine' => 146,
        'endLine' => 155,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'getStatistiques' => 
      array (
        'name' => 'getStatistiques',
        'parameters' => 
        array (
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
 * Statistiques globales des configurations de paiement.
 *
 * @return array Tableau des statistiques (total, actives, non_expirees, expirees, montant_moyen)
 */',
        'startLine' => 162,
        'endLine' => 171,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'banqueEstAcceptee' => 
      array (
        'name' => 'banqueEstAcceptee',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\ConcoursPaiement',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 181,
            'endLine' => 181,
            'startColumn' => 39,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'nomBanque' => 
          array (
            'name' => 'nomBanque',
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
            'startLine' => 181,
            'endLine' => 181,
            'startColumn' => 65,
            'endColumn' => 81,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Vérifier si une banque est acceptée pour cette configuration.
 *
 * @param ConcoursPaiement $config Configuration de paiement
 * @param string $nomBanque Nom de la banque à vérifier
 *
 * @return bool True si la banque est acceptée
 */',
        'startLine' => 181,
        'endLine' => 184,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'peutValiderAutomatiquement' => 
      array (
        'name' => 'peutValiderAutomatiquement',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\ConcoursPaiement',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 193,
            'endLine' => 193,
            'startColumn' => 48,
            'endColumn' => 71,
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
 * Vérifier si la validation automatique est possible pour cette configuration.
 *
 * @param ConcoursPaiement $config Configuration de paiement
 *
 * @return bool True si la validation auto est possible
 */',
        'startLine' => 193,
        'endLine' => 196,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'getInformationsBancaires' => 
      array (
        'name' => 'getInformationsBancaires',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\ConcoursPaiement',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 205,
            'endLine' => 205,
            'startColumn' => 46,
            'endColumn' => 69,
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
 * Récupérer les informations bancaires complètes d\'une configuration.
 *
 * @param ConcoursPaiement $config Configuration de paiement
 *
 * @return array Informations bancaires formatées
 */',
        'startLine' => 205,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'aliasName' => NULL,
      ),
      'validerConfiguration' => 
      array (
        'name' => 'validerConfiguration',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 217,
            'endLine' => 217,
            'startColumn' => 42,
            'endColumn' => 52,
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
 * Valider les données de configuration avant sauvegarde.
 *
 * @param array $data Données de configuration
 *
 * @return array Tableau des erreurs (vide si aucune erreur)
 */',
        'startLine' => 217,
        'endLine' => 268,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Domain\\Paiement',
        'declaringClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'implementingClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
        'currentClassName' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
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
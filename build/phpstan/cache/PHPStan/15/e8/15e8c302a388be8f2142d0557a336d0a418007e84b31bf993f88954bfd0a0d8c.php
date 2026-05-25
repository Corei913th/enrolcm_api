<?php declare(strict_types = 1);

// ftm-D:\_CORE\KNOWLEDGE\COURSES\IUC_CS2I\CDWFS\SN_TRIM-2\EC06\enrolcm_api\app\Http\Controllers\Concours\ConcoursController.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.2',
   'data' => 
  array (
    0 => 
    array (
      'd54cd23ad263cfb1a574b4cb9403e49a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'a46f65c4fbf16e97bb4735ab095b4c76' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => '__construct',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'eb5d7ba43a7c4462b1becbc2dabd5852' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'index',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '8e423ba6146f3b73b4804baceed8c459' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'availables',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '2eb881109b1899399e2c242209001332' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'show',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'a5be21c57497a20d4e42bbc1b75f3e10' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'store',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '128ff1fcd1fc5f310c74fe979a4957ca' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'update',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '7e3006ec25ad45bab19f4d2f4fc2d98c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'destroy',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'ac90fb91751c81b8be0a079b8523231a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'activate',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'bc57b09d208cb9c5a4a7e10f80c80193' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'deactivate',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'e70c6355526c43901623a62521d9c7c8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'assignSpec',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '7d3d9978d25674c2ef55be3773172979' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'configurePayment',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '8098088edc3802266986835555a48651' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'paymentInfo',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'b2a985e387605e8ba8daff988aeb49ee' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'stats',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '7a926e005c9cbd33c952dfa94cd2b838' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'attachSession',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '37c13316651559f94d8aa7bd77d1d151' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'detachSession',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '54bd1ddbbee64ce76de1424d087407c4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'changeSessionState',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '274a7d972009c6d8b69ea92e5b76d6c9' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'listCentres',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '33f464cd7caa41146857c373ee9ac88e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'attachCentre',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '8cd99af0330f9f9eda9b24afa5c77457' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'detachCentre',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '53e48581806a3bb93981b2d0bddf4965' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'updateCentreStatus',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '3ca88cf622af835a445d0079415ef3b2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'syncCentres',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'be5f96170e9e1719e2fd7112cafb858b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'listCandidats',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'e971880b2384fd14b45df6f84c97ad44' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'listFilieresAttachees',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'c04d6c92427ae6e3b798b910aed625a0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'listFilieresDisponibles',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'add48713ac67090c1498ca84682b659d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'attachFiliere',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '570f574ccf1b7e1ea884208983e5318f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'detachFiliere',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '8717d9013b734d7e033d7f3b23648911' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'updateFiliereNombrePlaces',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '8c00627d1379353dd3361e42f9cb75b7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'telechargerFicheInscription',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'addaeaf77c85c98430e4357a6e68fb6f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'validatePlaces',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'd712942e0488921a2d7ccf8609264d1a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'App\\Http\\Controllers\\Concours',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'concoursservice' => 'App\\Services\\Domain\\Concours\\ConcoursService',
          'centreservice' => 'App\\Services\\Domain\\Concours\\CentreService',
          'concoursfiliereservice' => 'App\\Services\\Domain\\Concours\\ConcoursFiliereService',
          'concourspaiementservice' => 'App\\Services\\Domain\\Paiement\\ConcoursPaiementService',
          'candidatureservice' => 'App\\Services\\Domain\\Candidature\\CandidatureService',
          'createconcoursrequest' => 'App\\Http\\Requests\\Concours\\CreateConcoursRequest',
          'updateconcoursrequest' => 'App\\Http\\Requests\\Concours\\UpdateConcoursRequest',
          'configurerpaiementrequest' => 'App\\Http\\Requests\\Concours\\ConfigurerPaiementRequest',
          'attachcentrerequest' => 'App\\Http\\Requests\\Concours\\AttachCentreRequest',
          'updatecentrestatusrequest' => 'App\\Http\\Requests\\Concours\\UpdateCentreStatusRequest',
          'synccentresrequest' => 'App\\Http\\Requests\\Concours\\SyncCentresRequest',
          'createconcoursdto' => 'App\\DTOs\\Concours\\CreateConcoursDTO',
          'updateconcoursdto' => 'App\\DTOs\\Concours\\UpdateConcoursDTO',
          'concoursexception' => 'App\\Exceptions\\ConcoursException',
          'concoursresource' => 'App\\Http\\Resources\\ConcoursResource',
          'ficheinscriptionpdfservice' => 'App\\Services\\Infrastructure\\Pdf\\FicheInscriptionPdfService',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
          'cache' => 'Illuminate\\Support\\Facades\\Cache',
        ),
         'className' => 'App\\Http\\Controllers\\Concours\\ConcoursController',
         'functionName' => 'placesReport',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
    ),
    1 => 
    array (
      'D:\\_CORE\\KNOWLEDGE\\COURSES\\IUC_CS2I\\CDWFS\\SN_TRIM-2\\EC06\\enrolcm_api\\app\\Http\\Controllers\\Concours\\ConcoursController.php' => 'e9fd714cc98e4af7842d2655a3203b4b7b12e06bcfc83212a8d4cc061821eb10',
    ),
  ),
));
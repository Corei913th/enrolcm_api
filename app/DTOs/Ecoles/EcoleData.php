<?php

namespace App\DTOs\Ecoles;

use Spatie\LaravelData\Data;

class EcoleData extends Data
{
    public function __construct(
        public string $code_ecole,
        public string $libelle_ecole,
        public ?string $region,
        public ?string $localisation,
        
        // Fichiers et médias
        public ?string $logo_url,
        public ?string $emblemee,
        public ?string $photo_facade,
        public ?string $document_t,
        
        // Informations de contact
        p_ecole,
 
}
,
    ) {} null =ptiong $descristrinc ?publi      e,
  _actif = tru $estic bool       publnnées
 doatut et méta  // St   
   ,
        blissementtype_etatring $c ?s       publireation,
 $date_c?string  public t,
       o_agremenerng $numic ?stri publ       s légales
tionforma      // In  
        
ephone,_teleuring $direct?str public 
       _email,ecteuring $dirstrblic ? pu
       eur_nom,ing $directpublic ?str
         $devise,?string     public es
   istrativs admin Information    //     
      cole,
 ng $fax_e ?stripublic   le,
     elephone_ecoc ?string $t       publieb_ecole,
 sitewng $ic ?stri     publ   l_ecole,
tring $emailic ?s       pub
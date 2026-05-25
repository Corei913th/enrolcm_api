<?php

namespace Database\Seeders;

use App\Models\Ecole;
use Illuminate\Database\Seeder;

class EcoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ecoles = [
            [
                // ENSP - École Polytechnique de Yaoundé
                'code_ecole' => 'ENSP',
                'libelle_ecole' => 'École Nationale Supérieure Polytechnique de Yaoundé',
                'libelle_ecole_en' => 'National Advanced School of Engineering of Yaoundé',

                // Localisation
                'region' => 'CENTRE',
                'localisation' => 'Campus Université de Yaoundé I, Ngoa-Ekellé',
                'adresse_complete' => 'Ngoa-Ekellé, Yaoundé, Cameroun',
                'ville' => 'Yaoundé',

                // Contact
                'telephone_ecole' => '+237222234567',
                'fax' => '+237222234568',
                'telephone_2' => '+237222234569',
                'email_ecole' => 'contact@polytechnique.cm',
                'siteweb_ecole' => 'https://polytechnique.cm',
                'bp_ecole' => 'BP 8390',

                // Identité
                'devise' => 'Sapienta – Collativa – Cognitio',
                'slogan' => 'Former les ingénieurs de demain',

                // Tutelle
                'nom_institution_tutelle' => 'Ministère de l\'Enseignement Supérieur',
                'nom_institution_tutelle_en' => 'Ministry of Higher Education',
                'numero_agrement' => '0001/MINESUP/SG/DAUQ/SDEAC/SE',
                'date_creation' => '1971-06-04',

                // Statut
                'est_actif' => true,
                'mentions_legales' => 'Établissement public d\'enseignement supérieur sous tutelle du Ministère de l\'Enseignement Supérieur du Cameroun.',
            ],
            [
                // ENSAI - Sciences Agro-Industrielles
                'code_ecole' => 'ENSAI',
                'libelle_ecole' => 'École Nationale Supérieure des Sciences Agro-Industrielles',
                'libelle_ecole_en' => 'National Advanced School of Agro-Industrial Sciences',

                // Localisation
                'region' => 'ADAMAOUA',
                'localisation' => 'Université de Ngaoundéré',
                'adresse_complete' => 'Université de Ngaoundéré, Ngaoundéré, Cameroun',
                'ville' => 'Ngaoundéré',

                // Contact
                'telephone_ecole' => '+237222345678',
                'fax' => '+237222345679',
                'email_ecole' => 'contact@ensai.cm',
                'siteweb_ecole' => 'https://ensai.univ-ndere.cm',
                'bp_ecole' => 'BP 455',

                // Identité
                'devise' => 'Savoir et Développement',
                'slogan' => 'Excellence en agro-industrie',

                // Tutelle
                'nom_institution_tutelle' => 'Ministère de l\'Enseignement Supérieur',
                'nom_institution_tutelle_en' => 'Ministry of Higher Education',
                'numero_agrement' => '0002/MINESUP/SG/DAUQ/SDEAC/SE',
                'date_creation' => '1982-01-01',

                // Statut
                'est_actif' => true,
                'mentions_legales' => 'École nationale spécialisée dans les sciences agro-industrielles.',
            ],
            [
                // ENSET - Douala
                'code_ecole' => 'ENSET',
                'libelle_ecole' => 'École Normale Supérieure d\'Enseignement Technique',
                'libelle_ecole_en' => 'National Advanced Teachers\' Training College of Technical Education',

                // Localisation
                'region' => 'LITTORAL',
                'localisation' => 'Bonabéri',
                'adresse_complete' => 'Bonabéri, Douala, Cameroun',
                'ville' => 'Douala',

                // Contact
                'telephone_ecole' => '+237233456789',
                'fax' => '+237233456790',
                'telephone_2' => '+237233456791',
                'email_ecole' => 'contact@enset-douala.cm',
                'siteweb_ecole' => 'https://enset-douala.cm',
                'bp_ecole' => 'BP 1872',

                // Identité
                'devise' => 'Former pour Transformer',
                'slogan' => 'Excellence dans la formation technique',

                // Tutelle
                'nom_institution_tutelle' => 'Ministère de l\'Enseignement Supérieur',
                'nom_institution_tutelle_en' => 'Ministry of Higher Education',
                'numero_agrement' => '0003/MINESUP/SG/DAUQ/SDEAC/SE',
                'date_creation' => '1979-01-01',

                // Statut
                'est_actif' => true,
                'mentions_legales' => 'Institution de formation des enseignants du secondaire technique.',
            ],
            [
                // ENAM - Administration et Magistrature
                'code_ecole' => 'ENAM',
                'libelle_ecole' => 'École Nationale d\'Administration et de Magistrature',
                'libelle_ecole_en' => 'National School of Administration and Magistracy',

                // Localisation
                'region' => 'CENTRE',
                'localisation' => 'Quartier du Lac',
                'adresse_complete' => 'Quartier du Lac, Yaoundé, Cameroun',
                'ville' => 'Yaoundé',

                // Contact
                'telephone_ecole' => '+237222567890',
                'fax' => '+237222567891',
                'email_ecole' => 'contact@enam-cm.org',
                'siteweb_ecole' => 'https://enam-cm.org',
                'bp_ecole' => 'BP 7171',

                // Identité
                'devise' => 'Servir avec Excellence',
                'slogan' => 'Formation des hauts cadres administratifs',

                // Tutelle
                'nom_institution_tutelle' => 'Présidence de la République',
                'nom_institution_tutelle_en' => 'Presidency of the Republic',
                'numero_agrement' => '0004/PR/SG/DAF',
                'date_creation' => '1959-01-01',

                // Statut
                'est_actif' => true,
                'mentions_legales' => 'École nationale de formation des cadres supérieurs de l\'administration publique.',
            ],
            [
                // IRIC - Relations Internationales
                'code_ecole' => 'IRIC',
                'libelle_ecole' => 'Institut des Relations Internationales du Cameroun',
                'libelle_ecole_en' => 'Institute of International Relations of Cameroon',

                // Localisation
                'region' => 'CENTRE',
                'localisation' => 'Campus Obili',
                'adresse_complete' => 'Obili, Yaoundé, Cameroun',
                'ville' => 'Yaoundé',

                // Contact
                'telephone_ecole' => '+237222678901',
                'fax' => '+237222678902',
                'email_ecole' => 'contact@iricuy2.com',
                'siteweb_ecole' => 'https://www.iricuy2.com',
                'bp_ecole' => 'BP 1637',

                // Identité
                'devise' => 'Diplomatie et Coopération',
                'slogan' => 'Former les diplomates de demain',

                // Tutelle
                'nom_institution_tutelle' => 'Université de Yaoundé II / Ministère des Relations Extérieures',
                'nom_institution_tutelle_en' => 'University of Yaoundé II / Ministry of External Relations',
                'numero_agrement' => '0005/MINREX/SG/DAF',
                'date_creation' => '1971-01-01',

                // Statut
                'est_actif' => true,
                'mentions_legales' => 'Institut de formation des diplomates et cadres des relations internationales.',
            ],
            [
                // ENIEG - Génie Civil
                'code_ecole' => 'ENIEG',
                'libelle_ecole' => 'École Nationale Supérieure d\'Ingénierie et de Génie Civil',
                'libelle_ecole_en' => 'National Advanced School of Engineering and Civil Engineering',

                // Localisation
                'region' => 'OUEST',
                'localisation' => 'Université de Dschang',
                'adresse_complete' => 'Université de Dschang, Dschang, Cameroun',
                'ville' => 'Dschang',

                // Contact
                'telephone_ecole' => '+237233789012',
                'email_ecole' => 'contact@enieg.cm',
                'siteweb_ecole' => 'https://enieg.univ-dschang.cm',
                'bp_ecole' => 'BP 1343',

                // Identité
                'devise' => 'Ingénierie et Développement',
                'slogan' => 'Construire l\'avenir du Cameroun',

                // Tutelle
                'nom_institution_tutelle' => 'Ministère de l\'Enseignement Supérieur',
                'nom_institution_tutelle_en' => 'Ministry of Higher Education',
                'numero_agrement' => '0006/MINESUP/SG/DAUQ/SDEAC/SE',
                'date_creation' => '1998-01-01',

                // Statut
                'est_actif' => true,
                'mentions_legales' => 'École d\'ingénierie spécialisée dans le génie civil et les infrastructures.',
            ],
        ];

        foreach ($ecoles as $ecole) {
            Ecole::updateOrCreate(
                ['code_ecole' => $ecole['code_ecole']],
                $ecole
            );
        }
    }
}

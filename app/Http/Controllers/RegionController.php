<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\JsonResponse;

class RegionController extends Controller
{
    /**
     * Liste des régions
     */
    public function index(): JsonResponse
    {
        $regions = Region::where('est_actif', true)
            ->orderBy('libelle')
            ->get()
            ->map(function ($region) {
                return [
                    'id' => $region->id,
                    'code' => $region->code,
                    'libelle' => $region->libelle->value, // Valeur Enum (ex: 'CENTRE')
                    'nom' => $region->libelle->label(), // Label lisible (ex: 'Centre')
                    'est_actif' => $region->est_actif,
                ];
            });

        return api_success($regions, 'Liste des régions récupérée avec succès');
    }

    /**
     * Liste des régions actives (alias de index)
     */
    public function actifs(): JsonResponse
    {
        return $this->index();
    }
}

<?php

namespace App\Services;

use App\Models\Ecole;

class EcoleService
{
    public static function create(array $data): Ecole
    {
        return Ecole::create($data);
    }

    public static function update(Ecole $ecole, array $data): Ecole
    {
        $ecole->update($data);
        return $ecole;
    }

    public static function toggle(Ecole $ecole): Ecole
    {
        $ecole->update([
            'est_actif' => !$ecole->est_actif
        ]);

        return $ecole;
    }
}

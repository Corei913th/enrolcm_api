<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasOptimizedUpdate
{
    /**
     * Met à jour un modèle uniquement si des changements sont détectés.
     * Retourne true si des modifications ont été effectuées, false sinon.
     *
     * @return bool True si des modifications ont été effectuées
     */
    protected function updateIfDirty(Model $model, array $data): bool
    {
        // Remplir le modèle avec les nouvelles données
        $model->fill($data);

        // Vérifier si des changements ont été détectés
        if (! $model->isDirty()) {
            return false;
        }

        // Sauvegarder uniquement si des changements existent
        $model->save();

        return true;
    }

    /**
     * Met à jour un modèle et invalide le cache uniquement si nécessaire.
     *
     * @param  callable|null  $afterUpdate  Callback à exécuter après mise à jour réussie
     * @return bool True si des modifications ont été effectuées
     */
    protected function updateWithCache(Model $model, array $data, ?callable $afterUpdate = null): bool
    {
        $wasUpdated = $this->updateIfDirty($model, $data);

        if ($wasUpdated) {
            // Invalider le cache uniquement si des modifications ont été effectuées
            if (method_exists($this, 'invalidateCacheAfterModification')) {
                $this->invalidateCacheAfterModification($model->id);
            }

            // Exécuter le callback si fourni
            if ($afterUpdate) {
                $afterUpdate($model);
            }
        }

        return $wasUpdated;
    }

    /**
     * Retourne les champs qui ont été modifiés.
     *
     * @return array Tableau des champs modifiés avec leurs anciennes et nouvelles valeurs
     */
    protected function getDirtyFields(Model $model): array
    {
        $dirty = [];

        foreach ($model->getDirty() as $field => $newValue) {
            $dirty[$field] = [
                'old' => $model->getOriginal($field),
                'new' => $newValue,
            ];
        }

        return $dirty;
    }

    /**
     * Vérifie si des champs spécifiques ont été modifiés.
     */
    protected function hasChangedFields(Model $model, array $fields): bool
    {
        return $model->isDirty($fields);
    }

    /**
     * Met à jour uniquement les champs spécifiés s'ils sont présents dans les données.
     *
     * @return bool True si des modifications ont été effectuées
     */
    protected function updateOnlyFields(Model $model, array $data, array $allowedFields): bool
    {
        $filteredData = array_intersect_key($data, array_flip($allowedFields));

        return $this->updateIfDirty($model, $filteredData);
    }
}

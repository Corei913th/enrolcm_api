<?php

namespace App\Observers;

use App\Enums\StatutNote;
use App\Models\Note;

/**
 * Observer for Note model
 */
class NoteObserver
{
    /**
     * Handle the Note "updating" event.
     * Prevents modification of validated notes
     */
    public function updating(Note $note): void
    {
        $oldStatut = $note->getOriginal('statut');
        $oldValue = $oldStatut instanceof StatutNote ? $oldStatut : StatutNote::tryFrom($oldStatut);

        if ($note->isDirty('valeur') && $oldValue === StatutNote::VALIDEE) {
            throw new \LogicException('Impossible de modifier une note validée');
        }

        if ($note->isDirty('statut') && $oldValue === StatutNote::VALIDEE) {
            throw new \LogicException('Impossible de modifier le statut d\'une note validée');
        }

        if ($note->isDirty('est_eliminatoire') && $oldValue === StatutNote::VALIDEE) {
            throw new \LogicException('Impossible de modifier le caractère éliminatoire d\'une note validée');
        }
    }

    /**
     * Handle the Note "deleting" event.
     * Prevents deletion of validated notes
     */
    public function deleting(Note $note): void
    {
        if ($note->statut === StatutNote::VALIDEE) {
            throw new \LogicException('Impossible de supprimer une note validée');
        }
    }
}

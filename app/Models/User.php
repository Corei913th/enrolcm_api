<?php

namespace App\Models;

/**
 * Alias pour le modèle Utilisateur
 * Permet la compatibilité avec les packages Laravel qui attendent App\Models\User
 */
class User extends Utilisateur
{
    // Ce modèle hérite de toutes les fonctionnalités d'Utilisateur
}

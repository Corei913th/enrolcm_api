# Collections Postman - API Concours

Ce dossier contient les collections Postman pour tester l'API de gestion des concours.

## 📁 Collections Disponibles

### 1. **concours.postman_collection.json**
Routes de gestion des concours :
- ✅ Concours disponibles (ouverts)
- ✅ Lister tous les concours (admin)
- ✅ Détails d'un concours
- ✅ Créer/modifier/supprimer un concours
- ✅ Activer/désactiver un concours

### 2. **notes.postman_collection.json**
Routes de gestion des notes d'examen :
- ✅ Saisir une note pour une épreuve
- ✅ Valider une note saisie
- ✅ Modifier une note (avant validation)
- ✅ Annuler une note
- ✅ Consulter les notes d'un candidat
- ✅ Calculer la moyenne générale

### 3. **resultats.postman_collection.json**
Routes de calcul et publication des résultats :
- ✅ Lister tous les résultats
- ✅ Calculer automatiquement tous les résultats
- ✅ Déterminer les admissions selon les places
- ✅ Publier les résultats aux candidats
- ✅ Consulter le résultat d'un candidat
- ✅ Voir le classement d'une filière

### 4. **salles.postman_collection.json**
Routes d'affectation automatique aux salles :
- ✅ Affecter automatiquement les candidats aux salles
- ✅ Lister les affectations d'une épreuve
- ✅ Consulter le plan de salle
- ✅ Voir les statistiques d'affectation
- ✅ Réaffecter un candidat à une autre salle
- ✅ Marquer un candidat comme présent

### 5. **filieres.postman_collection.json**
Routes de gestion des filières par concours :
- ✅ Lister les filières attachées
- ✅ Attacher une filière avec nombre de places
- ✅ Détacher une filière
- ✅ Consulter les statistiques d'une filière
- ✅ Modifier le nombre de places

## 🚀 Comment utiliser

### 1. **Importer dans Postman**
1. Ouvrir Postman
2. Cliquer sur "Import" en haut à gauche
3. Sélectionner "File"
4. Importer chaque fichier `.postman_collection.json`

### 2. **Configuration des variables**
Chaque collection contient des variables à configurer :

#### Variables globales :
- `{{baseURL}}` : `http://localhost:8000/api` (URL de votre API)
- `{{auth_token}}` : Votre token JWT d'authentification

#### Variables spécifiques :
- `{{concours_id}}` : ID d'un concours
- `{{session_id}}` : ID d'une session
- `{{candidature_id}}` : ID d'une candidature
- `{{filiere_id}}` : ID d'une filière
- `{{planning_id}}` : ID d'un planning d'épreuve
- `{{note_id}}` : ID d'une note
- `{{affectation_id}}` : ID d'une affectation salle

### 3. **Workflow de test**

#### Authentification :
1. Utiliser la collection `auth.postman_collection.json` pour obtenir un token
2. Copier le token dans la variable `{{auth_token}}`

#### Test complet :
1. **Créer un concours** (collection `concours`)
2. **Attacher des filières** (collection `filieres`)
3. **Saisir des notes** (collection `notes`)
4. **Calculer les résultats** (collection `resultats`)
5. **Affecter aux salles** (collection `salles`)

## 📋 Routes organisées

```
API Concours
├── concours/                    # Gestion des concours
│   ├── ouverts                  # Concours disponibles
│   ├── {id}                     # CRUD concours
│   └── {id}/activate            # Activation/désactivation
│
├── concours/notes/              # Gestion des notes
│   ├── POST /                   # Saisir note
│   ├── PUT /{id}/validate       # Valider note
│   ├── PUT /{id}                # Modifier note
│   ├── DELETE /{id}             # Annuler note
│   └── candidatures/{id}/notes  # Notes d'un candidat
│
├── concours/resultats/          # Gestion des résultats
│   ├── GET /                    # Lister résultats
│   ├── POST /calculer           # Calcul automatique
│   ├── POST /admissions         # Déterminer admissions
│   ├── POST /publier            # Publier résultats
│   └── candidatures/{id}/resultat # Résultat candidat
│
├── concours/salles/             # Affectation salles
│   ├── planning/{id}/affecter-salles    # Affectation auto
│   ├── planning/{id}/affectations       # Lister affectations
│   ├── planning/{id}/plan-salle         # Plan de salle
│   ├── affectations/{id}/reaffecter     # Réaffecter candidat
│   └── affectations/{id}/present        # Marquer présent
│
└── concours/filieres/           # Gestion filières
    ├── GET /                    # Lister filières
    ├── POST /                   # Attacher filière
    ├── DELETE /{id}             # Détacher filière
    ├── GET /{id}/stats          # Stats filière
    └── PUT /{id}/places         # Modifier places
```

## 🔐 Autorisations

- **Routes publiques** : Concours ouverts, détails concours
- **Routes admin** : Toutes les autres (nécessitent `Authorization: Bearer {token}`)

## 📝 Formats des données

### Corps des requêtes :
- **Content-Type** : `application/json`
- **Accept** : `application/json`

### Réponses :
- **Succès** : `{ "success": true, "data": {...}, "message": "..." }`
- **Erreur** : `{ "success": false, "message": "...", "code": 400 }`

---

## 🎯 Prêt pour les tests !

Toutes les collections sont maintenant prêtes à être importées dans Postman pour tester l'API complète de gestion des concours. 🚀

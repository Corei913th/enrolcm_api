# Changements Architecture Concours

## Vue d'ensemble

Refactorisation complète de l'API Concours pour une approche flexible et harmonisée.

## Modifications Apportées

### 1. Harmonisation Create/Update

#### Avant
- `CreateConcoursRequest` : Champs obligatoires rigides
- `UpdateConcoursRequest` : Champs différents, logique distincte
- DTOs incompatibles

#### Après
- **API uniforme** : Même validation pour CREATE et UPDATE
- **DTOs compatibles** : `CreateConcoursDTO` ↔ `UpdateConcoursDTO`
- **Logique centralisée** : Validations identiques

### 2. Workflow Flexible

#### Avant
- Création obligatoire avec `spec_concours_id` + `session_id`
- Workflow rigide : tout ou rien

#### Après
- **4 workflows possibles** :
  - Template seul (libellé uniquement)
  - Specs seules (avec spécifications)
  - Session seule (avec dates/places)
  - Complet (specs + session + dates)

### 3. Champs Optionnels Intelligents

#### Avant
- Tous champs `nullable` sans distinction
- Validation uniforme

#### Après
- **`sometimes`** pour validation conditionnelle :
  - `spec_concours_id` : Validé seulement si fourni
  - `session_id` : Validé seulement si fourni
  - `est_actif` : Override valeur par défaut
- **`nullable`** pour champs vraiment optionnels :
  - `date_debut`, `date_limite_depot`, `nombre_places`

### 4. Validation Métier Améliorée

#### Avant
- Validations basiques
- Pas de cohérence métier

#### Après
- **Dates cohérentes** : `date_limite_depot < date_debut`
- **Unicité intelligente** :
  - Templates : unicité globale
  - Avec session : unicité par session
- **Sessions actives** : rejet sessions inactives
- **Places positives** : validation quantitative

### 5. Gestion Sessions Dynamique

#### Avant
- Sessions attachées seulement à la création
- Pas de modification possible

#### Après
- **Attachement post-création** :
  ```php
  PUT /concours/{id}
  {"session_id": "uuid", "date_debut": "...", "nombre_places": 500}
  ```
- **Changement de session** :
  ```php
  PUT /concours/{id}
  {"session_id": "nouvelle-uuid"} // Détache ancienne, attache nouvelle
  ```
- **États automatiques** : Création `EtatConcoursSession` approprié

### 6. Mapping Base de Données Cohérent

#### Avant
- Incohérence noms : `date_debut` (API) → `date_examen` (DB)
- DTOs avec mapping manuel

#### Après
- **Mapping uniforme** :
  ```php
  // Dans DTOs
  'date_examen' => $this->date_debut,      // API → DB
  'nbre_max_places' => $this->nombre_places // API → DB
  ```
- **API claire** : noms intuitifs côté client
- **DB optimisée** : noms techniques côté stockage

### 7. Modèle Concours Étendu

#### Avant
- Champs limités
- `description` manquait

#### Après
- **Nouveaux champs** :
  - `description` : TEXT nullable ajouté
- **Fillable complet** :
  ```php
  protected $fillable = [
      'spec_concours_id', 'libelle_concours', 'description',
      'date_limite_depot', 'date_examen', 'nbre_max_places',
      'frais_inscription', 'est_actif'
  ];
  ```

### 8. Service Concours Enrichi

#### Avant
- Logique basique
- Pas de gestion sessions

#### Après
- **`attachToSession()`** : méthode dédiée attachement
- **Validations conditionnelles** : selon présence session
- **Gestion transactions** : attach/detach atomique
- **Erreurs explicites** : messages métier clairs

### 9. Tests Exhaustifs

#### Avant
- Tests limités
- Pas de couverture workflow

#### Après
- **10 tests** couvrant tous scénarios
- **42 assertions** pour robustesse
- **100% succès** validé
- **Isolation** : RefreshDatabase

## Impact sur l'API

### Endpoints Non Modifiés
- `GET /api/concours` : liste (compatible)
- `GET /api/concours/{id}` : détail (compatible)
- `DELETE /api/concours/{id}` : suppression (compatible)

### Endpoints Enrichis
- `POST /api/concours` : création flexible (harmonisée)
- `PUT /api/concours/{id}` : mise à jour flexible (nouveau workflow)

## Avantages de l'Architecture

✅ **Flexibilité** : 4 workflows au lieu d'1 rigide
✅ **Cohérence** : API uniforme CREATE/UPDATE
✅ **Évolutivité** : Sessions gérables dynamiquement
✅ **Maintenabilité** : Code DRY, tests complets
✅ **Robustesse** : Validations métier complètes
✅ **UX** : Workflows adaptés aux cas d'usage

## Migration Backward Compatible

✅ **API existante préservée**
✅ **Données existantes intactes**
✅ **Nouveaux champs optionnels**
✅ **Transitions douces possibles**

## Tests de Non-Régression

- ✅ Créations existantes fonctionnent
- ✅ Updates existants compatibles
- ✅ Données legacy préservées
- ✅ Nouvelles fonctionnalités additives uniquement

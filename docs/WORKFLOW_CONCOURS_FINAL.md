# Workflow Concours Final

## Vue d'ensemble

Workflow ultra-flexible permettant la gestion complète du cycle de vie des concours camerounais.

## Acteurs et Rôles

- **Administrateur** : Création specs, gestion concours
- **Établissement** : Configuration sessions, places
- **Candidat** : Consultation concours disponibles

## Cycle de Vie Concours

### Phase 1: Spécifications (Une fois)
```bash
# Création specs concours (réutilisables)
POST /api/specs-concours
{
  "nom_spec": "Concours Polytechnique",
  "desc_infos_concours": "Règles générales Polytechnique",
  "frais_inscription": 25000,
  "carte_nationale_identite": true,
  "acte_naissance": true,
  "releve_notes": true
}
```

### Phase 2: Sessions Académiques (Par année)
```bash
# Création session annuelle
POST /api/sessions
{
  "libelle_session": "2025-2026",
  "est_actif": true,
  "statut_session": "OUVERT"
}
```

### Phase 3: Concours Templates (Par établissement)
```bash
# Création template réutilisable
POST /api/concours
{
  "libelle_concours": "Polytechnique Template",
  "spec_concours_id": "uuid-spec-polytechnique",
  "description": "Concours Polytechnique - Template annuel"
}
```

### Phase 4: Activation Session (Par année)
```bash
# Attachement session spécifique
PUT /api/concours/{template-id}
{
  "session_id": "uuid-session-2025-2026",
  "date_debut": "2025-06-15",
  "date_limite_depot": "2025-05-15",
  "nombre_places": 500
}
```

## Scénarios d'Usage

### Scénario 1: Concours Standardisé
```
Specs Polytechnique → Template → Session 2025 → Session 2026 → ...
```

### Scénario 2: Concours Ponctuel
```
Specs Spéciales → Concours Direct + Session 2025
```

### Scénario 3: Concours Template-Only
```
Template seul → Attachement session plus tard
```

## États et Transitions

### États Concours
- **TEMPLATE** : Définition générale (sans session)
- **ACTIF** : Attaché à session active
- **INACTIF** : Désactivé temporairement
- **ARCHIVE** : Session terminée

### Transitions Possibles
```
TEMPLATE → ACTIF (attachement session)
ACTIF → INACTIF (désactivation)
INACTIF → ACTIF (réactivation)
ACTIF → ARCHIVE (session terminée)
TEMPLATE → ARCHIVE (abandon)
```

## Gestion des Places

### Par Session
```json
{
  "session_id": "2025-2026",
  "nombre_places": 500,
  "date_limite_depot": "2025-05-15"
}
```

### Contraintes
- **Places positives** : `> 0`
- **Capacité limitée** : validation stock
- **Modification possible** : jusqu'à date limite

## Dates Critiques

### Chronologie Standard
```
Aujourd'hui → Date limite dépôt → Date examen → Publication résultats
   ↓            ↓                    ↓              ↓
Ouverture    Fermeture           Examens        Annonces
inscriptions inscriptions
```

### Validations
- `date_limite_depot < date_examen` : Logique temporelle
- `date_examen > aujourd'hui` : Concours futur
- Pas de chevauchement : sessions distinctes

## Permissions et Contrôles

### Administrateur Global
- ✅ Création specs concours
- ✅ Gestion sessions académiques
- ✅ Validation configurations

### Établissement
- ✅ Création templates concours
- ✅ Attachement sessions
- ✅ Configuration places/dates
- ✅ Modification avant ouverture

### Candidat
- ✅ Consultation concours ouverts
- ✅ Vérification dates limites
- ✅ Soumission candidatures

## Gestion des Erreurs

### Erreurs Métier
- `SESSION_INACTIVE` : Session fermée
- `PLACES_INSUFFISANTES` : Capacité dépassée
- `DATE_DEPASSEE` : Inscription tardive
- `CONCOURS_INEXISTANT` : Template supprimé

### Récupération
- **Réactivation** : Sessions fermées réouvrables
- **Extension** : Dates limites modifiables
- **Report** : Examens déplaçables
- **Annulation** : Concours supprimables

## Métriques et Suivi

### KPIs Concours
- **Taux remplissage** : Inscrits / Places
- **Taux succès** : Admis / Inscrits
- **Délais moyens** : Soumission → Résultats

### Alertes Automatiques
- ⚠️ **Places presque pleines** (80%)
- ⚠️ **Date limite proche** (7 jours)
- ⚠️ **Session sans concours** (orphelins)

## Intégration Système

### APIs Externes
- **OCR Service** : Validation documents
- **Notification** : Alertes candidats/admin
- **Paiement** : Intégration mobile money
- **Résultats** : Publication centralisée

### Webhooks
- `concours.created` : Nouveau concours
- `concours.session_attached` : Activation session
- `concours.closed` : Fermeture inscriptions
- `concours.results_published` : Publication résultats

## Sécurité et Conformité

### RGPD
- ✅ **Données minimales** : Champs obligatoires seulement
- ✅ **Consentement** : Validation explicite
- ✅ **Droit oubli** : Suppression complète
- ✅ **Audit trail** : Historique modifications

### Sécurité
- ✅ **Authentification** : JWT obligatoire
- ✅ **Autorisation** : Rôles granulaire
- ✅ **Chiffrement** : Données sensibles
- ✅ **Logs** : Traçabilité complète

## Performance

### Optimisations
- **Index DB** : Requêtes rapides (concours + session)
- **Cache** : Concours populaires
- **Pagination** : Listes volumineuses
- **Lazy loading** : Relations à la demande

### SLA
- **Disponibilité** : 99.9% (maintenance planifiée)
- **Latence** : <500ms réponses API
- **Débit** : 1000 req/min supporté

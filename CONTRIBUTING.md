# Contribuer à EnrolcmAPI

## Convention de Commits

On suit le format **Conventional Commits** :

```
<type>(<portée>): <description>
```

| Type | Usage |
|---|---|
| `feat` | Nouvelle fonctionnalité |
| `fix` | Correction de bug |
| `refactor` | Réorganisation sans nouveau comportement |
| `config` | Changement de configuration (CORS, .env, etc.) |
| `ci` | Pipeline, Docker, monitoring |
| `docs` | Documentation (README, CICD, CONTRIBUTING) |
| `style` | Formatting, lint (Pint) |
| `test` | Ajout ou modification de tests |
| `perf` | Amélioration de performance |
| `chore` | Tâche technique (dépendances, scripts) |

**Portée** (optionnelle) : module concerné — `api`, `auth`, `ocr`, `candidats`, `concours`, `paiements`, `docker`, `ci`, `k8s`

### Exemples

```
feat(api): ajouter endpoint d'inscription
fix(auth): corriger le refresh token expiré
refactor(ocr): extraire la logique de parsing PDF
config(cors): ajouter le domaine frontend staging
ci(docker): installer Chromium headless pour browsershot
docs(contributing): ajouter la convention de commits
style: appliquer Pint PSR-12
test(candidats): couvrir le workflow de validation
```

## Workflow

- `main` → production stable
- `develop` → intégration
- `feature/*` → branches de travail
- `hotfix/*` → correction urgente

Toute PR doit passer la CI (tests, lint, audit) avant merge.

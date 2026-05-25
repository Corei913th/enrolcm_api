# EnrolcmAPI — Pipeline CI/CD & Observabilité

**Documentation Technique Officielle**

| Projet | EnrolcmAPI |
| :--- | :--- |
| **Stack** | Laravel 12 · PHP 8.2 · PostgreSQL 15 · Redis 7 · Docker · Kubernetes |
| **CI/CD** | GitHub Actions → Docker Hub |
| **Observabilité** | ELK Stack (Logs) · Prometheus & Grafana (Métriques) · Alertmanager |

---

## PARTIE I : CONCEPTS FONDAMENTAUX DU CI/CD

### 1. Intégration Continue (CI)
Pipeline automatique : Linting (Pint) → Analyse Statique (Larastan) → Tests Unitaires & Fonctionnels.
*Principe : Tout échec bloque le déploiement.*

### 2. Déploiement Continu (CD)
- **Livraison Continue** : Construction des images Docker et publication sur Docker Hub.
- **Déploiement Continu** : Mise à jour automatique du serveur mutualisé avec Health Check post-déploiement.

---

## PARTIE II : ANALYSE DE LA STACK TECHNIQUE

| Couche | Technologie | Rôle |
| :--- | :--- | :--- |
| **Backend** | Laravel 12 | API REST & Administration |
| **Base de données** | PostgreSQL 15 | Stockage persistant |
| **Cache** | Database (PostgreSQL) | Cache applicatif |
| **OCR** | Tesseract 5 | Extraction de texte des reçus de paiement |
| **PDF** | Ghostscript + ImageMagick | Conversion PDF → image pour OCR |
| **Frontend** | Vite + npm | Build des assets (Tailwind, JS) |
| **Conteneurisation** | Docker | Isolation des services (App, Nginx, DB, Redis) |
| **Metrics** | Prometheus | Collecte des indicateurs (Scrape /api/metrics) |
| **Visualisation** | Grafana | Dashboards Stratégiques & Business |
| **Logs** | ELK Stack | Centralisation (Filebeat → Logstash → ES → Kibana) |

### Dépendances Système (Dockerfile)

```
# PHP Extensions
pdo_pgsql, mbstring, exif, pcntl, bcmath, gd, zip, imagick

# Paquets système
git, curl, libpng-dev, libonig-dev, libxml2-dev, libzip-dev,
libpq-dev, libmagickwand-dev,
ghostscript,
tesseract-ocr, tesseract-ocr-fra, tesseract-ocr-eng,
nodejs, npm
```

---

## PARTIE III : STRATÉGIE DE GESTION DU CODE

### 1. Workflow des Branches (GitHub Flow)
- **main** : Code de production stable.
- **develop** : Branche d'intégration (Staging).
- **feature/** : Nouvelles fonctionnalités (vie courte).
- **hotfix/** : Corrections urgentes de production.

### 2. Convention de Commits
Utilisation de **Conventional Commits** :
- `feat(api):` Ajout d'une fonctionnalité.
- `fix(auth):` Correction d'un bug.
- `ci(monitoring):` Modification des fichiers de pipeline ou monitoring.

---

## PARTIE IV : OBSERVABILITÉ & MONITORING

### 1. Dashboards Grafana

#### A. Strategic Dashboard v2 (Opérations)
- **Taux d'Erreur Global** : Jauge critique basée sur les codes HTTP 4xx/5xx.
- **Temps de Réponse Moyen App** : Mesure précise via le middleware Laravel (en secondes).
- **Santé Infra** : Utilisation CPU/RAM du serveur et état de la base de données.

#### B. Business KPIs (Métiers)
- **Chiffre d'Affaires** : Répartition Encaissé (Collected) vs En attente (Pending).
- **Croissance Abonnés** : Total des abonnés en temps réel.
- **Volume Facturation** : Décompte des factures payées et impayées.

### 2. Alerting (Alertmanager)
- **HighErrorRate** : Seuil de 5% d'erreurs dépassé.
- **DatabaseDown** : Perte de connexion PostgreSQL.
- **AppServiceDown** : L'API ne répond plus.
- **HighMemoryUsage** : RAM > 90%.

### 3. Centralisation des Logs (ELK)
- **Kibana** : Point d'entrée pour le debugging. Index pattern : `laravel-logs-*`.
- **Logstash** : Parseur de logs JSON Laravel.
- **Filebeat** : Collecteur optimisé (mode `--strict.perms=false` pour compatibilité Docker Windows).

---

## PARTIE V : DÉPLOIEMENT & INFRASTRUCTURE

### 1. Docker Compose (Orchestration standard)
15 services de la stack de manière coordonnée.

### 2. Kubernetes (Évolution Scalable)
- **Deployments** (Laravel, Nginx)
- **StatefulSets** (PostgreSQL, Elasticsearch)
- **HorizontalPodAutoscaler** (Scaling auto de 2 à 10 réplicas).

---

## PARTIE VI : DÉTAILS DU PIPELINE GITHUB ACTIONS

### 1. Workflow de Validation (CI)
Déclenché à chaque `push` sur n'importe quelle branche.

| Job | Étapes clés | Rôle |
| :--- | :--- | :--- |
| **Security Audit** | `composer audit` · `phpstan` | Détecte les failles dans les dépendances et les erreurs de logique (SAST). |
| **Secret Scanning** | `gitleaks` | Vérifie qu'aucun mot de passe ou clé n'est présent dans l'historique Git. |
| **IaC Scan** | `trivy` | Analyse le Dockerfile et les fichiers de config pour les failles de sécurité. |
| **Lint & Style** | `laravel pint` | Garantit que le code respecte la norme PSR-12. |
| **Tests** | `artisan test` | Lance les tests unitaires et fonctionnels avec une base PostgreSQL éphémère. Installe Tesseract OCR + build les assets Vite. |
| **SonarQube** | `sonar-scanner` | Analyse finale de la qualité et de la dette technique (après succès des tests). |

### 2. Workflow de Déploiement (CD Production)
Déclenché uniquement après le succès de la CI sur la branche **main**.

1. **Validation CI** : Vérifie que le workflow précédent est bien passé.
2. **Préparation** : Génération sécurisée des clés d'application et du fichier `.env`.
3. **Synchronisation** : Déploiement des fichiers via **FTP-Deploy-Action** vers l'hébergement (dossier `public_html/bogning/enrolcm_api/`).
4. **Post-Déploiement** : Migration de base de données et vidage des caches.

---

## GLOSSAIRE
- **CI (Continuous Integration)** : Automatisation des tests.
- **CD (Continuous Deployment)** : Automatisation de la mise en ligne.
- **Scrape** : Action de Prometheus de venir lire les métriques sur `/api/metrics`.
- **Latency** : Temps mis par l'application pour répondre à une requête.
- **P95** : Indicateur de performance excluant les 5% de requêtes les plus lentes.
- **OCR** : Optical Character Recognition — reconnaissance de texte sur les images de reçus.

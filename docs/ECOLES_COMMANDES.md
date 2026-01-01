# 🛠️ Commandes Utiles - Module Écoles

## 📦 Installation & Configuration

### Installer les dépendances
```bash
composer install
```

### Copier le fichier d'environnement
```bash
copy .env.example .env
```

### Générer la clé d'application
```bash
php artisan key:generate
```

### Exécuter les migrations
```bash
php artisan migrate
```

### Peupler la base de données
```bash
# Toutes les seeders
php artisan db:seed

# Uniquement les écoles
php artisan db:seed --class=EcoleSeeder
```

## 🧪 Tests

### Lancer tous les tests
```bash
php artisan test
```

### Lancer uniquement les tests du module Écoles
```bash
php artisan test --filter EcoleTest
```

### Tests avec coverage
```bash
php artisan test --filter EcoleTest --coverage
```

### Tests en mode verbose
```bash
php artisan test --filter EcoleTest -v
```

## 🔍 Vérifications

### Lister les routes du module
```bash
php artisan route:list --path=ecoles
```

### Vérifier la configuration
```bash
php artisan config:cache
php artisan route:cache
```

### Nettoyer les caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🗄️ Base de Données

### Créer une migration
```bash
php artisan make:migration create_ecoles_table
```

### Rollback dernière migration
```bash
php artisan migrate:rollback
```

### Réinitialiser et re-migrer
```bash
php artisan migrate:fresh
```

### Réinitialiser avec seeders
```bash
php artisan migrate:fresh --seed
```

### Vérifier le statut des migrations
```bash
php artisan migrate:status
```

## 🏭 Génération de Code

### Créer un modèle avec migration et factory
```bash
php artisan make:model Ecole -mf
```

### Créer un contrôleur
```bash
php artisan make:controller EcoleController --api
```

### Créer une Request
```bash
php artisan make:request StoreEcoleRequest
```

### Créer une Resource
```bash
php artisan make:resource EcoleResource
```

### Créer un Seeder
```bash
php artisan make:seeder EcoleSeeder
```

### Créer une Factory
```bash
php artisan make:factory EcoleFactory
```

### Créer un Test
```bash
php artisan make:test EcoleTest
```

## 🐛 Debugging

### Afficher les logs en temps réel
```bash
php artisan pail
```

### Tinker (console interactive)
```bash
php artisan tinker
```

### Exemples Tinker
```php
// Créer une école
$ecole = App\Models\Ecole::factory()->create();

// Lister les écoles
App\Models\Ecole::all();

// Trouver une école
App\Models\Ecole::find('uuid');

// Compter les écoles
App\Models\Ecole::count();

// Écoles actives
App\Models\Ecole::where('est_actif', true)->get();
```

## 🚀 Serveur de Développement

### Démarrer le serveur
```bash
php artisan serve
```

### Démarrer sur un port spécifique
```bash
php artisan serve --port=8080
```

### Démarrer avec un hôte spécifique
```bash
php artisan serve --host=0.0.0.0
```

## 📊 Analyse de Code

### Formater le code (Laravel Pint)
```bash
./vendor/bin/pint
```

### Formater un fichier spécifique
```bash
./vendor/bin/pint app/Http/Controllers/EcoleController.php
```

### Vérifier sans modifier
```bash
./vendor/bin/pint --test
```

## 🔐 Authentification

### Créer un utilisateur de test
```bash
php artisan tinker
```
```php
$user = App\Models\User::factory()->create([
    'email' => 'test@example.com',
    'password' => bcrypt('password')
]);
```

### Générer un token Sanctum
```php
$token = $user->createToken('test-token')->plainTextToken;
echo $token;
```

## 📝 Git

### Voir le statut
```bash
git status
```

### Ajouter tous les fichiers
```bash
git add -A
```

### Commit
```bash
git commit -m "feat: Implémentation complète du module Écoles avec architecture DDD"
```

### Push vers la branche
```bash
git push origin feature/ecoles
```

### Créer une Pull Request
```bash
# Via GitHub CLI
gh pr create --title "Module Écoles" --body "Implémentation complète avec DTOs, Services, Tests"
```

## 🧹 Maintenance

### Optimiser l'autoloader
```bash
composer dump-autoload -o
```

### Mettre à jour les dépendances
```bash
composer update
```

### Vérifier les dépendances obsolètes
```bash
composer outdated
```

### Analyser la sécurité
```bash
composer audit
```

## 📦 Production

### Optimiser pour la production
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Mettre en mode maintenance
```bash
php artisan down
```

### Sortir du mode maintenance
```bash
php artisan up
```

## 🔧 Utilitaires

### Générer un UUID
```bash
php artisan tinker
```
```php
echo Str::uuid();
```

### Vérifier la connexion DB
```bash
php artisan db:show
```

### Lister les tables
```bash
php artisan db:table ecoles
```

## 📚 Documentation

### Générer la documentation API (si Scribe installé)
```bash
php artisan scribe:generate
```

### Lister toutes les commandes Artisan
```bash
php artisan list
```

### Aide sur une commande
```bash
php artisan help migrate
```

## 🎯 Commandes Rapides du Module

### Setup complet
```bash
composer install && php artisan migrate && php artisan db:seed --class=EcoleSeeder
```

### Test & Vérification
```bash
php artisan test --filter EcoleTest && php artisan route:list --path=ecoles
```

### Réinitialisation complète
```bash
php artisan migrate:fresh --seed && php artisan test --filter EcoleTest
```

### Nettoyage complet
```bash
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear
```

## 🌐 API Testing avec cURL

### Créer une école
```bash
curl -X POST http://localhost:8000/api/ecoles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code_ecole": "TEST",
    "libelle_ecole": "École de Test",
    "region": "CENTRE",
    "localisation": "Yaoundé"
  }'
```

### Lister les écoles
```bash
curl -X GET "http://localhost:8000/api/ecoles?per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Afficher une école
```bash
curl -X GET http://localhost:8000/api/ecoles/{uuid} \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Mettre à jour une école
```bash
curl -X PUT http://localhost:8000/api/ecoles/{uuid} \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"libelle_ecole": "Nouveau Nom"}'
```

### Supprimer une école
```bash
curl -X DELETE http://localhost:8000/api/ecoles/{uuid} \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Toggle statut
```bash
curl -X PATCH http://localhost:8000/api/ecoles/{uuid}/toggle-status \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 💡 Astuces

### Watcher pour les tests
```bash
php artisan test --filter EcoleTest --watch
```

### Logs en temps réel avec filtrage
```bash
tail -f storage/logs/laravel.log | grep -i ecole
```

### Compter les lignes de code
```bash
# Windows PowerShell
(Get-ChildItem -Path app/Services/Ecoles -Recurse -Include *.php | Get-Content | Measure-Object -Line).Lines
```

### Rechercher dans le code
```bash
# Windows PowerShell
Select-String -Path app/**/*.php -Pattern "EcoleService"
```

---

**Note :** Remplacez `YOUR_TOKEN` par un vrai token Sanctum et `{uuid}` par un UUID valide d'école.

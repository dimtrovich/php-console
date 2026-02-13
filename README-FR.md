# Dimtrovich Console

[![Latest Version](https://img.shields.io/packagist/v/dimtrovich/console.svg?style=flat-square)](https://packagist.org/packages/dimtrovich/console)
[![Total Downloads](https://img.shields.io/packagist/dt/dimtrovich/console.svg?style=flat-square)](https://packagist.org/packages/dimtrovich/console)
[![License](https://img.shields.io/packagist/l/dimtrovich/console.svg?style=flat-square)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/packagist/php-v/dimtrovich/console.svg?style=flat-square)](https://php.net)

[English](README.md) | **Français**

Un constructeur d'applications console puissant et riche en fonctionnalités pour PHP. Construit sur [adhocore/cli](https://github.com/adhocore/php-cli), Dimtrovich Console fournit une interface élégante et intuitive pour créer des outils en ligne de commande avec des fonctionnalités avancées comme l'art ASCII, les barres de progression, les menus interactifs et un formatage de sortie magnifique.

## 📦 Installation

```bash
composer require dimtrovich/console
```

## 🚀 Démarrage Rapide

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dimtrovich\Console\Application;
use Dimtrovich\Console\Command;

// Créer une commande simple
class SalutationCommande extends Command
{
    protected string $name = 'salut';
    protected string $description = 'Saluer quelqu\'un';
    protected array $arguments = [
        'nom' => ['Le nom de la personne à saluer']
    ];

    public function handle()
    {
        $nom = $this->argument('nom', 'Monde');
        $this->success("Bonjour, {$nom} !");
        return 0;
    }
}

// Créer et exécuter l'application
$app = Application::create('Mon Outil CLI', '1.0.0')
    ->withLocale('fr') // Utiliser les traductions françaises
    ->withCommands([SalutationCommande::class])
    ->run();

exit($app);
```

## 📚 Table des Matières

- [Concepts Fondamentaux](README.md#concepts-fondamentaux)
- [Création de Commandes](README.md#création-de-commandes)
- [Gestion des Entrées](README.md#gestion-des-entrées)
- [Formatage de Sortie](README.md#formatage-de-sortie)
- [Fonctionnalités Avancées](README.md#fonctionnalités-avancées)
- [Composants](README.md#composants)
- [Art ASCII](README.md#art-ascii)
- [Thèmes et Styles](README.md#thèmes-et-styles)
- [Internationalisation (i18n)](README.md#internationalisation-i18n)
- [Configuration de l'Application](README.md#configuration-de-lapplication)
- [Intégration des Logs](README.md#intégration-des-logs)
- [Tests](README.md#tests)
- [Référence API](README.md#référence-api)

---

## Concepts Fondamentaux

Dimtrovich Console est construit autour de plusieurs concepts clés :

- **Application** : Point d'entrée principal qui gère les commandes et la configuration
- **Commande** : Tâches exécutables individuelles avec arguments et options
- **Entrée/Sortie** : Interaction riche avec l'utilisateur via diverses méthodes
- **Composants** : Éléments d'interface réutilisables comme les alertes, badges et barres de progression

## Création de Commandes

### Structure de Base d'une Commande

```php
<?php

use Dimtrovich\Console\Command;

class UtilisateurCommande extends Command
{
    protected string $name = 'utilisateur:creer';
    protected string $group = 'Gestion Utilisateurs';
    protected string $description = 'Créer un nouvel utilisateur';
    protected string $alias = 'u:c';
    protected string $version = '1.0.0';
    protected string $usage = 'utilisateur:creer [options] [--] <nom> <email>';

    protected array $arguments = [
        'nom'   => ['Le nom complet de l\'utilisateur'],
        'email' => ['L\'adresse email de l\'utilisateur', 'default@exemple.com']
    ];

    protected array $options = [
        '--admin'  => ['Donner les privilèges admin', false],
        '--role'   => ['Rôle utilisateur', 'utilisateur', 'strval'],
        '--actif'  => ['Activer l\'utilisateur', true, 'boolval']
    ];

    public function handle()
    {
        $nom = $this->argument('nom');
        $email = $this->argument('email');
        $estAdmin = $this->option('admin');
        
        $this->info("Création de l'utilisateur : {$nom} ({$email})");
        
        if ($estAdmin) {
            $this->warn('Cet utilisateur aura les privilèges admin');
        }
        
        // Votre logique ici
        
        $this->success('Utilisateur créé avec succès !');
        return 0;
    }
}
```

## Gestion des Entrées

### Invites de Base

```php
class InteractiveCommande extends Command
{
    public function handle()
    {
        // Invite simple
        $nom = $this->ask('Quel est votre nom ?', 'Invité');
        
        // Entrée masquée (mot de passe)
        $motDePasse = $this->secret('Entrez votre mot de passe :');
        
        // Confirmation
        if ($this->confirm('Voulez-vous continuer ?', 'o')) {
            $this->info('Continuation...');
        }
        
        // Choix parmi des options
        $couleur = $this->choice(
            'Couleur préférée ?',
            ['rouge' => 'Rouge', 'bleu' => 'Bleu', 'vert' => 'Vert'],
            'bleu'
        );
        
        // Choix multiples
        $couleurs = $this->choices(
            'Sélectionnez des couleurs :',
            ['r' => 'Rouge', 'v' => 'Vert', 'b' => 'Bleu']
        );
        
        // Auto-complétion
        $pays = $this->askWithCompletion(
            'Pays :',
            ['France', 'Canada', 'Belgique', 'Suisse'],
            'France'
        );
    }
}
```

## Formatage de Sortie

### Tableaux

```php
class TableauCommande extends Command
{
    public function handle()
    {
        $utilisateurs = [
            ['Jean Dupont', 30, 'Paris'],
            ['Marie Martin', 25, 'Lyon'],
            ['Pierre Durand', 35, 'Marseille']
        ];
        
        // Tableau simple
        $this->table(['Nom', 'Âge', 'Ville'], $utilisateurs);
    }
}
```

**Sortie :**
```
Nom          Âge  Ville
Jean Dupont  30   Paris
Marie Martin 25   Lyon
Pierre Durand 35  Marseille
```

### Grille

```php
class GrilleCommande extends Command
{
    public function handle()
    {
        $donnees = [
            ['Produit', 'Prix', 'Stock', 'Statut'],
            ['Ordinateur', 999.99, 15, 'En Stock'],
            ['Souris', 29.99, 42, 'En Stock'],
            ['Clavier', 79.99, 8, 'Stock Faible'],
            ['Écran', 299.99, 0, 'Rupture']
        ];
        
        $this->grid($donnees);
    }
}
```

**Sortie :**
```
Produit     Prix   Stock  Statut      
Ordinateur  999.99 15     En Stock    
Souris      29.99  42     En Stock    
Clavier     79.99  8      Stock Faible
Écran       299.99 0      Rupture
```

## Fonctionnalités Avancées

### Barres de Progression

```php
class ProgressionCommande extends Command
{
    public function handle()
    {
        // Avec tableau
        $elements = range(1, 100);
        $this->withProgressBar($elements, function($element, $bar) {
            usleep(50000); // Simulation de travail
        });
        
        // Avec compteur manuel
        $this->withProgressBar(50, function($bar) {
            for ($i = 0; $i < 50; $i++) {
                usleep(50000);
                $bar->advance();
            }
        });
        
        // Barre de progression manuelle
        $bar = $this->progress(100);
        for ($i = 0; $i < 100; $i++) {
            $bar->advanceWithMessage(1, "Traitement élément {$i}");
            usleep(50000);
        }
        $bar->finish();
        $bar->showStats();
    }
}
```

**Sortie :**
```
[████████░░░░░░░░░░░░░░░░░░░░░] 25%
Statistiques : 100 éléments en 5.23s (19.12 éléments/s)
Messages :
  • Traitement élément 0
  • Traitement élément 25
  • Traitement élément 50
  • Traitement élément 75
```

### Chronologies

```php
class ChronologieCommande extends Command
{
    public function handle()
    {
        $evenements = [
            ['status' => 'completed', 'description' => 'Base de données migrée'],
            ['status' => 'processing', 'description' => 'Cache vidé'],
            ['status' => 'failed', 'description' => 'Assets compilés'],
            ['description' => 'Attente du serveur']
        ];
        
        $this->timeline($evenements);
    }
}
```

**Sortie :**
```
Chronologie :
  ✓ Base de données migrée
  ↻ Cache vidé
  ✗ Assets compilés
  ○ Attente du serveur
```

### Graphiques

```php
class GraphiqueCommande extends Command
{
    public function handle()
    {
        $donnees = ['Linux' => 50, 'Windows' => 30, 'Mac' => 20];
        
        // Diagramme à barres
        $this->chart($donnees, 'bar');
        
        // Diagramme circulaire
        $this->chart($donnees, 'pie');
    }
}
```

**Sortie (Diagramme à barres) :**
```
Linux                ████████████████████████████████████████████ 50
Windows              ████████████████████████████████ 30
Mac                  ████████████████████ 20
```

**Sortie (Diagramme circulaire) :**
```
Graphique Circulaire
  Linux : 50.0%
  Windows : 30.0%
  Mac : 20.0%
```

## Composants

### Alertes

```php
use Dimtrovich\Console\Components\Alert;

class AlerteCommande extends Command
{
    public function handle()
    {
        $alerte = $this->alert();
        
        $alerte->info('Le système fonctionne', 'État Système');
        $alerte->success('Opération terminée avec succès');
        $alerte->warning('Espace disque faible', 'Attention');
        $alerte->error('Connexion à la base de données échouée', 'Erreur');
        $alerte->danger('Erreur critique', 'DANGER');
    }
}
```

**Sortie :**
```
**************************
*     ÉTAT SYSTÈME       *
*  Le système fonctionne  *
**************************

**********************
*      SUCCÈS        *
* Opération terminée *
**********************
```

### Badges

```php
use Dimtrovich\Console\Components\Badge;

class BadgeCommande extends Command
{
    public function handle()
    {
        $badge = $this->badge();
        
        $badge->info('Le système fonctionne', 'SYSTÈME');
        $badge->success('Tâche terminée', 'TERMINÉ');
        $badge->warning('Mémoire faible', 'ATTENTION');
        $badge->error('Connexion échouée', 'ERREUR');
        
        // Badges contour
        $badge->outline('Message contour', 'CONTOUR', 'bleu');
        
        // Badges pilule
        $badge->pill('Message pilule', 'PILULE', 'info');
    }
}
```

**Sortie :**
```
[SYSTÈME] Le système fonctionne
[TERMINÉ] Tâche terminée
(ATTENTION) Mémoire faible
(INFO) Message pilule
```

### Icônes

```php
use Dimtrovich\Console\Icon;

class IcôneCommande extends Command
{
    public function handle()
    {
        $this->alert()->success('Utilisateur créé', 'UTILISATEUR', Icon::USER);
        $this->badge()->info('Téléchargement terminé', 'FICHIER', Icon::DOWNLOAD);
    }
}
```

**Sortie avec icônes :**
```
👤 UTILISATEUR
📁 Téléchargement terminé
```

## Art ASCII

```php
use Dimtrovich\Console\Traits\AsciiArt;

class AsciiCommande extends Command
{
    use AsciiArt; // Fonctionnalité optionnelle
    
    public function handle()
    {
        // Art ASCII de base
        $this->asciiArt('BIENVENUE');
        
        // Avec police spécifique
        $this->asciiArt('BONJOUR', 'grand');
        
        // Créer une bannière
        $this->banner('IMPORTANT', '*', 'grand');
    }
}
```

**Sortie (police standard) :**
```
  ██    ██  █████  ██
█   █  █  █  █     ██
```

**Sortie bannière :**
```
********************
*    IMPORTANT     *
********************
```

## Thèmes et Styles

Dimtrovich Console est livré avec 8 thèmes intégrés, chacun soigneusement conçu pour différents environnements et préférences.

### Thèmes Disponibles

| Thème | Description | Aperçu |
|-------|-------------|--------|
| `default` | Style original adhocore/cli | Couleurs classiques et équilibrées |
| `light` | Optimisé pour fonds clairs | Couleurs foncées sur fond clair |
| `dark` | Optimisé pour fonds sombres | Haut contraste, reposant |
| `solarized` | Palette populaire d'Ethan Schoonover | Parfait pour longues sessions de code |
| `monokai` | Thème vibrant de coloration syntaxique | Populaire chez les développeurs |
| `nord` | Palette arctique bleutée | Propre et calme |
| `dracula` | Thème sombre aux couleurs vives | Palette accrocheuse |
| `github` | Couleurs familières de GitHub | Propre et professionnel |

### Application des Thèmes

```php
$app = Application::create('MonApp', '1.0.0')
    ->withTheme('dark')      // Thème sombre
    ->withTheme('solarized') // Thème solarisé
    ->withTheme('monokai')   // Thème monokai
    ->withTheme('github');   // Thème GitHub
```

### Styles Personnalisés

```php
$app->withStyles([
    'en_tete_aide' => ['fg' => 'vert', 'bold' => 1],
    'erreur'       => ['fg' => 'rouge', 'bg' => 'noir'],
    'bleu_perso'   => ['fg' => 69], // Couleur 256
]);
```

## Internationalisation (i18n)

### Locales Intégrées

| Locale | Langue | Fichier |
|--------|--------|---------|
| `en` | Anglais | (intégré) |
| `fr` | Français | `fr.php` |

### Définir la Locale

```php
$app = Application::create('MonApp', '1.0.0')
    ->withLocale('fr'); // Utiliser les traductions françaises
```

### Traductions Personnalisées

```php
$app->withTranslations('fr', [
    'Hello %s' => 'Bonjour %s',
    'Goodbye'  => 'Au revoir',
    'Welcome to %s' => 'Bienvenue sur %s',
], true); // true = définir comme locale par défaut
```

## Configuration de l'Application

```php
use Dimtrovich\Console\Application;

$app = Application::create('Mon App Console', '2.1.0')
    ->withLocale('fr')                       // Utiliser les traductions françaises
    ->withTheme('dracula')                   // Utiliser le thème Dracula
    ->withIcons(true, false, true)           // Configurer les icônes par défaut
    ->withLogo("                             // Logo ASCII
   _____ _ _ _        _____ _    _ _____
  |  ___) (_) |      / ____| |  | |  __ \
  | |__ | ||_ _| | | |    | |__| | |__) |
  ")
    ->withHeadTitle('Mon Outil CLI v2')      // En-tête personnalisé
    ->withFooter()                           // Afficher le pied de page d'aide
    ->withDebug()                            // Activer le mode débogage
    ->withCommands([
        CreerCommande::class,
        ServirCommande::class,
        ListerRoutesCommande::class,
    ])
    ->withDefaultCommand('aide');              // Commande par défaut
```

### Configuration des Icônes

```php
$app->withIcons(
    alert: true,   // Activer les icônes par défaut pour les alertes
    badge: false,  // Désactiver les icônes par défaut pour les badges
    logger: true   // Activer les icônes par défaut pour les logs
);
```

Les appels individuels peuvent surcharger :
```php
$this->alert()->success('Terminé', 'SUCCÈS', Icon::STAR); // Forcer icône étoile
$this->badge()->info('Message', 'INFO', false);           // Pas d'icône pour ce badge
```

## Intégration des Logs

### Journalisation de Base

```php
class JournalisationCommande extends Command
{
    public function handle()
    {
        // Journal avec préfixe par défaut
        $this->log()->info('Utilisateur connecté', ['user_id' => 123]);
        $this->log()->error('Connexion à la base de données échouée');
        
        // Succès (niveau info avec style vert)
        $this->log()->success('Opération terminée');
        
        // Alias
        $this->log()->warn('Méthode dépréciée');
        $this->log()->danger('Erreur critique');
    }
}
```

**Sortie console :**
```
ℹ INFO  Utilisateur connecté
✓ SUCCÈS Opération terminée
⚠ ATTENTION Méthode dépréciée
✗ ERREUR  Erreur critique
```

### Préfixes de Log

```php
class BaseDonneesCommande extends Command
{
    public function handle()
    {
        // Créer un logger avec préfixe
        $logDB = $this->log('DB');
        $logDB->info('Connexion à la base de données');
        
        // Chaîner les préfixes
        $this->log('APP')
            ->withPrefix('CACHE')
            ->info('Cache vidé');
        // Sortie : [APP > CACHE] Cache vidé
    }
}
```

### Configuration

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$monolog = new Logger('console');
$monolog->pushHandler(new StreamHandler('logs/console.log'));

$app = Application::create('MonApp')
    ->withLogger($monolog, 'APP') // Tous les logs préfixés par [APP]
    ->withCommands([...])
    ->run();
```

## Tests

### Test de Sortie Console

```php
use Tests\Helpers\ConsoleOutput;

class MaCommandeTest extends TestCase
{
    public function testSortieCommande()
    {
        ConsoleOutput::setUp();
        
        $app = Application::create('Test');
        $app->withCommands([MaCommande::class]);
        $app->call('ma:commande');
        
        $sortie = ConsoleOutput::buffer();
        $this->assertStringContainsString('Succès', $sortie);
        
        ConsoleOutput::tearDown();
    }
}
```

## Référence API

### Méthodes de la Classe Command

| Méthode | Description |
|--------|-------------|
| `handle()` | Logique principale de la commande (abstraite) |
| `argument(string $nom, $defaut = null)` | Obtenir la valeur d'un argument |
| `arguments()` | Obtenir tous les arguments |
| `hasArgument(string $nom)` | Vérifier si un argument existe |
| `option(string $nom, $defaut = null)` | Obtenir la valeur d'une option |
| `options()` | Obtenir toutes les options |
| `hasOption(string $nom)` | Vérifier si une option existe |
| `parameter(string $nom, $defaut = null)` | Obtenir argument ou option |
| `call(string $commande, array $args = [], array $opts = [])` | Appeler une autre commande |
| `commandExists(string $nom)` | Vérifier si une commande existe |

### Méthodes d'Entrée

| Méthode | Description |
|--------|-------------|
| `ask(string $question, $defaut = null)` | Demander une entrée |
| `secret(string $texte, callable $fn = null, int $essais = 3)` | Demander une entrée masquée |
| `confirm(string $question, string $defaut = 'o')` | Demander une confirmation |
| `choice(string $question, array $choix, $defaut = null, bool $case = false)` | Choix unique |
| `choices(string $question, array $choix, $defaut = null, bool $case = false)` | Choix multiples |

### Méthodes de Sortie

| Méthode | Description |
|--------|-------------|
| `info(string $message)` | Message d'information |
| `success(string $message)` | Message de succès |
| `warn(string $message)` | Message d'avertissement |
| `error(string $message)` | Message d'erreur |
| `table(array $enTetes, array $lignes = [], array $styles = [])` | Afficher un tableau |
| `grid(array $donnees, ?callable $formateur = null)` | Afficher une grille |
| `json($donnees)` | Afficher du JSON formaté |
| `timeline(array $evenements)` | Afficher une chronologie |
| `chart(array $donnees, string $type = 'bar', int $hauteur = 10)` | Afficher un graphique |
| `heatmap(array $donnees, array $couleurs = ['░', '▒', '▓', '█'])` | Afficher une carte de chaleur |

### Méthodes de l'Application

| Méthode | Description |
|--------|-------------|
| `create(string $nom, string $version = '1.0.0')` | Créer l'application |
| `withLocale(string $locale)` | Définir la locale |
| `withTranslations(string $locale, array $traductions, bool $defaut = false)` | Ajouter des traductions |
| `withTheme(string $theme)` | Appliquer un thème |
| `withIcons(?bool $alerte = null, ?bool $badge = null, ?bool $logger = null)` | Configurer les icônes |
| `withLogger(LoggerInterface $logger, string $prefixe = '')` | Définir le logger PSR |
| `withCommands(array $commandes)` | Enregistrer des commandes |
| `run(array $argv = [])` | Exécuter l'application |

---

## 📄 Licence

Le package Dimtrovich Console est un logiciel open-source sous licence [MIT](https://opensource.org/licenses/MIT).	

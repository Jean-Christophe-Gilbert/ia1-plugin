# Guide développeur — IA1

## Architecture

IA1 suit une structure MVC modulaire standard pour un plugin WordPress :

```
includes/                      # Logique métier
├── class-ia1-settings.php     # Gestion des options
├── class-ia1-indexer.php      # Indexation & scoring
└── class-ia1-mistral.php      # API Mistral AI

admin/                         # Interface back-office
├── class-ia1-admin.php        # Contrôleur admin
├── views/                     # Templates admin
├── css/                       # Styles admin
└── js/                        # JS admin

public/                        # Frontend (visiteurs)
├── class-ia1-public.php       # Contrôleur public
├── views/                     # Template du widget
├── css/                       # Styles du chat
└── js/                        # JS du chat
```

---

## Hooks et filtres

### Actions disponibles

```php
// Appelée après l'indexation d'un post
do_action( 'ia1_post_indexed', $post_id );

// Appelée après une réindexation complète
do_action( 'ia1_reindexed_all', $stats );
```

### Filtres disponibles

```php
// Modifier les paramètres de personnalisation
$settings = apply_filters( 'ia1_customization_settings', $settings );

// Modifier le prompt système avant envoi à Mistral
$prompt = apply_filters( 'ia1_system_prompt', $prompt, $question );

// Modifier la réponse de Mistral avant affichage au visiteur
$response = apply_filters( 'ia1_mistral_response', $response, $question );
```

---

## Base de données

### Table `wp_ia1_index`

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | bigint(20) | ID unique |
| `post_id` | bigint(20) | ID du post WordPress |
| `post_type` | varchar(20) | Type de contenu (post, page, product) |
| `title` | text | Titre du contenu |
| `content` | longtext | Contenu indexé (HTML nettoyé) |
| `url` | varchar(255) | URL du contenu |
| `indexed_at` | datetime | Date d'indexation |

---

## Options WordPress

Toutes les options sont préfixées par `ia1_`.

### API & comportement

| Option | Description | Défaut |
|--------|-------------|--------|
| `ia1_api_key` | Clé API Mistral | — |
| `ia1_model` | Modèle utilisé | mistral-large |
| `ia1_temperature` | Température (créativité) | 0.5 |
| `ia1_max_contexts` | Nombre de contextes | 5 |
| `ia1_system_prompt` | Instructions système | — |

### Personnalisation

| Option | Description |
|--------|-------------|
| `ia1_assistant_name` | Nom de l'assistant |
| `ia1_assistant_subtitle` | Sous-titre |
| `ia1_welcome_message` | Message d'accueil |
| `ia1_primary_color` | Couleur principale (hex) |
| `ia1_avatar_initials` | Initiales avatar (1–3 caractères) |

### Système

| Option | Description |
|--------|-------------|
| `ia1_version` | Version du plugin |
| `ia1_migrated_from_lydia` | Flag de migration effectuée |

---

## Ajouter une fonctionnalité

### Nouvelle classe

1. Créer le fichier dans `includes/` :
```php
// includes/class-ia1-ma-fonctionnalite.php
class IA1_Ma_Fonctionnalite {
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {
        // votre logique
    }
}
```

2. La charger dans `ia1-ai-plugin.php` :
```php
require_once IA1_PLUGIN_DIR . 'includes/class-ia1-ma-fonctionnalite.php';
```

3. L'instancier dans le constructeur du plugin principal.

### Nouvelle page admin

1. Enregistrer dans `class-ia1-admin.php` :
```php
add_submenu_page( 'ia1', 'Ma Page', 'Ma Page', 'manage_options', 'ia1-ma-page', array( $this, 'render_ma_page' ) );
```

2. Créer la vue dans `admin/views/`.

### Modifier le widget

- Structure HTML : `public/views/chat-widget.php`
- Styles : `public/css/ia1-chat.css`
- Comportement : `public/js/ia1-chat.js`

---

## Debug

Activer le mode debug WordPress dans `wp-config.php` :

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Les erreurs sont enregistrées dans `wp-content/debug.log`.  
Le plugin écrit aussi ses propres logs dans `ia1-debug.log` via `ia1_log()`.

---

## Contribuer

1. Fork le projet sur GitHub
2. Créer une branche : `git checkout -b feature/amelioration`
3. Commiter : `git commit -m 'Description du changement'`
4. Pusher : `git push origin feature/amelioration`
5. Ouvrir une Pull Request

---

## Support développeur

- **Email** : jc@ia1.fr
- **GitHub Issues** : [github.com/Jean-Christophe-Gilbert/ia1-plugin/issues](https://github.com/Jean-Christophe-Gilbert/ia1-plugin/issues)

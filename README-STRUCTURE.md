# IA1 Plugin - Structure Complète v3.1.0

## 📁 Structure des fichiers

```
ia1-plugin/
├── ia1-plugin.php                    # Fichier principal (renommé depuis ia1-ai-plugin.php)
│
├── includes/                         # Classes principales
│   ├── class-ia1-settings.php        # Gestion des options/paramètres
│   ├── class-ia1-indexer.php         # Indexation et recherche
│   ├── class-ia1-mistral.php         # API Mistral AI
│   └── class-ia1-core.php            # (Optionnel) Logique commune
│
├── admin/                            # Interface d'administration
│   ├── class-ia1-admin.php           # Contrôleur admin
│   ├── views/
│   │   ├── admin-page.php            # Page de configuration
│   │   └── indexation-page.php       # Page d'indexation
│   ├── css/
│   │   └── ia1-admin.css             # Styles admin
│   └── js/
│       └── ia1-admin.js              # JavaScript admin (prévisualisation temps réel)
│
├── public/                           # Frontend (visible par les visiteurs)
│   ├── class-ia1-public.php          # Contrôleur public
│   ├── views/
│   │   └── chat-widget.php           # Template du widget de chat
│   ├── css/
│   │   └── ia1-chat.css              # Styles du chat
│   └── js/
│       └── ia1-chat.js               # JavaScript du chat
│
├── languages/                        # Traductions (à venir)
│   └── ia1-fr_FR.po
│
├── README.md                         # Ce fichier
├── CHANGELOG.md                      # Historique des versions
├── MIGRATION.md                      # Guide de migration depuis Lydia
└── LICENSE                           # Licence GPL v3
```

## 🎯 Nouvelles fonctionnalités v3.1.0

### ✨ Personnalisation complète

Les utilisateurs peuvent maintenant personnaliser leur assistant IA :

- **Nom de l'assistant** : Remplacer "IA1" par n'importe quel nom
- **Sous-titre** : Description courte sous le nom
- **Message d'accueil** : Premier message personnalisé
- **Couleur principale** : Couleur de l'en-tête et des boutons
- **Initiales avatar** : 1-3 caractères dans l'avatar

### 🎨 Prévisualisation en temps réel

L'interface d'administration affiche un aperçu du widget qui se met à jour en direct pendant que l'utilisateur tape.

### 📊 Page d'indexation dédiée

Nouvelle page avec :
- Statistiques de l'indexation
- Bouton de réindexation manuelle
- Barre de progression animée

## 🔧 Installation

1. **Télécharger le plugin**
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/Jean-Christophe-Gilbert/ia1-plugin.git
   ```

2. **Activer dans WordPress**
   - Aller dans Extensions → Extensions installées
   - Activer "IA1"

3. **Configurer**
   - Aller dans IA1 → Configuration
   - Ajouter la clé API Mistral
   - Personnaliser l'assistant
   - Sauvegarder

4. **Indexer le contenu**
   - Aller dans IA1 → Indexation
   - Cliquer sur "Réindexer tout le contenu"

5. **Intégrer le chat**
   - Créer une page "Assistant IA"
   - Ajouter le shortcode `[ia1_chat]`
   - Publier

## 📝 Utilisation

### Shortcodes disponibles

```php
// Shortcode basique
[ia1_chat]

// Avec attributs personnalisés
[ia1_chat placeholder="Posez votre question..." height="600px"]

// Ancien shortcode (rétrocompatibilité)
[lydia_chat]
```

### Paramètres disponibles

| Paramètre | Description | Défaut |
|-----------|-------------|--------|
| `placeholder` | Texte du champ de saisie | "Demander à [Nom]" |
| `height` | Hauteur du widget | "500px" |

## 🔌 Hooks et filtres

### Actions

```php
// Après l'indexation d'un post
do_action( 'ia1_post_indexed', $post_id );

// Après une réindexation complète
do_action( 'ia1_reindexed_all', $stats );
```

### Filtres

```php
// Modifier les paramètres de personnalisation
apply_filters( 'ia1_customization_settings', $settings );

// Modifier le prompt système
apply_filters( 'ia1_system_prompt', $prompt, $question );

// Modifier la réponse de Mistral avant affichage
apply_filters( 'ia1_mistral_response', $response, $question );
```

## 🗄️ Base de données

### Table `wp_ia1_index`

Contient l'index des contenus :

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | bigint(20) | ID unique |
| `post_id` | bigint(20) | ID du post WordPress |
| `post_type` | varchar(20) | Type de contenu (post, page, product) |
| `title` | text | Titre du contenu |
| `content` | longtext | Contenu indexé (nettoyé) |
| `url` | varchar(255) | URL du contenu |
| `embedding` | longtext | (Future) Embedding vectoriel |
| `indexed_at` | datetime | Date d'indexation |

## ⚙️ Options WordPress

Toutes les options sont préfixées par `ia1_` :

### API Mistral
- `ia1_api_key` : Clé API
- `ia1_model` : Modèle utilisé
- `ia1_temperature` : Température (créativité)
- `ia1_max_contexts` : Nombre de contextes
- `ia1_system_prompt` : Instructions système
- `ia1_use_wikipedia` : Utiliser Wikipedia

### Personnalisation
- `ia1_assistant_name` : Nom de l'assistant
- `ia1_assistant_subtitle` : Sous-titre
- `ia1_welcome_message` : Message d'accueil
- `ia1_primary_color` : Couleur principale
- `ia1_avatar_initials` : Initiales avatar

### Système
- `ia1_version` : Version du plugin
- `ia1_migrated_from_lydia` : Migration effectuée

## 🔄 Migration depuis Lydia

La migration est **automatique** lors de l'activation :

1. Détection des options `lydia_*`
2. Copie vers les options `ia1_*`
3. Migration de la table d'index
4. Conservation du nom "Lydia" par défaut (personnalisable)

## 🛠️ Développement

### Ajouter une nouvelle fonctionnalité

1. Créer une classe dans `includes/`
2. La charger dans `ia1-plugin.php` :
   ```php
   require_once IA1_PLUGIN_DIR . 'includes/class-ia1-ma-classe.php';
   ```
3. Initialiser dans le constructeur de `IA1_Plugin`

### Modifier l'interface admin

1. Éditer `admin/views/admin-page.php` (structure HTML)
2. Éditer `admin/css/ia1-admin.css` (styles)
3. Éditer `admin/js/ia1-admin.js` (comportement)

### Modifier le widget de chat

1. Éditer `public/views/chat-widget.php` (structure HTML)
2. Éditer `public/css/ia1-chat.css` (styles)
3. Éditer `public/js/ia1-chat.js` (comportement)

## 🐛 Debugging

Activer le mode debug WordPress :

```php
// Dans wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Les erreurs seront enregistrées dans `wp-content/debug.log`.

## 📞 Support

- **Email** : jc@ia1.fr
- **Téléphone** : 06 40 75 53 92
- **GitHub** : https://github.com/Jean-Christophe-Gilbert/ia1-plugin
- **Site** : https://ia1.fr

## 📄 Licence

GPL v3 or later - https://www.gnu.org/licenses/gpl-3.0.html

## 🙏 Crédits

- **Mistral AI** : Pour leur excellente API
- **WordPress** : Pour le CMS
- **Communauté open source** : Pour l'inspiration

---

**Développé par IA1** • Propulsé par Mistral AI • Open Source & Souverain

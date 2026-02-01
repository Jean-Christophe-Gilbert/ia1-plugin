# IA1 — Intelligence Artificielle Locale pour WordPress

> 🔄 Anciennement Lydia — Version 3.1.10

Plugin WordPress pour intégrer une intelligence artificielle conversationnelle qui connaît le contenu de votre site. Un service logiciel opéré localement, propulsé par Mistral AI.

## 🚀 Fonctionnalités

- **IA conversationnelle** basée sur Mistral AI (entreprise française, RGPD-compliant)
- **Algorithme intelligent** : scoring multicritère, détection d'intention, prioritisation des hub pages
- **Indexation automatique** de votre contenu WordPress (articles, pages, produits WooCommerce)
- **Interface élégante** responsive et moderne, personnalisable (nom, couleurs, avatar)
- **Citations des sources** avec liens cliquables
- **100% open source** — code transparent et auditable
- **Souveraineté numérique** — vos données restent sur votre serveur

## 📋 Prérequis

- WordPress 5.8 minimum
- PHP 7.4 minimum
- Une clé API Mistral AI (gratuite pour tester sur [console.mistral.ai](https://console.mistral.ai))

## 🔧 Installation rapide

```bash
cd wp-content/plugins/
git clone https://github.com/Jean-Christophe-Gilbert/ia1-plugin.git
```

1. Activer : WordPress Admin → Extensions → Activer "IA1"
2. Configurer : WordPress Admin → IA1 → Coller votre clé API Mistral
3. Indexer : IA1 → Indexation → "Réindexer tout le contenu"
4. Intégrer : ajouter `[ia1_chat]` dans une page

Voir [docs/INSTALLATION.md](docs/INSTALLATION.md) pour le guide complet.

## 📁 Structure du projet

```
ia1-plugin/
├── ia1-ai-plugin.php              # Fichier principal
├── includes/                      # Classes principales
│   ├── class-ia1-settings.php     # Gestion des paramètres
│   ├── class-ia1-indexer.php      # Indexation & scoring intelligent
│   └── class-ia1-mistral.php      # API Mistral AI + détection d'intention
├── admin/                         # Interface d'administration
│   ├── class-ia1-admin.php        # Contrôleur admin
│   ├── views/                     # Pages admin (config, indexation)
│   ├── css/                       # Styles admin
│   └── js/                        # JS admin (prévisualisation temps réel)
├── public/                        # Frontend (visiteurs)
│   ├── class-ia1-public.php       # Contrôleur public
│   ├── views/                     # Template du widget
│   ├── css/                       # Styles du chat
│   └── js/                        # JS du chat
├── docs/                          # Documentation
│   ├── INSTALLATION.md
│   ├── MIGRATION.md
│   ├── ALGORITHME.md
│   └── DEVELOPPEMENT.md
├── README.md                      # Ce fichier
└── CHANGELOG.md                   # Historique des versions
```

## ⚙️ Configuration

Dans **IA1 → Réglages** :

| Paramètre | Description | Défaut |
|-----------|-------------|--------|
| Clé API Mistral | Votre clé API | — |
| Modèle | mistral-small / medium / large | mistral-large |
| Température | Créativité des réponses (0.1–1.0) | 0.5 |
| Nombre de contextes | Passages envoyés à l'IA (3–10) | 5 |

### Shortcode

```
[ia1_chat]
[ia1_chat placeholder="Votre question..." height="400px"]
```

Le shortcode `[lydia_chat]` reste supporté pour la rétrocompatibilité.

## 📊 Comment ça marche

1. **Indexation** : IA1 indexe automatiquement votre contenu WordPress
2. **Question** : Un visiteur pose une question dans le chat
3. **Scoring** : L'algorithme multicritère identifie les passages les plus pertinents (hub pages, détection d'intention, hiérarchie des post types)
4. **IA** : Mistral AI génère une réponse naturelle basée sur ces passages
5. **Sources** : Les sources sont affichées avec des liens cliquables vers vos pages

Voir [docs/ALGORITHME.md](docs/ALGORITHME.md) pour le détail du scoring.

## 💰 Modèle économique

| Couche | Rôle | Monétisation |
|--------|------|--------------|
| **IA1 Core** | Technologie (ce plugin) | Gratuit — open source |
| **IA1 Pro** | Exploitation continue | Abonnement mensuel |
| **IA1 Studio** | Mise en place & stratégie | One-shot |

API Mistral AI : pay-as-you-go, environ 0.001–0.003€/question.  
Exemple : 1000 visiteurs × 2 questions = 6–18€/mois.

Offres détaillées sur [ia1.fr/boutique](https://ia1.fr/boutique/).

## 🔒 Sécurité & Confidentialité

- Vos données restent sur votre serveur WordPress
- Seules les requêtes nécessaires sont envoyées à Mistral AI
- Mistral AI est une entreprise française, RGPD-compliant
- Aucune conversation n'est stockée de manière permanente
- Code 100% open source et auditable

## 🔄 Migration depuis Lydia

La migration depuis Lydia 2.x est **automatique**. Voir [docs/MIGRATION.md](docs/MIGRATION.md).

## 📖 Documentation

| Document | Description |
|----------|-------------|
| [Installation](docs/INSTALLATION.md) | Guide d'installation et configuration |
| [Migration](docs/MIGRATION.md) | Migration depuis Lydia 2.x |
| [Algorithme](docs/ALGORITHME.md) | Scoring intelligent et détection d'intention |
| [Développement](docs/DEVELOPPEMENT.md) | Hooks, base de données, contribution |

## 📞 Support

- **Email** : jc@ia1.fr
- **Téléphone** : 06 40 75 53 92
- **GitHub Issues** : [github.com/Jean-Christophe-Gilbert/ia1-plugin/issues](https://github.com/Jean-Christophe-Gilbert/ia1-plugin/issues)
- **Site** : [ia1.fr](https://ia1.fr)

---

*Développé par IA1 • Propulsé par Mistral AI • Open Source & Souverain*  
*Made with ❤️ in Niort, France 🇫🇷*

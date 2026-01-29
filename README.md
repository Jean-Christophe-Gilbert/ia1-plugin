# IA1 - Intelligence Artificielle Locale pour WordPress

> 🔄 **Anciennement Lydia** - Version 3.0.0

Plugin WordPress pour intégrer une intelligence artificielle conversationnelle qui connaît le contenu de votre site.

## 🚀 Fonctionnalités

- **IA conversationnelle** basée sur Mistral AI (entreprise française, RGPD-compliant)
- **Indexation automatique** de votre contenu WordPress (articles, pages, produits WooCommerce)
- **Interface élégante** responsive et moderne
- **Citations des sources** avec liens cliquables
- **100% open source** - code transparent et auditable
- **Souveraineté numérique** - vos données restent sur votre serveur

## 📋 Prérequis

- WordPress 5.8 minimum
- PHP 7.4 minimum
- Une clé API Mistral AI (gratuite pour tester sur [console.mistral.ai](https://console.mistral.ai))

## 🔧 Installation

1. **Télécharger le plugin**
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/Jean-Christophe-Gilbert/ia1-plugin.git
   ```

2. **Activer le plugin**
   - Aller dans WordPress Admin → Extensions
   - Activer "IA1"

3. **Configurer la clé API**
   - Aller dans WordPress Admin → IA1
   - Coller votre clé API Mistral AI
   - Sauvegarder

4. **Indexer votre contenu**
   - Aller dans IA1 → Indexation
   - Cliquer sur "Réindexer tout le contenu"
   - Attendre la fin de l'indexation

5. **Ajouter le chat sur votre site**
   ```
   [ia1_chat]
   ```
   ou
   ```
   [lydia_chat]  <!-- Ancien shortcode, toujours supporté -->
   ```

## ⚙️ Configuration

### Paramètres disponibles

Dans **IA1** → **Réglages** :

- **Clé API Mistral** : Votre clé d'API Mistral AI
- **Modèle** : Choisir entre mistral-small, mistral-medium, mistral-large
- **Température** : Créativité des réponses (0.1 à 1.0)
- **Nombre de contextes** : Nombre de passages pertinents à envoyer (3-10)

### Shortcode

```
[ia1_chat placeholder="Votre question..." height="400px"]
```

**Attributs :**
- `placeholder` : Texte du placeholder (défaut: "Demander à IA1")
- `height` : Hauteur de la zone de chat (défaut: "350px")

## 🔄 Migration depuis Lydia

Si vous aviez la version 2.x (appelée "Lydia"), la migration est **automatique** :

1. Désactiver l'ancien plugin "Lydia"
2. Installer le nouveau plugin "IA1"
3. Activer "IA1"

Vos réglages et votre index seront automatiquement migrés.

## 💰 Coûts

- **Plugin** : 100% gratuit et open source
- **API Mistral AI** : Pay-as-you-go
  - Crédits gratuits pour tester
  - Environ 0.001€ à 0.003€ par question
  - Exemple : 1000 visiteurs × 2 questions = 6 à 18€/mois

## 📊 Fonctionnement

1. **Indexation** : IA1 indexe automatiquement votre contenu (articles, pages)
2. **Question** : Un visiteur pose une question dans le chat
3. **Recherche** : IA1 trouve les passages les plus pertinents dans votre index
4. **IA** : Mistral AI génère une réponse naturelle basée sur ces passages
5. **Sources** : Les sources sont affichées avec des liens cliquables

## 🔒 Sécurité & Confidentialité

- Vos données restent sur votre serveur WordPress
- Seules les requêtes nécessaires sont envoyées à Mistral AI
- Mistral AI est une entreprise française, RGPD-compliant
- Aucune conversation n'est stockée de manière permanente
- Code 100% open source et auditable

## 🛠️ Développement

### Structure du projet

```
ia1-plugin/
├── ia1-ai-plugin.php    # Fichier principal
├── README.md            # Ce fichier
├── CHANGELOG.md         # Historique des versions
└── assets/              # Ressources (à venir)
```

### Contribuer

Les contributions sont les bienvenues ! N'hésitez pas à :

1. Fork le projet
2. Créer une branche (`git checkout -b feature/amelioration`)
3. Commit vos changements (`git commit -m 'Ajout fonctionnalité'`)
4. Push vers la branche (`git push origin feature/amelioration`)
5. Ouvrir une Pull Request

## 📝 Support

- **Email** : jc@ia1.fr
- **Téléphone** : 06 40 75 53 92
- **Issues GitHub** : [github.com/Jean-Christophe-Gilbert/ia1-plugin/issues](https://github.com/Jean-Christophe-Gilbert/ia1-plugin/issues)

## 📄 Licence

Ce plugin est sous licence open source. Voir le fichier LICENSE pour plus de détails.

## 🙏 Remerciements

- [Mistral AI](https://mistral.ai) pour leur excellente API
- La communauté WordPress
- Tous les contributeurs

---

**Développé par IA1** • **Propulsé par Mistral AI** • **Open Source & Souverain**

Made with ❤️ in Niort, France

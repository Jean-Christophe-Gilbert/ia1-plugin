# 🎉 IA1 v3.0.0 - Renommage du Plugin

**Date de sortie** : 26 janvier 2026

## 🔄 Changement majeur

Cette version marque le **renommage complet** du plugin de **Lydia** vers **IA1** (Intelligence Artificielle 1).

### Pourquoi ce changement ?

Le nom "Lydia" était déjà déposé et utilisé par d'autres services, notamment l'application de paiement mobile française Lydia. Pour éviter toute confusion et respecter les marques existantes, nous avons choisi de rebaptiser notre plugin **IA1**.

## ✨ Ce qui change

### Identité visuelle
- Le plugin s'appelle maintenant **IA1**
- Les menus WordPress affichent "IA1" au lieu de "Lydia IA"
- L'interface utilisateur affiche "IA1" dans tous les messages
- Le message de bienvenue : "Bonjour ! Je suis IA1..."

### Technique
- Fichier principal : `ia1-ai-plugin.php` (au lieu de `lydia-ai-plugin.php`)
- Classe PHP : `IA1_WordPress` (au lieu de `Lydia_WordPress`)
- Constantes : `IA1_VERSION`, `IA1_PLUGIN_DIR`, `IA1_LOG_FILE`
- Fonctions : `ia1_log()`, `ia1_chat()`, etc.
- Options WordPress : `ia1_*` au lieu de `lydia_*`
- Fichier de logs : `ia1-debug.log`

### Shortcodes
- **Nouveau** : `[ia1_chat]` (recommandé)
- **Ancien** : `[lydia_chat]` (toujours supporté pour la rétrocompatibilité)

## 🔒 Ce qui ne change PAS

- ✅ **Toutes les fonctionnalités** restent identiques
- ✅ **L'API Mistral AI** fonctionne exactement de la même manière
- ✅ **Vos réglages** sont automatiquement préservés
- ✅ **Votre clé API** est conservée
- ✅ **Votre index** est maintenu
- ✅ **Le design** de l'interface reste le même
- ✅ **Les performances** sont identiques

## 📦 Installation

### Nouvelle installation

```bash
cd wp-content/plugins/
git clone https://github.com/Jean-Christophe-Gilbert/ia1-plugin.git
```

Puis activer le plugin dans WordPress Admin → Extensions.

### Migration depuis Lydia 2.x

**C'est automatique !** 🎉

1. Désactiver l'ancien plugin "Lydia"
2. Installer le nouveau plugin "IA1"
3. Activer IA1

Vos réglages et votre index seront automatiquement migrés. Voir [MIGRATION.md](MIGRATION.md) pour plus de détails.

## 🚀 Utilisation

### Shortcode simple

```
[ia1_chat]
```

### Shortcode avec options

```
[ia1_chat placeholder="Posez votre question..." height="400px"]
```

### Configuration

1. Aller dans **WordPress Admin → IA1**
2. Entrer votre clé API Mistral AI
3. Sauvegarder
4. Aller dans **IA1 → Indexation**
5. Cliquer sur "Réindexer tout le contenu"

## 📋 Checklist de migration

- [ ] Désactiver l'ancien plugin Lydia
- [ ] Installer IA1
- [ ] Activer IA1
- [ ] Vérifier que les réglages sont présents
- [ ] Tester le chat sur le site
- [ ] (Optionnel) Remplacer `[lydia_chat]` par `[ia1_chat]`

## 🐛 Bugs connus

Aucun bug connu à ce jour. Si vous rencontrez un problème, merci de l'indiquer sur [GitHub Issues](https://github.com/Jean-Christophe-Gilbert/ia1-plugin/issues).

## 📝 Fichiers de la release

Cette release contient :

- `ia1-ai-plugin.php` - Le plugin WordPress
- `README.md` - Documentation complète
- `CHANGELOG.md` - Historique des versions
- `MIGRATION.md` - Guide de migration depuis Lydia
- `RELEASE_NOTES.md` - Ce fichier

## 🔮 Prochaines versions

### v3.1.0 (prévu février 2026)
- Amélioration de l'interface utilisateur
- Support des images dans les réponses
- Optimisation des performances

### v3.2.0 (prévu mars 2026)
- Mode multi-langues
- Personnalisation avancée du design
- Analytics des questions posées

## 🙏 Remerciements

Merci à tous nos utilisateurs qui nous ont fait confiance avec Lydia et qui continuent l'aventure avec IA1 !

Un grand merci également à :
- L'équipe Mistral AI pour leur excellente API
- La communauté WordPress
- Tous nos contributeurs

## 📞 Support

- **Email** : jc@ia1.fr
- **Téléphone** : 06 40 75 53 92
- **GitHub** : [ia1-plugin](https://github.com/Jean-Christophe-Gilbert/ia1-plugin)
- **Site web** : [ia1.fr](https://ia1.fr)

---

**Développé par IA1** • **Propulsé par Mistral AI** • **Open Source & Souverain**

Made with ❤️ in Niort, France 🇫🇷

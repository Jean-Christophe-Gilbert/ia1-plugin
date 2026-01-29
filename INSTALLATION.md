# 🚀 Guide d'installation - IA1 Plugin v3.1.0

## ✅ Ce qui a été fait

J'ai créé une **structure complète et modulaire** pour votre plugin IA1 avec :

### 📁 Fichiers créés (14 fichiers)

#### Fichier principal
- `ia1-plugin.php` (renommé depuis ia1-ai-plugin.php ✅)

#### Includes (classes principales)
- `includes/class-ia1-settings.php` - Gestion des paramètres
- `includes/class-ia1-indexer.php` - Indexation et recherche
- `includes/class-ia1-mistral.php` - API Mistral AI

#### Admin (interface d'administration)
- `admin/class-ia1-admin.php` - Contrôleur admin
- `admin/views/admin-page.php` - Interface de personnalisation
- `admin/views/indexation-page.php` - Page d'indexation
- `admin/css/ia1-admin.css` - Styles admin
- `admin/js/ia1-admin.js` - JavaScript avec prévisualisation temps réel

#### Public (frontend)
- `public/class-ia1-public.php` - Contrôleur public
- `public/views/chat-widget.php` - Template du widget
- `public/css/ia1-chat.css` - Styles du chat
- `public/js/ia1-chat.js` - JavaScript du chat

## 🎯 Nouvelles fonctionnalités

### ✨ Personnalisation complète

Chaque client peut maintenant personnaliser :
- ✅ Nom de l'assistant (au lieu de "Lydia" par défaut)
- ✅ Sous-titre / description
- ✅ Message d'accueil
- ✅ Couleur principale
- ✅ Initiales de l'avatar

### 🎨 Prévisualisation en temps réel

L'interface admin affiche un aperçu du widget qui se met à jour instantanément pendant la saisie.

### 📊 Page d'indexation

Nouvelle page dédiée avec statistiques et réindexation manuelle.

## 📥 Installation sur votre serveur

### Étape 1 : Remplacer les fichiers

1. **Faire une sauvegarde de l'ancien plugin**
   ```bash
   cd wp-content/plugins/
   mv ia1-plugin ia1-plugin-backup
   ```

2. **Uploader la nouvelle version**
   - Téléchargez le dossier `ia1-plugin` complet
   - Uploadez-le via FTP dans `wp-content/plugins/`
   - Ou utilisez Git :
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/Jean-Christophe-Gilbert/ia1-plugin.git
   ```

### Étape 2 : Activer (ou réactiver)

1. Aller dans WordPress Admin → Extensions
2. Si le plugin était déjà activé, il sera automatiquement mis à jour
3. Sinon, cliquer sur "Activer"

### Étape 3 : Vérifier la migration

Si vous aviez l'ancienne version "Lydia" :
- Les paramètres seront automatiquement migrés
- Le nom par défaut sera "Lydia" (personnalisable)
- Un message de confirmation apparaîtra dans l'admin

### Étape 4 : Tester la personnalisation

1. Aller dans **IA1 → Configuration**
2. Section "🎨 Personnalisation de votre assistant"
3. Modifier le nom, les couleurs, etc.
4. Voir la prévisualisation se mettre à jour en temps réel
5. Cliquer sur "Enregistrer les modifications"

### Étape 5 : Réindexer (recommandé)

1. Aller dans **IA1 → Indexation**
2. Cliquer sur "🚀 Réindexer tout le contenu"
3. Attendre la fin de l'indexation

## 🔍 Vérification

### Page admin accessible ?
✅ http://votresite.com/wp-admin/admin.php?page=ia1

### Widget fonctionne ?
1. Créer une page de test
2. Ajouter le shortcode : `[ia1_chat]`
3. Visiter la page
4. Le widget doit s'afficher avec votre personnalisation

### Prévisualisation fonctionne ?
1. Aller dans IA1 → Configuration
2. Modifier le nom de l'assistant
3. La prévisualisation doit se mettre à jour instantanément

## 🐛 Résolution de problèmes

### "Classe IA1_Settings not found"
→ Vérifier que tous les fichiers sont bien uploadés dans la bonne structure

### "Permission denied"
→ Vérifier les permissions des fichiers :
```bash
chmod 644 ia1-plugin.php
chmod -R 755 includes/ admin/ public/
```

### Prévisualisation ne se met pas à jour
→ Vider le cache du navigateur (Ctrl+Shift+R)

### Widget ne s'affiche pas
→ Vérifier dans l'inspecteur (F12) s'il y a des erreurs JavaScript

## 📝 Prochaines étapes

1. **Tester l'interface de personnalisation**
   - Changer tous les paramètres
   - Vérifier que la prévisualisation fonctionne
   - Sauvegarder et vérifier sur le frontend

2. **Créer une page d'aide sur ia1.fr**
   - Avec captures d'écran de l'interface
   - Guide de personnalisation pour les clients

3. **Mettre à jour sur GitHub**
   ```bash
   git add .
   git commit -m "v3.1.0 - Personnalisation complète + nouvelle structure"
   git push origin main
   git tag v3.1.0
   git push origin v3.1.0
   ```

4. **Annoncer la nouvelle version**
   - Sur le site ia1.fr
   - Par email aux clients existants
   - Mettre en avant la personnalisation

## 📞 Besoin d'aide ?

Si vous rencontrez un problème :
1. Vérifier les logs WordPress : `wp-content/debug.log`
2. Activer le mode debug :
   ```php
   // Dans wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
3. Me contacter : jc@ia1.fr

---

**Félicitations ! Votre plugin IA1 est maintenant prêt avec la personnalisation complète ! 🎉**

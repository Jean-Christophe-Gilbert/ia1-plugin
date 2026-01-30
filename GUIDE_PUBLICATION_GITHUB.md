# 📦 Guide de publication GitHub - IA1 v3.1.10

Instructions complètes pour publier la release v3.1.10 sur GitHub

---

## 📋 Checklist pré-publication

Avant de publier, vérifiez que vous avez :

- [x] ✅ Testé la version sur un site de prod (Celtic Social Club ✓)
- [x] ✅ Tous les fichiers modifiés
- [x] ✅ Documentation à jour
- [x] ✅ CHANGELOG.md complété
- [ ] ⏳ Tag Git créé
- [ ] ⏳ Release GitHub publiée
- [ ] ⏳ ZIP généré pour téléchargement

---

## 📁 Fichiers à inclure dans le repository

### Fichiers modifiés pour v3.1.10

```
ia1-plugin/
├── ia-plugin.php                    ← MODIFIÉ (v3.1.10)
├── includes/
│   ├── class-ia1-indexer.php       ← MODIFIÉ (scoring intelligent)
│   ├── class-ia1-mistral.php       ← MODIFIÉ (détection intention)
│   ├── class-ia1-settings.php      ← INCHANGÉ
│   └── class-ia1-core.php          ← INCHANGÉ
├── public/
│   ├── js/
│   │   └── ia1-chat.js             ← MODIFIÉ (sources liste à puces)
│   ├── css/
│   │   └── ia1-chat.css            ← MODIFIÉ (styles améliorés)
│   └── views/
│       └── chat-widget.php         ← INCHANGÉ
├── admin/
│   └── class-ia1-admin.php         ← INCHANGÉ
├── CHANGELOG.md                     ← MODIFIÉ (v3.1.10 ajoutée)
├── README.md                        ← À METTRE À JOUR
├── RELEASE_NOTES.md                 ← NOUVEAU (v3.1.10)
├── MIGRATION_v3.1.9_to_v3.1.10.md  ← NOUVEAU
└── README_ALGORITHME_v3.1.10.md    ← NOUVEAU (doc technique)
```

---

## 🔧 Étapes de publication

### Étape 1 : Préparer le repository local

```bash
# Aller dans votre dossier plugin local
cd /chemin/vers/ia1-plugin

# Vérifier le statut
git status

# Ajouter tous les fichiers modifiés
git add ia-plugin.php
git add includes/class-ia1-indexer.php
git add includes/class-ia1-mistral.php
git add public/js/ia1-chat.js
git add public/css/ia1-chat.css
git add CHANGELOG.md
git add RELEASE_NOTES.md
git add MIGRATION_v3.1.9_to_v3.1.10.md
git add README_ALGORITHME_v3.1.10.md

# Commit
git commit -m "Release v3.1.10 - Algorithme intelligent et UI améliorée

- Algorithme de scoring intelligent avec détection pages hub
- Détection d'intention utilisateur (achat, navigation, info)
- Détection automatique catégories de pages
- Affichage sources en liste à puces élégante
- Prompt système amélioré pour réponses naturelles
- Température optimisée (0.5)
- Documentation complète mise à jour"
```

### Étape 2 : Créer le tag Git

```bash
# Créer le tag v3.1.10
git tag -a v3.1.10 -m "Version 3.1.10 - Algorithme intelligent

Améliorations majeures :
- Algorithme de recherche intelligent
- Détection d'intention utilisateur
- Interface utilisateur améliorée
- Scoring avec 6 critères pondérés
- Réponses plus naturelles et pertinentes"

# Vérifier que le tag existe
git tag -l
```

### Étape 3 : Pusher vers GitHub

```bash
# Pusher les commits
git push origin main

# Pusher le tag
git push origin v3.1.10
```

### Étape 4 : Créer la release sur GitHub

1. **Aller sur GitHub** : https://github.com/Jean-Christophe-Gilbert/ia1-plugin

2. **Cliquer sur "Releases"** (dans la barre latérale droite)

3. **Cliquer sur "Draft a new release"**

4. **Remplir le formulaire** :

   **Tag version** : `v3.1.10` (sélectionner le tag créé)
   
   **Release title** : `🎉 IA1 v3.1.10 - Algorithme intelligent`
   
   **Description** : Coller le contenu de `RELEASE_NOTES.md`
   
   **Fichiers attachés** : 
   - Cocher "Set as the latest release"
   - Laisser GitHub générer automatiquement les archives Source code (zip) et Source code (tar.gz)

5. **Options** :
   - ☑️ Set as the latest release
   - ☐ Set as a pre-release (ne PAS cocher)

6. **Cliquer sur "Publish release"**

---

## 📦 Créer le ZIP pour distribution WordPress

### Option 1 : Via GitHub (recommandé)

GitHub génère automatiquement `ia1-plugin-3.1.10.zip` lors de la création de la release.

### Option 2 : Manuellement

```bash
# Depuis la racine du projet
cd ..

# Créer une archive ZIP propre (sans .git, node_modules, etc.)
zip -r ia1-plugin-v3.1.10.zip ia1-plugin/ \
  -x "*.git*" \
  -x "*node_modules*" \
  -x "*.DS_Store" \
  -x "*__MACOSX*" \
  -x "*.backup*"

# Vérifier le contenu
unzip -l ia1-plugin-v3.1.10.zip
```

---

## 📝 Mettre à jour le README principal

Modifiez `README.md` pour refléter la nouvelle version :

```markdown
# IA1 - Intelligence Artificielle Locale pour WordPress

**Version actuelle** : 3.1.10
**Dernière mise à jour** : 30 janvier 2025

[Badge] ![Version](https://img.shields.io/badge/version-3.1.10-blue)
[Badge] ![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue)
[Badge] ![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)

## 🎉 Nouveautés v3.1.10

- 🧠 Algorithme de recherche intelligent
- 🎯 Détection d'intention utilisateur
- 💬 Réponses plus naturelles
- 📋 Sources en liste à puces élégante

[Voir les notes de version complètes](RELEASE_NOTES.md)

[... reste du README ...]
```

---

## 🌐 Communication de la release

### 1. Twitter / X (optionnel)
```
🎉 IA1 v3.1.10 est disponible !

✨ Algorithme intelligent qui comprend vos intentions
🎯 Dirige automatiquement vers les bonnes pages
💬 Réponses plus naturelles et conversationnelles

Open Source | WordPress | Mistral AI

👉 https://github.com/Jean-Christophe-Gilbert/ia1-plugin

#WordPress #AI #OpenSource #MistralAI
```

### 2. Site ia1.fr
Publier un article de blog annonçant la v3.1.10 avec :
- Capture d'écran du nouveau design
- Vidéo démo (optionnel)
- Lien vers GitHub
- Instructions d'installation

### 3. WordPress.org (futur)
Si vous décidez de publier sur le répertoire officiel WordPress.

---

## ✅ Checklist post-publication

Après publication, vérifiez :

- [ ] Release visible sur GitHub
- [ ] Tag v3.1.10 présent
- [ ] ZIP téléchargeable
- [ ] CHANGELOG.md à jour sur GitHub
- [ ] README.md reflète la bonne version
- [ ] Liens dans RELEASE_NOTES fonctionnels
- [ ] Page ia1.fr mise à jour
- [ ] Email aux utilisateurs (optionnel)

---

## 📊 Statistiques à suivre

Après publication, surveillez :

- **GitHub** :
  - ⭐ Nombre d'étoiles
  - 👁️ Nombre de vues
  - ⬇️ Nombre de téléchargements
  - 🐛 Issues ouvertes

- **Site ia1.fr** :
  - 📈 Trafic
  - ⬇️ Téléchargements
  - 📧 Demandes de support

---

## 🆘 En cas de problème

### Problème 1 : Le tag ne s'affiche pas sur GitHub

```bash
# Vérifier les tags locaux
git tag -l

# Repousser le tag
git push origin v3.1.10 --force
```

### Problème 2 : Besoin de modifier la release après publication

Sur GitHub :
1. Releases → v3.1.10
2. Cliquer sur l'icône crayon (Edit)
3. Modifier le contenu
4. Sauvegarder

### Problème 3 : Bug critique découvert après publication

1. **Ne PAS supprimer** la release
2. Publier immédiatement une v3.1.11 avec le fix
3. Ajouter un avertissement dans les notes de v3.1.10
4. Déplacer "latest release" vers v3.1.11

---

## 📅 Calendrier de release

### Versions mineures (3.1.x) - Patches
- Fréquence : Au besoin (bugs critiques)
- Délai minimal : 1 semaine entre releases

### Versions moyennes (3.x.0) - Features
- Fréquence : 1-2 mois
- Annonce préalable : 1 semaine

### Versions majeures (4.0.0) - Breaking changes
- Fréquence : 6-12 mois
- Annonce préalable : 1 mois
- Période beta : 2 semaines

---

## 🎯 Prochaines versions planifiées

### v3.1.11 (hotfix si nécessaire)
- Corrections de bugs critiques uniquement

### v3.2.0 (prévu Mars 2025)
- Multilingue amélioré
- Statistiques d'utilisation
- Export conversations

### v3.3.0 (prévu Mai 2025)
- Intégration analytics
- Mode debug avancé
- Suggestions automatiques

---

## 📞 Contacts

- **Développeur** : Jean-Christophe Gilbert
- **Email** : jc@ia1.fr
- **Site** : ia1.fr
- **GitHub** : Jean-Christophe-Gilbert

---

**Bon courage pour la publication ! 🚀**

*N'oubliez pas de célébrer après la publication ! 🎉*

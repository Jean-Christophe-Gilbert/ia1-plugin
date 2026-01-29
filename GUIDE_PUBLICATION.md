# 📖 Guide Pas à Pas : Publication IA1 v3.0.0

## 🎯 Ce guide vous accompagne pour publier la nouvelle version

**Durée estimée** : 15-20 minutes  
**Niveau** : Facile - Toutes les étapes sont détaillées

---

## 📋 Avant de commencer

### Ce dont vous avez besoin :

- [ ] Un compte GitHub
- [ ] Accès au dépôt `lydia-ia-plugin`
- [ ] Le fichier ZIP `ia1-plugin-v3.0.0.zip`
- [ ] Les fichiers téléchargés (ia1-ai-plugin.php, README.md, etc.)

### Ce que vous allez faire :

1. Renommer le dépôt GitHub
2. Remplacer les fichiers
3. Créer la release v3.0.0
4. Communiquer aux utilisateurs

**⏱️ Temps total estimé : 15 minutes**

---

## 🚀 ÉTAPE 1 : Préparer votre ordinateur (2 min)

### 1.1 Créer un dossier de travail

Sur votre ordinateur, créez un dossier :
```
Bureau/
└── ia1-release/
    ├── ia1-plugin-v3.0.0.zip       ← Le ZIP que je vous ai fourni
    ├── ia1-ai-plugin.php            ← Les fichiers individuels
    ├── README.md
    ├── CHANGELOG.md
    ├── MIGRATION.md
    └── RELEASE_NOTES.md
```

### 1.2 Vérifier que vous avez tout

✅ Le ZIP : `ia1-plugin-v3.0.0.zip` (environ 16 KB)  
✅ Les 5 fichiers individuels

---

## 🏷️ ÉTAPE 2 : Renommer le dépôt GitHub (1 min)

### 2.1 Aller sur GitHub

1. Ouvrez votre navigateur
2. Allez sur : `https://github.com/Jean-Christophe-Gilbert/lydia-ia-plugin`
3. Connectez-vous si nécessaire

### 2.2 Renommer le dépôt

1. Cliquez sur **"Settings"** (⚙️ en haut à droite)
2. Dans la section **"General"**, tout en haut
3. Trouvez **"Repository name"**
4. Remplacez `lydia-ia-plugin` par `ia1-plugin`
5. Cliquez sur **"Rename"**

⚠️ **Important** : GitHub va vous demander de confirmer. C'est normal, cliquez sur "I understand, rename this repository"

✅ **Résultat** : Votre dépôt s'appelle maintenant `ia1-plugin`

---

## 📁 ÉTAPE 3 : Remplacer les fichiers dans GitHub (5 min)

### Option A : Via l'interface Web GitHub (Facile)

#### 3.1 Supprimer l'ancien fichier principal

1. Sur la page d'accueil de votre dépôt
2. Trouvez le fichier `lydia-ai-plugin.php`
3. Cliquez dessus
4. Cliquez sur l'icône **poubelle** (🗑️) en haut à droite
5. En bas de page : "Commit changes"
6. Message : `Suppression ancien fichier lydia`
7. Cliquez sur **"Commit changes"**

#### 3.2 Ajouter les nouveaux fichiers

Pour **CHAQUE fichier** (ia1-ai-plugin.php, README.md, CHANGELOG.md, MIGRATION.md) :

1. Retournez à la page d'accueil du dépôt
2. Cliquez sur **"Add file"** → **"Upload files"**
3. Glissez-déposez **UN** fichier
4. Message de commit : `Ajout [nom du fichier]`
5. Cliquez sur **"Commit changes"**
6. **Répétez** pour chaque fichier

✅ **Résultat** : Votre dépôt contient maintenant tous les nouveaux fichiers

### Option B : Via Git en ligne de commande (Si vous êtes à l'aise)

```bash
# Clone le dépôt (avec le nouveau nom)
git clone https://github.com/Jean-Christophe-Gilbert/ia1-plugin.git
cd ia1-plugin

# Copie les nouveaux fichiers
cp ~/Bureau/ia1-release/*.php .
cp ~/Bureau/ia1-release/*.md .

# Supprime l'ancien fichier
rm lydia-ai-plugin.php

# Commit et push
git add .
git commit -m "v3.0.0 - Renommage Lydia → IA1"
git push
```

---

## 🎉 ÉTAPE 4 : Créer la Release v3.0.0 (5 min)

### 4.1 Aller dans la section Releases

1. Sur la page d'accueil de votre dépôt `ia1-plugin`
2. À droite, trouvez la section **"Releases"** (ou allez à `/releases`)
3. Cliquez sur **"Create a new release"** ou **"Draft a new release"**

### 4.2 Remplir les informations de la release

**Tag version** :
- Tapez : `v3.0.0`
- Sélectionnez : "Create new tag: v3.0.0 on publish"

**Release title** :
```
🎉 IA1 v3.0.0 - Renommage du plugin
```

**Description** :
Ouvrez le fichier `RELEASE_NOTES.md` et **copiez-collez tout son contenu** ici.

### 4.3 Attacher le ZIP

1. Descendez jusqu'à la section **"Attach binaries"**
2. Cliquez sur la zone ou glissez-déposez `ia1-plugin-v3.0.0.zip`
3. Attendez que le téléchargement soit terminé (barre de progression verte)

### 4.4 Publier

1. ☑️ Cochez **"Set as the latest release"**
2. Cliquez sur **"Publish release"**

✅ **Résultat** : La release v3.0.0 est publiée !

**URL de votre release** : 
`https://github.com/Jean-Christophe-Gilbert/ia1-plugin/releases/tag/v3.0.0`

---

## 📢 ÉTAPE 5 : Communiquer aux utilisateurs (5 min)

### 5.1 Créer une annonce

**Titre** : Le plugin Lydia devient IA1 🎉

**Message suggéré** :

```
Bonjour à tous,

Le plugin Lydia change de nom et devient IA1 (Intelligence Artificielle 1) !

🔄 Pourquoi ce changement ?
Le nom "Lydia" était déjà utilisé par d'autres services. Pour éviter toute confusion, 
nous avons choisi de renommer le plugin.

✨ Qu'est-ce qui change ?
- Le nom du plugin : Lydia → IA1
- L'interface affiche maintenant "IA1"
- Le fichier principal : ia1-ai-plugin.php

✅ Qu'est-ce qui ne change PAS ?
- Toutes les fonctionnalités restent identiques
- Vos réglages sont automatiquement préservés
- Votre clé API et votre index sont conservés
- Migration 100% automatique !

📦 Comment mettre à jour ?
1. Téléchargez la v3.0.0 : 
   https://github.com/Jean-Christophe-Gilbert/ia1-plugin/releases/tag/v3.0.0

2. Désactivez l'ancien plugin "Lydia"
3. Installez et activez "IA1"
4. C'est tout ! ✨

📖 Guide de migration complet :
https://github.com/Jean-Christophe-Gilbert/ia1-plugin/blob/main/MIGRATION.md

Des questions ? Contactez-moi :
📧 jc@ia1.fr
📞 06 40 75 53 92

Merci de votre confiance ! 🙏

Jean-Christophe
```

### 5.2 Où publier cette annonce ?

- [ ] **Email** aux utilisateurs connus
- [ ] **Site web** ia1.fr (si applicable)
- [ ] **Forum** WordPress.org (si vous y êtes)
- [ ] **Réseaux sociaux** (LinkedIn, Twitter, etc.)

---

## 🎯 ÉTAPE 6 : Mettre à jour le README de ia1.fr (2 min)

Si vous avez un site ia1.fr, mettez à jour :

- [ ] Les liens de téléchargement → pointer vers `ia1-plugin`
- [ ] Les noms : "Lydia" → "IA1"
- [ ] Les captures d'écran (si nécessaire)

---

## ✅ Checklist finale

Vérifiez que vous avez bien fait tout ça :

- [ ] Le dépôt GitHub s'appelle `ia1-plugin`
- [ ] Les nouveaux fichiers sont dans le dépôt
- [ ] L'ancien fichier `lydia-ai-plugin.php` est supprimé
- [ ] La release v3.0.0 est publiée
- [ ] Le ZIP est attaché à la release
- [ ] L'annonce est publiée aux utilisateurs

---

## 🆘 En cas de problème

### Le renommage GitHub ne marche pas
→ Vérifiez que vous êtes bien propriétaire du dépôt

### Les fichiers ne s'uploadent pas
→ Vérifiez la taille (doivent être < 100 MB)
→ Essayez un par un au lieu de tous en même temps

### La release ne se crée pas
→ Vérifiez que le tag `v3.0.0` n'existe pas déjà
→ Supprimez-le si nécessaire et recréez-le

### Je ne sais plus où j'en suis
→ Contactez-moi : jc@ia1.fr ou 06 40 75 53 92

---

## 🎉 Félicitations !

Vous avez publié IA1 v3.0.0 ! 🚀

**Ce qui va se passer maintenant :**

1. Les utilisateurs vont télécharger la nouvelle version
2. Ils vont migrer automatiquement
3. Vous allez peut-être recevoir quelques questions
4. Tout va bien se passer ! 😊

---

## 📞 Support

Si vous avez des questions pendant le processus :

- **Email** : jc@ia1.fr
- **Téléphone** : 06 40 75 53 92
- **GitHub Issues** : https://github.com/Jean-Christophe-Gilbert/ia1-plugin/issues

---

**Bon courage ! Vous avez tout ce qu'il faut pour réussir.** 💪

_Guide créé le 26 janvier 2026_

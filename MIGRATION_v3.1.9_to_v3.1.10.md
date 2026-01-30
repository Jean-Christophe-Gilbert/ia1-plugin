# Migration v3.1.9 → v3.1.10

Guide de mise à jour depuis IA1 v3.1.9 vers v3.1.10

---

## ⚡ Mise à jour rapide (5 minutes)

### Étape 1 : Sauvegarde
```bash
# Sauvegarder votre dossier plugin actuel
cp -r wp-content/plugins/ia1-plugin wp-content/plugins/ia1-plugin.backup
```

### Étape 2 : Remplacer les fichiers

Remplacez ces fichiers dans `/wp-content/plugins/ia1-plugin/` :

```
ia1-plugin/
├── ia-plugin.php                    ✓ REMPLACER
├── includes/
│   ├── class-ia1-indexer.php       ✓ REMPLACER
│   └── class-ia1-mistral.php       ✓ REMPLACER
└── public/
    ├── js/ia1-chat.js              ✓ REMPLACER
    └── css/ia1-chat.css            ✓ REMPLACER
```

### Étape 3 : Réindexer

1. Allez dans **WordPress Admin → IA1 → Indexation**
2. Cliquez sur **"Réindexer tout le contenu"**
3. Attendez la fin (peut prendre quelques minutes)

### Étape 4 : Vider le cache

Si vous utilisez un plugin de cache :
- WP Fastest Cache : Vider tout le cache
- W3 Total Cache : Vider tous les caches
- WP Super Cache : Supprimer le cache

### Étape 5 : Tester

Testez avec une question comme :
- "Où est votre boutique ?"
- "Je veux acheter un produit"
- "Comment vous contacter ?"

✅ **C'est terminé !**

---

## 🔍 Changements détaillés

### Modifications du fichier `ia-plugin.php`

#### Ancienne version (3.1.9)
```php
'ia1_temperature' => 0.7,
'ia1_system_prompt' => 'Tu es un assistant conversationnel...',
```

#### Nouvelle version (3.1.10)
```php
'ia1_temperature' => 0.5,  // ← Changé
'ia1_system_prompt' => 'Tu es IA1, l\'assistant conversationnel...  // ← Prompt enrichi
```

**Actions** :
- ✅ Automatique lors de l'activation
- Si vous aviez personnalisé ces valeurs, elles sont préservées
- Pour utiliser les nouveaux paramètres : Réglages → IA1 → "Réinitialiser la personnalisation"

---

### Modifications de `class-ia1-indexer.php`

#### Nouvelles méthodes ajoutées :
- `detect_content_category()` - Détecte le type de page
- Scoring SQL amélioré avec 6 boosts

#### Exemple de ce qui change :

**Avant v3.1.9** :
```php
// Scoring simple
relevance_score = 
    (titre match ? 100 : 0) +
    (occurrences titre * 10) +
    (occurrences contenu * 1)
```

**Après v3.1.10** :
```php
// Scoring intelligent
relevance_score = 
    (titre match ? 200 : 0) +
    (occurrences titre * 15) +
    (occurrences contenu * 2) +
    (page hub ? +50 : 0) +         // ← NOUVEAU
    (page type ? +40 : 0) +        // ← NOUVEAU
    (titre court pertinent ? +25 : 0)  // ← NOUVEAU
```

**Impact** : Les pages principales sont automatiquement priorisées.

---

### Modifications de `class-ia1-mistral.php`

#### Nouvelles méthodes ajoutées :
- `detect_query_intent()` - Détecte l'intention de l'utilisateur
- `build_intent_instructions()` - Instructions spécifiques selon l'intention
- `build_smart_user_prompt()` - Prompt enrichi avec métadonnées

#### Ce qui change dans les prompts :

**Avant v3.1.9** :
```
Voici le contenu disponible :
[Contenu brut]

Question : [Question utilisateur]
Réponds en te basant UNIQUEMENT sur le contenu.
```

**Après v3.1.10** :
```
Voici le contenu disponible :
[Contenu avec métadonnées : [PAGE PRINCIPALE], [Catégorie: boutique]]

INSTRUCTIONS IMPORTANTES :
- L'utilisateur cherche à ACHETER (intention détectée)
- Privilégie les pages boutique/shop
- Oriente vers les pages principales

Question : [Question utilisateur]
```

**Impact** : L'IA comprend mieux le contexte et l'intention.

---

### Modifications de `ia1-chat.js`

#### Sources : avant vs après

**Avant v3.1.9** :
```javascript
// Liste simple
sourcesHtml += '<div>' + source.title + '</div>';
```

**Après v3.1.10** :
```javascript
// Liste à puces avec icônes
sourcesHtml = '<ul>' + 
  '<li><a>' + source.title + ' <icon></icon></a></li>' +
'</ul>';
```

**Impact** : Affichage élégant avec puces et icônes.

---

### Modifications de `ia1-chat.css`

#### Nouveaux styles ajoutés :

```css
/* Liste à puces pour les sources */
.ia1-message-sources-list {
    list-style: none;
    padding: 0;
}

.ia1-message-sources-list li::before {
    content: '•';
    color: #2271b1;
    font-weight: bold;
}

/* Liens avec icônes */
.ia1-message-source-link svg {
    opacity: 0.6;
    transition: opacity 0.2s;
}
```

**Impact** : Sources joliment présentées.

---

## 🗄️ Base de données

### Aucune modification de la structure

✅ La table `wp_ia1_index` reste inchangée
✅ Aucune migration SQL nécessaire
✅ Les données existantes sont conservées

**MAIS** : Il est recommandé de **réindexer** pour profiter de la nouvelle détection de catégorie.

---

## ⚙️ Options WordPress

### Options modifiées automatiquement :

```php
// Lors de l'activation du plugin mis à jour
update_option( 'ia1_version', '3.1.10' );
update_option( 'ia1_temperature', 0.5 );  // Si valeur par défaut
update_option( 'ia1_system_prompt', '[nouveau prompt]' );  // Si valeur par défaut
```

### Options préservées :

✅ Votre clé API Mistral
✅ Vos personnalisations (nom, couleurs, messages)
✅ Vos paramètres modifiés manuellement

---

## 🔄 Compatibilité

### Rétrocompatibilité

✅ **Totalement compatible** avec v3.1.9
✅ Aucune rupture d'API
✅ Shortcodes `[ia1_chat]` et `[lydia_chat]` fonctionnent
✅ Pas de changement dans l'admin

### Si vous aviez des personnalisations :

#### Personnalisation du CSS
Vos styles personnalisés sont préservés.
Ajoutez simplement les nouveaux styles pour les sources si vous voulez le nouveau design.

#### Personnalisation du prompt système
Si vous aviez modifié le prompt, il reste inchangé.
Pour utiliser le nouveau prompt amélioré :
1. Admin → IA1 → Configuration
2. Copiez le nouveau prompt depuis `RELEASE_NOTES.md`
3. Ou cliquez sur "Réinitialiser"

#### Post types personnalisés
Aucun changement nécessaire, tout fonctionne automatiquement.

---

## 🐛 Résolution de problèmes

### Problème 1 : Les réponses n'ont pas changé

**Solution** :
1. Vérifiez que les fichiers sont bien remplacés
2. Réindexez le contenu (crucial !)
3. Videz le cache
4. Testez en navigation privée

### Problème 2 : Erreur 500 après mise à jour

**Solution** :
1. Vérifiez les permissions des fichiers (644 pour fichiers, 755 pour dossiers)
2. Vérifiez les logs PHP : `/var/log/php-fpm/error.log`
3. Restaurez la sauvegarde si nécessaire
4. Contactez le support : jc@ia1.fr

### Problème 3 : Les sources ne s'affichent pas en liste à puces

**Solution** :
1. Vérifiez que `ia1-chat.js` et `ia1-chat.css` sont bien remplacés
2. Videz le cache du navigateur (Ctrl+F5)
3. Vérifiez qu'il n'y a pas de conflit CSS

### Problème 4 : Le nouveau prompt n'est pas appliqué

**Solution** :
1. Admin → IA1 → Configuration
2. Regardez le champ "Instructions système"
3. Si c'est l'ancien, remplacez par le nouveau manuellement

---

## ✅ Checklist complète

Avant de dire que c'est terminé, vérifiez :

- [ ] Fichiers remplacés (5 fichiers)
- [ ] Contenu réindexé
- [ ] Cache vidé
- [ ] Test avec question "boutique" → trouve la boutique
- [ ] Test avec question "contact" → trouve le contact
- [ ] Sources affichées en liste à puces
- [ ] Réponses plus naturelles
- [ ] Pas d'erreurs dans la console navigateur
- [ ] Pas d'erreurs dans les logs PHP

---

## 🆘 Besoin d'aide ?

Si vous rencontrez un problème :

1. **Documentation** : [ia1.fr](https://ia1.fr)
2. **Issues GitHub** : [github.com/Jean-Christophe-Gilbert/ia1-plugin/issues](https://github.com/Jean-Christophe-Gilbert/ia1-plugin/issues)
3. **Email** : jc@ia1.fr
4. **Téléphone** : 06 40 75 53 92

---

## 📊 Temps estimés

- 🚀 **Mise à jour express** : 5 minutes
- 📚 **Avec lecture de la doc** : 15 minutes
- 🧪 **Avec tests complets** : 30 minutes
- 🔧 **Avec personnalisations** : 45 minutes

---

**Bon courage pour la mise à jour ! 🚀**

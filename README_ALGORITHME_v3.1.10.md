# IA1 v3.1.10 - Améliorations de l'algorithme de recherche

## 🎯 Objectif

Rendre IA1 **plus intelligent** pour TOUS les sites WordPress, pas juste pour un cas spécifique.
L'IA doit automatiquement diriger les utilisateurs vers les **pages principales** (boutique, catégories, hub) plutôt que vers des pages individuelles isolées.

---

## 📊 Problème identifié

### Avant (v3.1.9)
**Question** : "Je cherche à acheter un t-shirt"
**Résultat** : Renvoie vers des articles de blog mentionnant "t-shirt"
**Problème** : Ne trouve pas la page boutique principale

### Pourquoi ?
1. **Scoring trop simple** : compte juste les occurrences de mots
2. **Pas de hiérarchie** : page produit = page catégorie = page blog
3. **Pas de détection d'intention** : ne comprend pas que "acheter" = boutique

---

## ✨ Solutions implémentées

### 1. **Algorithme de scoring amélioré** (`class-ia1-indexer.php`)

Le nouveau système de scoring prend en compte :

#### BOOST 1 : Correspondance titre (200 points)
- Si le titre correspond exactement à la recherche

#### BOOST 2 : Occurrences dans titre (15 pts/occurrence)
- Chaque fois que le mot apparaît dans le titre

#### BOOST 3 : Occurrences dans contenu (2 pts/occurrence)
- Chaque fois que le mot apparaît dans le contenu

#### BOOST 4 : **Pages HUB** (15-50 points) **← NOUVEAU**
- Page avec >2000 caractères = +50 pts (page très complète)
- Page avec >1000 caractères = +30 pts (page importante)
- Page avec >500 caractères = +15 pts (page moyenne)
- **Logique** : Les pages principales ont généralement plus de contenu

#### BOOST 5 : **Hiérarchie de post types** (10-40 points) **← NOUVEAU**
- `page` WordPress = +40 pts (souvent des pages principales)
- `product` WooCommerce = +30 pts (produits)
- `post` WordPress = +20 pts (articles de blog)
- Autres = +10 pts
- **Logique** : Les pages WordPress sont souvent plus importantes que les posts

#### BOOST 6 : Titre court et pertinent (+25 points) **← NOUVEAU**
- Titre court (<50 caractères) + mot-clé = spécifique et pertinent

---

### 2. **Détection de catégorie** (`detect_content_category()`) **← NOUVEAU**

L'algorithme détecte automatiquement le type de page :
- **Boutique** : mots-clés "boutique", "shop", "acheter", "commander"
- **Contact** : mots-clés "contact", "nous contacter"
- **À propos** : mots-clés "à propos", "qui sommes-nous"
- **FAQ** : mots-clés "faq", "questions", "aide"
- **Blog** : mots-clés "blog", "actualités", "news"

Cette information aide l'IA à mieux comprendre le contexte.

---

### 3. **Détection d'intention** (`class-ia1-mistral.php`) **← NOUVEAU**

L'IA analyse maintenant l'intention de l'utilisateur :

#### Intention ACHAT (`purchase`)
- Mots déclencheurs : "acheter", "commander", "prix", "boutique"
- **Action** : Privilégie les pages boutique/catégories
- **Instruction à l'IA** : "Oriente l'utilisateur vers les pages principales"

#### Intention CONTACT (`contact`)
- Mots déclencheurs : "contact", "appeler", "email"
- **Action** : Cherche les infos de contact

#### Intention NAVIGATION (`navigation`)
- Mots déclencheurs : "où", "trouver", "page"
- **Action** : Dirige vers les pages principales

#### Intention INFORMATION (`information`)
- Mots déclencheurs : "qu'est-ce", "comment", "pourquoi"
- **Action** : Répond avec le contenu disponible

---

### 4. **Prompt intelligent** **← NOUVEAU**

Le prompt envoyé à Mistral AI inclut maintenant :
- Les **métadonnées** des pages (`[PAGE PRINCIPALE]`, `[Catégorie: boutique]`)
- Des **instructions spécifiques** selon l'intention détectée
- Des **règles de priorisation** pour favoriser les pages hub

**Exemple de prompt amélioré** :
```
INSTRUCTIONS IMPORTANTES :
- L'utilisateur cherche à ACHETER ou consulter des PRODUITS
- Privilégie les pages boutique/shop dans ta réponse
- Les pages marquées [PAGE PRINCIPALE] sont probablement des pages de catégorie
- ORIENTE L'UTILISATEUR vers ces pages principales plutôt que vers des produits individuels

RÈGLES DE RÉPONSE :
1. Si plusieurs pages pertinentes, privilégie les [PAGE PRINCIPALE]
2. Si info pas exacte, suggère la page la plus pertinente
3. Reste naturel et conversationnel
```

---

## 🎯 Résultats attendus

### Scénario 1 : Recherche de produits
**Question** : "Je cherche à acheter un t-shirt"
**Avant** : Articles de blog mentionnant t-shirts
**Après** : ✅ Page boutique principale + catégorie vêtements

### Scénario 2 : Contact
**Question** : "Comment vous contacter ?"
**Avant** : Articles mentionnant le mot "contact"
**Après** : ✅ Page contact directement

### Scénario 3 : Navigation
**Question** : "Où est votre boutique ?"
**Avant** : Ne trouve rien
**Après** : ✅ Page boutique avec description

---

## 📦 Installation

### Fichiers à remplacer

```bash
wp-content/plugins/ia1-plugin/includes/
├── class-ia1-indexer.php    # Remplacer ✓
└── class-ia1-mistral.php     # Remplacer ✓
```

### Après remplacement

1. **Désactiver puis réactiver** le plugin IA1 (optionnel mais recommandé)
2. **Réindexer le contenu** : WordPress Admin → IA1 → Indexation → "Réindexer tout le contenu"
3. **Tester** avec des questions variées

---

## 🧪 Tests recommandés

### Test 1 : Achat
```
Question : "Je veux acheter un produit"
Résultat attendu : Renvoie vers la boutique principale
```

### Test 2 : Navigation
```
Question : "Où trouver vos services ?"
Résultat attendu : Renvoie vers la page services/prestations
```

### Test 3 : Information spécifique
```
Question : "Quel est le prix du produit X ?"
Résultat attendu : Renvoie vers le produit X spécifique
```

---

## 🔍 Comment ça marche en pratique ?

### Exemple concret : Site Celtic Social Club

#### Avant v3.1.10
```
Question : "Je cherche un t-shirt"
Scoring :
- Article blog "Interview" (mention t-shirt) : 15 pts
- Article blog "Tournée" (mention t-shirt) : 12 pts
- Page "Boutique" : 5 pts (peu de mentions directes)

Résultat : Renvoie vers les articles de blog ❌
```

#### Après v3.1.10
```
Question : "Je cherche un t-shirt"
Scoring :
- Page "Boutique" (2000 caractères, type:page) : 5 + 50 + 40 = 95 pts ✓
- Catégorie "Vêtements" (1500 caractères, type:page) : 15 + 30 + 40 = 85 pts ✓
- Article blog "Interview" : 15 pts

Détection d'intention : ACHAT
Instructions à l'IA : "Oriente vers pages principales"

Résultat : Renvoie vers la boutique et catégorie vêtements ✅
```

---

## 🚀 Avantages universels

### ✅ Pour les boutiques e-commerce
- Dirige automatiquement vers les pages catégories
- Privilégie les pages produits principales

### ✅ Pour les sites de services
- Trouve les pages de prestations/services
- Oriente vers les pages descriptives principales

### ✅ Pour les blogs
- Trouve les catégories d'articles
- Privilégie les pages "À propos", "Archive"

### ✅ Pour tous les sites
- Meilleure hiérarchie de l'information
- Navigation plus intuitive
- Réponses plus pertinentes

---

## 🔧 Compatibilité

- ✅ WordPress 5.8+
- ✅ PHP 7.4+
- ✅ WooCommerce (optionnel)
- ✅ Tous post types personnalisés
- ✅ Multilingue compatible
- ✅ Pas de modification de base de données

---

## 📝 Notes techniques

### Performance
- Pas d'impact significatif sur les performances
- Les calculs de scoring sont optimisés
- Cache WordPress utilisé naturellement

### Maintenance
- Code commenté et documenté
- Facile à étendre avec de nouveaux patterns
- Compatible avec les futures versions de WordPress

---

## 🎓 Pour les développeurs

### Ajouter vos propres patterns de détection

Dans `detect_content_category()` :
```php
$patterns = array(
    'votre_categorie' => array( 'mot1', 'mot2', 'mot3' ),
);
```

### Ajuster les boosts de scoring

Dans `search()` :
```php
CASE
    WHEN LENGTH(content) > 2000 THEN 50  // Ajustez ces valeurs
    WHEN LENGTH(content) > 1000 THEN 30
```

### Ajouter des intentions

Dans `detect_query_intent()` :
```php
'votre_intention' => array( 'mot_declencheur1', 'mot2' ),
```

---

Développé avec ❤️ pour rendre IA1 plus intelligent • v3.1.10

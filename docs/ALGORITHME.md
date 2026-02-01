# Algorithme de recherche — IA1 v3.1.10

IA1 n'est pas un moteur de recherche classique qui compte les occurrences de mots. C'est un système de scoring multicritère qui comprend la **structure** de votre site et l'**intention** de l'utilisateur.

---

## Le scoring multicritère

Chaque page de votre site reçoit un score en temps réel lors d'une recherche. Le résultat avec le score le plus élevé est celui qui sera utilisé pour générer la réponse.

### Les 6 critères

| # | Critère | Points | Logique |
|---|---------|--------|---------|
| 1 | Correspondance titre exacte | +200 | Le titre correspond à ce qu'on cherche |
| 2 | Occurrences dans le titre | +15 par occurrence | Le mot apparaît dans le titre |
| 3 | Occurrences dans le contenu | +2 par occurrence | Le mot apparaît dans le texte |
| 4 | **Hub pages** | +15 à +50 | Les pages longues sont des pages importantes |
| 5 | **Hiérarchie post types** | +10 à +40 | Les pages WordPress > les posts de blog |
| 6 | Titre court et ciblé | +25 | Titre <50 caractères + mot-clé = page spécifique |

### Hub pages (critère 4 en détail)

Une "hub page" est une page qui contient beaucoup de contenu — c'est généralement une page principale du site (boutique, services, à propos). Le système les détecte automatiquement par volume :

| Volume de contenu | Points ajoutés | Exemple |
|-------------------|----------------|---------|
| >2000 caractères | +50 | Page "Boutique" avec catalogue complet |
| >1000 caractères | +30 | Page "À propos" détaillée |
| >500 caractères | +15 | Page moyenne |

**Aucune configuration manuelle n'est nécessaire.** Le système reconnaît automatiquement ces pages sur n'importe quel site WordPress.

### Hiérarchie post types (critère 5 en détail)

| Post type | Points | Raison |
|-----------|--------|--------|
| `page` WordPress | +40 | Les pages sont généralement plus importantes que les posts |
| `product` WooCommerce | +30 | Les produits sont du contenu structuré |
| `post` (article de blog) | +20 | Les articles sont du contenu secondaire |
| Autres | +10 | Par défaut |

---

## La détection d'intention

Avant même de chercher dans votre contenu, IA1 analyse **pourquoi** l'utilisateur pose sa question. Cette analyse change les instructions données à Mistral AI.

### Les 4 intentions reconnues

| Intention | Mots-clés détectés | Instructions à l'IA |
|-----------|--------------------|--------------------|
| **Achat** | acheter, prix, commander, disponible, coût | "Privilégie les pages boutique et catégories" |
| **Navigation** | où est, comment aller, trouver, accéder | "Dirige vers les pages principales du site" |
| **Information** | qu'est-ce que, comment fonctionne, explication | Synthèse depuis tous les contenus pertinents |
| **Contact** | contacter, joindre, numéro, email, adresse | "Renvoie vers la page contact" |

### La détection de catégorie

En parallèle, chaque page est automatiquement classifiée selon son type :

| Catégorie | Mots-clés utilisés |
|-----------|--------------------|
| Boutique | boutique, shop, acheter, commander |
| Contact | contact, nous contacter |
| À propos | à propos, qui sommes-nous |
| FAQ | faq, questions, aide |
| Blog | blog, actualités, news |

Cette classification est utilisée pour enrichir le contexte envoyé à Mistral AI — les pages principales sont marquées `[PAGE PRINCIPALE]` dans les métadonnées.

---

## Exemple concret

**Question posée** : "Je cherche un t-shirt"

### Avant v3.1.10 (scoring simple)

Le système comptait uniquement les occurrences du mot "t-shirt" :
- Article de blog mentionnant "t-shirt" dans le texte → score élevé ❌
- Page Boutique avec le catalogue complet → score identique ou plus bas ❌

**Résultat** : l'IA renvoyait vers un article de blog.

### Après v3.1.10 (scoring multicritère)

| Page | Correspondance | Hub page | Post type | Total |
|------|---------------|----------|-----------|-------|
| Article de blog "Le t-shirt de l'été" | +200 (titre) +2×3 (contenu) | 0 (300 chars) | +20 (post) | **226 pts** |
| Page Boutique (catalogue complet) | +2×8 (contenu) | +50 (>2000 chars) | +40 (page) | **106 pts** |

En plus, l'intention "Achat" est détectée → l'IA reçoit l'instruction "privilégie les pages boutique". Elle combine le scoring ET l'intention pour diriger vers la boutique. ✅

---

## Fichiers concernés

| Fichier | Rôle |
|---------|------|
| `includes/class-ia1-indexer.php` | Calcul du scoring multicritère |
| `includes/class-ia1-mistral.php` | Détection d'intention, construction du prompt |

### Méthodes principales

- `detect_content_category()` — classifie une page selon son type
- `detect_query_intent()` — analyse l'intention de la question
- `build_intent_instructions()` — génère les instructions contextuelles pour l'IA

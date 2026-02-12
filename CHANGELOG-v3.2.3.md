# IA1 - Changelog v3.2.3

**Date:** 12 février 2026  
**Type:** Correction de bugs critiques  

## 🐛 Bugs corrigés

### 1. Problème d'indexation des blocs Gutenberg
**Symptôme :** Les pages créées avec l'éditeur Gutenberg n'étaient pas indexées correctement. Le contenu extrait était vide ou quasi-vide.

**Cause :** La fonction `wp_strip_all_tags()` était appliquée directement sur le contenu brut des blocs Gutenberg.

**Solution :** Application de `apply_filters('the_content')` avant le nettoyage pour rendre correctement les blocs Gutenberg et shortcodes.

### 2. Problème d'indexation des produits WooCommerce (CRITIQUE)
**Symptôme :** Les produits WooCommerce étaient indexés (19 produits visibles dans les stats) mais INVISIBLES aux recherches. Aucune requête ne trouvait les produits ("CD Love Is A Madness", "vinyle", "boutique" ne retournaient rien).

**Cause :** Les produits WooCommerce ont souvent peu de contenu dans `post_content` (description longue). L'essentiel de l'information est dans les métadonnées : description courte, prix, SKU, catégories, etc. Ces métadonnées n'étaient PAS indexées.

**Solution :** Création d'une méthode `enrich_woocommerce_product_content()` qui enrichit l'indexation avec :
- Description longue (post_content)
- Description courte (métadonnée WooCommerce)
- Prix formaté pour recherche ("Prix : XX euros", "XX€")
- SKU / Référence produit
- Catégories produits (CD, Vinyle, Merchandising, etc.)
- Tags produits
- Attributs (format, taille, couleur, etc.)
- Stock status (En stock / Rupture)

**Impact :**
- ✅ Tous les produits WooCommerce sont maintenant trouvables
- ✅ Recherche par nom de produit fonctionne
- ✅ Recherche par catégorie ("CD", "vinyle") fonctionne
- ✅ Recherche par prix fonctionne
- ✅ Recherche générique ("boutique", "acheter") fonctionne

## 📝 Fichiers modifiés

- `includes/class-ia1-indexer.php` : 
  - Ligne 46-53 : Méthode `index_post()` avec apply_filters + détection WooCommerce
  - Lignes 82-165 (nouvelle) : Méthode `enrich_woocommerce_product_content()`
- `ia1-plugin.php` : Version mise à jour vers 3.2.3

## 🚀 Déploiement

**IMPORTANT :** Après mise à jour, **réindexer OBLIGATOIREMENT** le contenu :
1. Aller dans IA1 → Indexation
2. Cliquer sur "Réindexer tout le contenu"
3. Vérifier que le nombre de pages/produits est cohérent
4. **TESTER les produits WooCommerce immédiatement**

**Sites concernés :**
- celticsocialclub.com (19 produits à réindexer)
- jcgilbert.fr
- ia1.fr
- Tous les sites utilisant IA1 avec WooCommerce

## 🔍 Tests de validation

### Test Gutenberg
1. Créer une page avec blocs Gutenberg
2. Réindexer
3. Poser une question sur cette page
4. ✅ L'IA doit trouver et citer la page

### Test WooCommerce (CRITIQUE)
1. Réindexer le contenu
2. Poser les questions suivantes :
   - "Quel est le prix du CD Love Is A Madness ?"
   - "Je veux acheter un vinyle"
   - "Y a-t-il des CD disponibles ?"
3. ✅ L'IA doit trouver et citer les produits avec les prix

## ⚠️ Note importante

Cette version corrige un bug CRITIQUE pour tous les sites e-commerce utilisant WooCommerce. Sans cette correction, les produits sont invisibles à l'IA malgré leur présence dans l'index.

---

**Version précédente :** 3.2.2 (non déployée)  
**Compatibilité WordPress :** 5.8+  
**Compatibilité PHP :** 7.4+  
**Compatibilité WooCommerce :** 3.0+

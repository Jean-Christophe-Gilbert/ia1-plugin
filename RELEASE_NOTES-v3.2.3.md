# IA1 v3.2.3 - Correction CRITIQUE WooCommerce + Gutenberg

## 🚨 Version URGENTE

Cette version corrige deux bugs critiques qui rendaient le plugin inutilisable pour :
- Les sites utilisant l'éditeur Gutenberg (WordPress moderne)
- Les sites e-commerce WooCommerce

## 🐛 Bugs corrigés

### 1. Indexation Gutenberg (pages vides)
Les pages créées avec l'éditeur Gutenberg n'étaient pas indexées correctement. Le contenu était vide après nettoyage.

**Solution :** Application de `apply_filters('the_content')` avant nettoyage.

### 2. Indexation WooCommerce (CRITIQUE)
**Symptôme grave :** Les produits étaient indexés (visibles dans les stats) mais TOTALEMENT INVISIBLES aux recherches. Aucune requête ne trouvait les produits.

**Impact réel :** Sur Celtic Social Club, 19 produits indexés mais :
- "CD Love Is A Madness" → Aucun résultat ❌
- "vinyle" → Aucun résultat ❌
- "boutique" → Aucun résultat ❌

**Cause :** Les produits WooCommerce stockent leurs infos dans des métadonnées (description courte, prix, SKU, catégories) qui n'étaient PAS indexées. Seul le post_content (souvent vide) était indexé.

**Solution :** Enrichissement complet de l'indexation avec :
- Description courte
- Prix (formaté : "25€", "Prix : 25 euros")
- SKU / Référence
- Catégories produits (CD, Vinyle, etc.)
- Tags produits
- Attributs (format, taille, couleur)
- Stock status

## 🔄 Mise à jour

### Fichiers modifiés
- `includes/class-ia1-indexer.php` (corrections majeures + nouvelle méthode)
- `ia1-plugin.php` (version 3.2.3)

### Installation

1. **Télécharger la dernière version**
2. **Remplacer les fichiers du plugin**
3. **CRITIQUE :** Aller dans IA1 → Indexation et cliquer sur "Réindexer tout le contenu"
4. **Tester immédiatement :**
   - Pour WooCommerce : Poser une question sur un produit
   - Pour Gutenberg : Poser une question sur une page récente

## 💡 Impact

### Avant v3.2.3
- ❌ Pages Gutenberg : invisibles
- ❌ Produits WooCommerce : invisibles malgré indexation
- ❌ Sites e-commerce : non fonctionnels

### Après v3.2.3
- ✅ Pages Gutenberg : 100% visibles
- ✅ Produits WooCommerce : 100% trouvables
- ✅ Sites e-commerce : pleinement fonctionnels

## 📋 Compatibilité

- WordPress 5.8+
- PHP 7.4+
- WooCommerce 3.0+
- Mistral AI

## ⚠️ Notes importantes

1. **La réindexation est OBLIGATOIRE** après mise à jour
2. Cette version corrige un bug bloquant pour l'e-commerce
3. Les sites utilisant WooCommerce doivent déployer IMMÉDIATEMENT

## 🙏 Remerciements

Merci à l'équipe du Celtic Social Club pour les tests et le signalement du bug critique WooCommerce.

---

**IA artisanale cultivée à Niort, France** 🇫🇷

**Version précédente :** 3.2.1  
**Prochaine version prévue :** 3.3.0 (améliorations fonctionnelles)

# Guide de déploiement IA1 v3.2.3

## 🚨 URGENCE : Correction critique WooCommerce + Gutenberg

## 🎯 Objectifs
1. Corriger le bug d'indexation des blocs Gutenberg (pages vides)
2. **Corriger le bug CRITIQUE WooCommerce** (19 produits indexés mais invisibles)

## 📦 Fichiers à déployer

### Fichiers modifiés
- `includes/class-ia1-indexer.php` (corrections majeures)
- `ia1-plugin.php` (version 3.2.3)

## 🚀 Procédure de déploiement URGENTE

### Priorité 1 : Celtic Social Club (site e-commerce)

**La boutique est cassée - les clients ne peuvent pas trouver les produits via IA1**

1. **Se connecter en FTP** (celticsocialclub.com)

2. **Remplacer les fichiers**
   ```
   wp-content/plugins/ia1-plugin/ia1-plugin.php
   wp-content/plugins/ia1-plugin/includes/class-ia1-indexer.php
   ```

3. **Réindexer IMMÉDIATEMENT**
   - WordPress Admin → IA1 → Indexation
   - Cliquer "Réindexer tout le contenu"
   - Attendre la fin (1-2 minutes)

4. **Test CRITIQUE** (ne pas sauter cette étape)
   - Aller sur la page IA Joe
   - Poser : "Quel est le prix du CD Love Is A Madness ?"
   - ✅ Résultat attendu : L'IA trouve le produit et donne le prix
   - ❌ Si échec : contacter jc@ia1.fr immédiatement

5. **Tests complémentaires**
   - "Je veux acheter un vinyle"
   - "Est-ce que le groupe joue à Penmarch ?" (test Gutenberg)
   - "Y a-t-il des CD disponibles ?"

### Priorité 2 : jcgilbert.fr

1. Remplacer les fichiers (même procédure)
2. Réindexer
3. Tester sur pages Gutenberg récentes

### Priorité 3 : ia1.fr

1. Remplacer les fichiers
2. Réindexer
3. Tester sur pages de documentation

## ✅ Checklist de validation

### Celtic Social Club (CRITIQUE)
- [ ] Fichiers déployés via FTP
- [ ] Réindexation complète effectuée
- [ ] Test produit : "CD Love Is A Madness" → **DOIT TROUVER**
- [ ] Test catégorie : "vinyle" → **DOIT TROUVER**
- [ ] Test page Gutenberg : "Tournée Penmarch" → **DOIT TROUVER**
- [ ] Nombre de documents indexés : ~92 (vérifier cohérence)
- [ ] **Validation client** : demander à un testeur de poser des questions produits

### jcgilbert.fr
- [ ] Fichiers déployés
- [ ] Réindexation effectuée
- [ ] Test pages récentes

### ia1.fr
- [ ] Fichiers déployés
- [ ] Réindexation effectuée
- [ ] Test documentation

## ⏱️ Temps estimé
- Celtic Social Club (URGENT) : 10 minutes
- jcgilbert.fr : 7 minutes
- ia1.fr : 7 minutes
- **Total : ~25 minutes**

## 🔧 En cas de problème

### Les produits ne sont toujours pas trouvés après réindexation

**Diagnostic :**
1. Vérifier que les fichiers ont bien été remplacés (version 3.2.3 visible dans admin)
2. Vider TOUS les caches :
   - WP Fastest Cache
   - Cache navigateur
   - Cache OVH (si applicable)
3. Refaire la réindexation une deuxième fois
4. Vérifier dans la base de données que le contenu des produits est enrichi :
   ```sql
   SELECT title, content FROM wp_ia1_index WHERE post_type = 'product' LIMIT 1;
   ```
   Le champ `content` doit contenir : prix, SKU, catégories, etc.

### La page Gutenberg n'est toujours pas indexée

1. Vérifier que la page est bien "Publiée" (pas brouillon)
2. Vider le cache
3. Réindexer
4. Vérifier dans IA1 → Indexation que le nombre de pages a augmenté

### Erreur lors de la réindexation

- Augmenter la limite de mémoire PHP (memory_limit = 256M)
- Augmenter max_execution_time (300 secondes)
- Réindexer par petits lots si nécessaire

## 📊 Métriques de succès

### Avant v3.2.3 (CASSÉ)
- Pages Gutenberg : 0% trouvées
- Produits WooCommerce : 0% trouvés (malgré 19 indexés)
- Satisfaction utilisateur : 0/10

### Après v3.2.3 (ATTENDU)
- Pages Gutenberg : 100% trouvées
- Produits WooCommerce : 100% trouvés
- Satisfaction utilisateur : 9/10

## 📞 Support

**En cas de problème critique :**
- Email : jc@ia1.fr
- Téléphone : 06 40 75 53 92
- Disponible immédiatement pour Celtic Social Club

---

**ATTENTION :** Cette version corrige un bug BLOQUANT pour l'e-commerce. Le déploiement sur Celtic Social Club est URGENT.

**Date de déploiement :** 12 février 2026  
**Déployé par :** JC  
**Statut :** 🔴 URGENT - Site e-commerce impacté

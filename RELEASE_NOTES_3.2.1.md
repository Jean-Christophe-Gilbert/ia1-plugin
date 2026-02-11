# 🔧 IA1 v3.2.1 — Correction Bug Scoring

**Date de sortie** : 11 février 2026  
**Type** : Correctif critique (Bugfix)

## 🐛 Bug corrigé

### Problème identifié
Le système de recherche n'utilisait **que le premier mot** de la requête utilisateur pour calculer la pertinence des résultats. 

**Exemple concret :**
- Question : "Comment fonctionne IA1 ?"
- Ancien comportement : seul "comment" était utilisé pour scorer les pages
- Résultat : l'IA ne trouvait pas la page "Comment fonctionne IA1" car elle scorait trop bas

### Solution apportée
Le scoring utilise maintenant **TOUS les mots** de la requête pour calculer la pertinence :
- "Comment fonctionne IA1" → les 3 mots sont maintenant pris en compte
- Chaque mot est pondéré dans : titre (×15), taxonomies (×20), contenu (×2)
- L'extraction d'excerpts utilise aussi tous les mots pour trouver les passages pertinents

## 📊 Impact

### Avant (v3.2.0)
```
Requête : "Comment fonctionne IA1"
→ Scoring uniquement sur "comment"
→ Pages génériques trouvées en premier
→ Mauvaise pertinence
```

### Après (v3.2.1)
```
Requête : "Comment fonctionne IA1"
→ Scoring sur "comment" + "fonctionne" + "ia1"
→ La bonne page est trouvée en premier
→ Excellente pertinence
```

## 🚀 Installation

### Mise à jour depuis 3.2.0
1. Désactiver le plugin actuel
2. Supprimer l'ancien dossier `ia1-plugin`
3. Uploader la nouvelle version 3.2.1
4. Réactiver le plugin
5. **Pas besoin de réindexer** — l'index existant fonctionne avec le nouveau scoring

### Installation depuis GitHub
```bash
cd wp-content/plugins/
rm -rf ia1-plugin  # Supprimer l'ancienne version
git clone https://github.com/Jean-Christophe-Gilbert/ia1-plugin.git
```

Puis dans WordPress : Réactiver "IA1"

## 🔍 Fichiers modifiés

- `ia1-plugin.php` : Version 3.2.0 → 3.2.1
- `includes/class-ia1-indexer.php` : Refonte fonction `search()` lignes 247-295
- `CHANGELOG.md` : Ajout entrée v3.2.1

## ✅ Testé sur

- WordPress 6.4+
- PHP 7.4, 8.0, 8.1, 8.2
- Avec/sans WooCommerce
- Indexation : 50 à 5000+ contenus

## 💡 Recommandation

**Mise à jour fortement recommandée** si vous constatez que l'IA ne trouve pas les bonnes pages avec des questions composées de plusieurs mots.

## 📞 Support

Questions ou problèmes ? 
- Email : jc@ia1.fr
- GitHub Issues : https://github.com/Jean-Christophe-Gilbert/ia1-plugin/issues

---

Développé avec ❤️ par [IA1](https://ia1.fr) à Niort, France

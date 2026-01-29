# Changelog - IA1 WordPress Plugin

## [3.0.0] - 2026-01-26

### 🔄 Changement majeur : Renommage Lydia → IA1

**Pourquoi ce changement ?**

Le nom "Lydia" était déjà déposé et utilisé par d'autres services (notamment l'application de paiement mobile française Lydia). Pour éviter toute confusion et respecter les marques existantes, nous avons rebaptisé le plugin **IA1** (Intelligence Artificielle 1).

**Ce qui change :**

- Le plugin s'appelle maintenant **IA1** au lieu de Lydia
- Toutes les fonctions, classes et constantes sont renommées (lydia_* → ia1_*)
- Les menus WordPress affichent "IA1" au lieu de "Lydia IA"
- L'interface utilisateur affiche "IA1" dans tous les textes
- Le fichier de log devient `ia1-debug.log` au lieu de `lydia-debug.log`
- Le shortcode reste `[lydia_chat]` mais sera remplacé par `[ia1_chat]` dans une future version mineure

**Ce qui ne change PAS :**

- Toutes les fonctionnalités restent identiques
- L'API Mistral AI fonctionne de la même manière
- Vos réglages et votre clé API sont préservés
- Le système d'indexation continue de fonctionner
- L'interface utilisateur garde le même design

**Migration automatique :**

- Les options WordPress sont automatiquement migrées (lydia_* → ia1_*)
- Aucune action n'est requise de votre part
- Vos données indexées sont conservées

**Important :**

Si vous utilisez le shortcode `[lydia_chat]` dans vos pages, il continuera de fonctionner. Nous recommandons cependant de le remplacer progressivement par `[ia1_chat]` pour les nouvelles installations.

---

## [2.2.8] - 2025-12-27

### Améliorations
- Affichage des sources dans l'interface utilisateur
- Amélioration de la gestion des citations
- Optimisation de la récupération du contexte

### Corrections
- Correction de bugs mineurs dans l'indexation
- Amélioration de la robustesse de l'API Mistral

---

## Versions antérieures

Les versions 2.0.0 à 2.2.7 étaient distribuées sous le nom "Lydia".

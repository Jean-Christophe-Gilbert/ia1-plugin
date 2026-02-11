# Changelog — IA1 WordPress Plugin

Tous les changements notables sont documentés ici.  
Format inspiré de [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/).

---

## [3.2.1] — 2026-02-11

### Corrigé

- **BUG MAJEUR : Scoring de recherche** : Le système n'utilisait que le premier mot de la requête pour calculer la pertinence, ce qui rendait les recherches multi-mots inefficaces
  - Avant : "Comment fonctionne IA1" → seul "comment" était utilisé pour le scoring
  - Maintenant : TOUS les mots de la requête sont pris en compte dans le calcul de pertinence
  - Impact : Recherche beaucoup plus précise, l'IA trouve maintenant les bonnes pages même avec des questions complexes
- **Extraction d'excerpts** : Utilise maintenant tous les mots de la requête pour extraire les passages pertinents, pas seulement le premier

### Technique

- Refonte de la fonction `search()` dans `class-ia1-indexer.php` pour calculer le score sur tous les mots-clés
- Amélioration de l'algorithme de scoring : chaque mot est maintenant pondéré dans titre, taxonomies et contenu

---

## [3.1.10] — 2026-01-30

### Ajouté

- **Algorithme de scoring multicritère** : 6 critères pondérés remplacent le comptage simple de mots
  - Correspondance titre (+200 pts)
  - Occurrences titre (+15 pts/occurrence)
  - Occurrences contenu (+2 pts/occurrence)
  - Hub pages : détection automatique des pages principales par volume de contenu (+15 à +50 pts)
  - Hiérarchie post types : `page` (+40 pts) > `product` (+30 pts) > `post` (+20 pts)
  - Titres courts et ciblés (+25 pts)
- **Détection d'intention** : l'IA comprend si l'utilisateur veut acheter, naviguer, s'informer ou contacter
- **Détection de catégorie automatique** : reconnaissance des pages boutique, contact, FAQ, à propos — sans configuration manuelle
- **Métadonnées enrichies** : les pages principales sont marquées `[PAGE PRINCIPALE]` dans le contexte envoyé à l'IA
- **Affichage des sources** en liste à puces avec icônes et puces colorées

### Modifié

- **Température par défaut** : 0.7 → 0.5 pour des réponses plus cohérentes
- **Prompt système** : instructions adaptées selon l'intention détectée
- **Extraction des excerpts** : plus intelligente, favorise les passages les plus pertinents
- **CSS du chat** : nouveau style pour les sources (fond bleu, puces personnalisées)

### Technique

- Nouvelles méthodes : `detect_content_category()`, `detect_query_intent()`, `build_intent_instructions()`
- Scoring SQL avec 6 critères pondérés dans `class-ia1-indexer.php`
- Seuil hub page : pages avec >2000 caractères automatiquement priorisées

---

## [3.1.9] — 2026-01-28

### Corrigé

- Correction encodage UTF-8 pour les caractères français accentués
- Fix recherche avec caractères spéciaux
- Amélioration de la sanitization des requêtes

---

## [3.1.8] — 2026-01-27

### Corrigé

- Correction des timeouts serveur sur les grosses indexations
- Optimisation des requêtes SQL

---

## [3.1.7] — 2026-01-27

### Ajouté

- Support de tous les post types publics (pas uniquement posts et pages)
- Indexation automatique à la publication d'un contenu

---

## [3.0.0] — 2026-01-26

### 🔄 Changement majeur : Renommage Lydia → IA1

Le nom "Lydia" était déjà déposé et utilisé par d'autres services (notamment l'application de paiement mobile française Lydia). Le plugin est rebaptisé **IA1** (Intelligence Artificielle 1).

#### Ce qui change

- Nom du plugin : Lydia → IA1
- Toutes les fonctions, classes et constantes renommées (`lydia_*` → `ia1_*`)
- Menus WordPress : "IA1" au lieu de "Lydia IA"
- Fichier principal : `ia1-ai-plugin.php`
- Fichier de log : `ia1-debug.log`
- Nouveau shortcode : `[ia1_chat]` (l'ancien `[lydia_chat]` reste supporté)

#### Ce qui ne change pas

- Toutes les fonctionnalités restent identiques
- L'API Mistral AI fonctionne de la même manière
- Réglages et clé API préservés automatiquement
- Index de contenu conservé

#### Migration

Automatique. Les options WordPress sont migrées (`lydia_*` → `ia1_*`). Voir [docs/MIGRATION.md](docs/MIGRATION.md).

---

## [2.2.8] — 2025-12-27

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

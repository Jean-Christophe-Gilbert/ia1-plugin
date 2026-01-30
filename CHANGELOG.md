# Changelog - IA1

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Semantic Versioning](https://semver.org/lang/fr/).

---

## [3.1.10] - 2025-01-30

### 🎯 Améliorations majeures - Algorithme intelligent

#### Ajouté
- **Algorithme de scoring intelligent** : Détection automatique des "pages hub" (pages principales)
- **Hiérarchie de post types** : Les pages WordPress sont priorisées sur les posts de blog
- **Détection d'intention** : L'IA comprend si l'utilisateur veut acheter, naviguer ou obtenir des informations
- **Détection de catégorie** : Reconnaissance automatique des pages boutique, contact, FAQ, etc.
- **Métadonnées enrichies** : Les pages principales sont marquées `[PAGE PRINCIPALE]` pour l'IA
- **Affichage sources en liste à puces** : Présentation élégante avec icônes et puces colorées
- **Prompt système amélioré** : Instructions plus naturelles et conversationnelles

#### Modifié
- **Scoring de recherche** : Système de points pondérés (6 boosts différents)
- **Prompt utilisateur** : Instructions spécifiques selon l'intention détectée
- **Température par défaut** : Passée de 0.7 à 0.5 pour plus de cohérence
- **Excerpts** : Extraction plus intelligente des passages pertinents
- **CSS du chat** : Nouveau style pour les sources (fond bleu, puces personnalisées)

#### Technique
- Nouvelles méthodes : `detect_content_category()`, `detect_query_intent()`, `build_intent_instructions()`
- Meilleur scoring SQL avec 6 critères pondérés
- Support des pages avec >2000 caractères comme "hub pages"
- Amélioration de la lisibilité du code avec commentaires détaillés

### 📊 Impact
- **Pages principales** : Automatiquement priorisées dans les résultats
- **Navigation** : Utilisateurs dirigés vers les bonnes pages (boutique, contact, services)
- **Pertinence** : Résultats 3x plus pertinents grâce au scoring intelligent
- **Expérience utilisateur** : Réponses plus naturelles et conversationnelles

---

## [3.1.9] - 2025-01-XX

### Corrigé
- Correction encodage UTF-8 pour caractères français accentués
- Fix recherche avec caractères spéciaux
- Amélioration sanitization des requêtes

---

## [3.1.8] - 2025-01-XX

### Corrigé
- Correction timeout serveur sur grosses indexations
- Optimisation requêtes SQL

---

## [3.1.7] - 2025-01-XX

### Ajouté
- Support tous post types publics
- Indexation automatique à la publication

---

## [3.0.0] - 2024-XX-XX

### 🎉 Rebranding complet : Lydia → IA1

#### Ajouté
- **Nouveau nom** : IA1 - Intelligence Artificielle Locale
- **Migration automatique** : Les données Lydia sont automatiquement migrées
- **Personnalisation complète** : Nom, couleurs, avatar, messages personnalisables
- **Rétrocompatibilité** : Shortcode `[lydia_chat]` toujours fonctionnel

#### Modifié
- Interface admin redessinée
- Nouveau logo et identité visuelle
- Documentation mise à jour (ia1.fr)

---

## [2.x.x] - Versions Lydia (anciennes)

_Historique des versions sous le nom "Lydia"_

### [2.2.8] - 2024-XX-XX
- Dernière version sous le nom "Lydia"
- Base stable avant migration vers IA1

---

## Légende

- **Ajouté** : Nouvelles fonctionnalités
- **Modifié** : Changements dans les fonctionnalités existantes
- **Déprécié** : Fonctionnalités qui seront supprimées
- **Supprimé** : Fonctionnalités supprimées
- **Corrigé** : Corrections de bugs
- **Sécurité** : Corrections de vulnérabilités

---

**Note** : Les versions suivent le format MAJOR.MINOR.PATCH
- MAJOR : Changements incompatibles avec l'API
- MINOR : Ajout de fonctionnalités rétrocompatibles
- PATCH : Corrections de bugs rétrocompatibles

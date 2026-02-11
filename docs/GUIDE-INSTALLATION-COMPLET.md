# 🚀 GUIDE D'INSTALLATION COMPLET - IA1 v3.2

## Vue d'ensemble

Tu vas installer **2 améliorations majeures** :

1. ✅ **Prompts améliorés** → Réponses plus précises avec citations
2. ✅ **Indexation enrichie** → Recherche dans catégories/tags

**Durée totale** : 10-15 minutes  
**Niveau** : Facile (étapes détaillées)

---

## 📦 Fichiers nécessaires

Tu as reçu 3 fichiers à installer :

1. `class-ia1-mistral-improved.php` → Remplace `includes/class-ia1-mistral.php`
2. `class-ia1-indexer-improved.php` → Remplace `includes/class-ia1-indexer.php`  
3. `ia1-migration-v3-2.php` → Nouveau fichier dans `includes/`

---

## 🔒 Étape 0 : BACKUP (OBLIGATOIRE)

### Option A : Via phpMyAdmin
1. Aller dans phpMyAdmin
2. Sélectionner ta base de données
3. Cliquer sur la table `wp_ia1_index`
4. Cliquer sur "Exporter"
5. Télécharger le fichier SQL

### Option B : Via ligne de commande
```bash
cd /chemin/vers/ton/site
mysqldump -u username -p database_name wp_ia1_index > backup_ia1_index_$(date +%Y%m%d).sql
```

### Option C : Via FTP
1. Télécharger TOUT le dossier `wp-content/plugins/ia1-plugin/`
2. Le renommer `ia1-plugin-backup-20250212`
3. Le garder sur ton ordinateur

**✅ Checklist backup** :
- [ ] Backup de la table SQL fait
- [ ] Backup du plugin fait
- [ ] Fichiers sauvegardés dans un endroit sûr

---

## 📝 Étape 1 : Installer les nouveaux fichiers

### Via FTP (RECOMMANDÉ)

1. **Ouvrir FileZilla** (ou ton client FTP)

2. **Naviguer vers** : `/wp-content/plugins/ia1-plugin/includes/`

3. **Renommer les anciens fichiers** :
   - `class-ia1-mistral.php` → `class-ia1-mistral.php.backup`
   - `class-ia1-indexer.php` → `class-ia1-indexer.php.backup`

4. **Uploader les nouveaux fichiers** :
   - Upload `class-ia1-mistral-improved.php`
   - Upload `class-ia1-indexer-improved.php`
   - Upload `ia1-migration-v3-2.php`

5. **Renommer** (important !) :
   - `class-ia1-mistral-improved.php` → `class-ia1-mistral.php`
   - `class-ia1-indexer-improved.php` → `class-ia1-indexer.php`
   - `ia1-migration-v3-2.php` → reste tel quel

**Structure finale** :
```
/wp-content/plugins/ia1-plugin/includes/
├── class-ia1-mistral.php          ← Nouveau fichier
├── class-ia1-mistral.php.backup   ← Ancien (au cas où)
├── class-ia1-indexer.php          ← Nouveau fichier
├── class-ia1-indexer.php.backup   ← Ancien (au cas où)
├── ia1-migration-v3-2.php         ← Nouveau fichier
└── ... autres fichiers
```

### Via SSH (pour les geeks)

```bash
cd /var/www/html/wp-content/plugins/ia1-plugin/includes/

# Backup
cp class-ia1-mistral.php class-ia1-mistral.php.backup
cp class-ia1-indexer.php class-ia1-indexer.php.backup

# Upload des nouveaux fichiers (utilise scp ou autre)
# Puis :
mv class-ia1-mistral-improved.php class-ia1-mistral.php
mv class-ia1-indexer-improved.php class-ia1-indexer.php

# Vérifier
ls -la
```

**✅ Checklist fichiers** :
- [ ] Anciens fichiers renommés en `.backup`
- [ ] Nouveaux fichiers uploadés
- [ ] Nouveaux fichiers renommés correctement
- [ ] `ia1-migration-v3-2.php` présent

---

## 🔄 Étape 2 : Migration de la base de données

### Option A : Automatique (RECOMMANDÉ)

1. **Éditer le fichier principal** `ia1-plugin.php` :

Via FTP, télécharger `ia1-plugin.php`, l'ouvrir avec un éditeur de texte.

2. **Trouver la méthode `activate()`** (vers la ligne 100)

3. **Ajouter ce code JUSTE AVANT le dernier `}` de la méthode** :

```php
        // Version du plugin
        update_option( 'ia1_version', IA1_VERSION );
        
        // === NOUVEAU CODE ICI ===
        // Migration automatique de la base de données vers v3.2
        $current_db_version = get_option( 'ia1_db_version', '3.0.0' );
        
        if ( version_compare( $current_db_version, '3.2.0', '<' ) ) {
            require_once IA1_PLUGIN_DIR . 'includes/ia1-migration-v3-2.php';
            $migration_result = ia1_migrate_database_v3_2();
            
            if ( $migration_result['success'] ) {
                update_option( 'ia1_db_version', '3.2.0' );
                
                // Log pour debug
                error_log( 'IA1 Migration v3.2: ' . $migration_result['message'] );
            }
        }
        // === FIN DU NOUVEAU CODE ===
    }
    
    /**
     * Désactivation du plugin
     */
    public function deactivate() {
```

4. **Sauvegarder et ré-uploader** `ia1-plugin.php`

5. **Dans WordPress Admin** :
   - Aller dans Extensions
   - **Désactiver** IA1
   - **Réactiver** IA1
   
6. **Vérifier** :
   - Pas de message d'erreur
   - Le plugin est actif

### Option B : Manuelle (si Option A échoue)

1. **Aller dans phpMyAdmin**

2. **Sélectionner ta base de données**

3. **Cliquer sur l'onglet "SQL"**

4. **Coller ce code** :

```sql
-- Remplace "wp_" par ton vrai préfixe si différent
ALTER TABLE wp_ia1_index 
ADD COLUMN taxonomy_terms TEXT AFTER url,
ADD COLUMN searchable_text LONGTEXT AFTER taxonomy_terms,
ADD COLUMN hub_score INT DEFAULT 0 AFTER searchable_text;
```

5. **Cliquer sur "Exécuter"**

6. **Vérifier** : Tu devrais voir "3 colonnes ajoutées"

**✅ Checklist migration** :
- [ ] Code ajouté dans `ia1-plugin.php` OU SQL exécuté dans phpMyAdmin
- [ ] Plugin désactivé/réactivé (si Option A)
- [ ] Aucune erreur affichée
- [ ] Le site fonctionne normalement

---

## 🔍 Étape 3 : Réindexation

1. **Aller dans WordPress Admin**

2. **Cliquer sur IA1** dans le menu de gauche

3. **Aller dans l'onglet "Indexation"**

4. **Cliquer sur "Réindexer tout le contenu"**

5. **Attendre** (barre de progression si disponible)
   - Pour 50 posts : ~30 secondes
   - Pour 200 posts : ~2 minutes
   - Pour 500 posts : ~5 minutes

6. **Vérifier le message** :
   - "✅ X posts indexés"
   - Aucune erreur

**✅ Checklist réindexation** :
- [ ] Indexation lancée
- [ ] Indexation terminée sans erreur
- [ ] Nombre de posts indexés cohérent

---

## 🧪 Étape 4 : Tests

### Test 1 : Recherche basique

1. **Aller sur une page avec le chat IA1**
2. **Poser la question** : "Parles-tu de poésies ?"
3. **Résultat attendu** :
   - ✅ Réponse avec des sources citées [Source 1], [Source 2]
   - ✅ Liste des sources en dessous cliquables
   - ✅ Pas de message "je n'ai pas trouvé"

### Test 2 : Citation des sources

1. **Poser une question** : "Quel est le prix d'IA1 ?"
2. **Vérifier** :
   - ✅ La réponse cite [Source 1], [Source 2], etc.
   - ✅ Les sources sont listées en dessous
   - ✅ Les liens fonctionnent

### Test 3 : Recherche dans catégories

1. **Poser une question liée à une catégorie** : "Quels sont tes articles sur [ta catégorie] ?"
2. **Vérifier** :
   - ✅ Trouve les posts de cette catégorie
   - ✅ Pas de message "je n'ai pas trouvé"

### Test 4 : Réponse structurée

1. **Poser une question complexe** : "Comment installer IA1 ?"
2. **Vérifier** :
   - ✅ Réponse structurée (1. 2. 3.)
   - ✅ Pas de Markdown cassé (pas de **)
   - ✅ Sources citées

**✅ Checklist tests** :
- [ ] Test 1 : Recherche basique OK
- [ ] Test 2 : Citations OK
- [ ] Test 3 : Catégories OK
- [ ] Test 4 : Structure OK

---

## 📊 Étape 5 : Vérification technique (optionnel)

### Vérifier la structure de la table

**Via phpMyAdmin** :
1. Ouvrir phpMyAdmin
2. Sélectionner ta base
3. Cliquer sur `wp_ia1_index`
4. Cliquer sur "Structure"
5. **Vérifier que tu vois** :
   - `taxonomy_terms` (TEXT)
   - `searchable_text` (LONGTEXT)
   - `hub_score` (INT)

### Vérifier le contenu indexé

**Via phpMyAdmin** :
1. Onglet "Parcourir" de la table `wp_ia1_index`
2. **Vérifier que** :
   - La colonne `taxonomy_terms` contient des catégories
   - La colonne `hub_score` contient des nombres > 0
   - La colonne `searchable_text` contient du texte

**Via SQL** :
```sql
-- Voir un exemple d'entrée
SELECT post_id, title, taxonomy_terms, hub_score 
FROM wp_ia1_index 
WHERE taxonomy_terms IS NOT NULL
LIMIT 5;
```

**✅ Checklist vérification** :
- [ ] 3 nouvelles colonnes présentes
- [ ] `taxonomy_terms` rempli
- [ ] `hub_score` avec des valeurs > 0

---

## 🎉 Étape 6 : Mise en production

Si tous les tests sont OK :

1. **Supprimer les fichiers backup** (optionnel) :
   - `class-ia1-mistral.php.backup`
   - `class-ia1-indexer.php.backup`

2. **Garder le backup SQL** (au cas où)

3. **Documenter** :
   - Date de mise à jour : [aujourd'hui]
   - Version IA1 : 3.2.0 (custom)
   - Améliorations : Prompts + Indexation

**✅ Checklist production** :
- [ ] Tous les tests passent
- [ ] Site fonctionne normalement
- [ ] Backup SQL conservé
- [ ] Date documentée

---

## 🐛 En cas de problème

### Problème 1 : Erreur lors de la réindexation

**Symptôme** : Message d'erreur rouge lors de la réindexation

**Solution** :
1. Aller dans phpMyAdmin
2. Exécuter : `TRUNCATE TABLE wp_ia1_index;`
3. Réessayer la réindexation
4. Si ça persiste, exécuter le SQL manuel de l'Option B

### Problème 2 : Chat ne répond plus

**Symptôme** : Le chat ne répond plus du tout

**Solution** :
1. Vérifier la console (F12) pour les erreurs JavaScript
2. Restaurer `class-ia1-mistral.php.backup` → `class-ia1-mistral.php`
3. Vider le cache WordPress (si plugin de cache)
4. Réessayer

### Problème 3 : Erreur SQL

**Symptôme** : "Column already exists" ou erreur MySQL

**Solution** :
1. Les colonnes existent déjà → OK, ignorer
2. Juste faire la réindexation (Étape 3)

### Problème 4 : Réponses toujours sans sources

**Symptôme** : Pas de [Source X] dans les réponses

**Solution** :
1. Vérifier que `class-ia1-mistral.php` est bien le nouveau fichier
2. Vider le cache du navigateur (Ctrl+F5)
3. Poser une nouvelle question
4. Si ça persiste, vérifier le contenu du fichier

### Restauration complète

Si GROS problème :

1. **Restaurer les fichiers** :
```bash
cp includes/class-ia1-mistral.php.backup includes/class-ia1-mistral.php
cp includes/class-ia1-indexer.php.backup includes/class-ia1-indexer.php
```

2. **Restaurer la base** :
```bash
mysql -u username -p database_name < backup_ia1_index_YYYYMMDD.sql
```

3. **Réindexer** avec l'ancienne version

---

## 📞 Support

Si problème malgré tout :

1. **Vérifier les logs** :
   - WordPress : `wp-content/debug.log` (si WP_DEBUG activé)
   - Serveur : `/var/log/apache2/error.log` ou `/var/log/nginx/error.log`

2. **Rassembler les infos** :
   - Version WordPress : [?]
   - Version PHP : [?]
   - Message d'erreur exact : [?]
   - Étape où ça bloque : [?]

3. **Contacter** :
   - Email : jc@ia1.fr
   - GitHub : Ouvrir une issue avec les infos ci-dessus

---

## ✅ Checklist finale

Tout est OK si :

- [ ] Les 3 fichiers sont installés
- [ ] La migration SQL a réussi
- [ ] La réindexation est terminée
- [ ] Les 4 tests passent
- [ ] Le site fonctionne normalement
- [ ] Les backups sont conservés

**Félicitations ! IA1 v3.2 est installé ! 🎉**

---

## 📈 Prochaines étapes

1. **Observer** les performances sur quelques jours
2. **Noter** les améliorations constatées
3. **Partager** sur GitHub pour les autres utilisateurs
4. **Proposer** d'autres améliorations si besoin

---

**Version de ce guide** : 1.0 - 12/02/2026
**Compatible avec** : IA1 v3.1.10 → v3.2.0 custom

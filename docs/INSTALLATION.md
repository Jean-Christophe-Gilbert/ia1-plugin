# Guide d'installation — IA1

## Prérequis

- WordPress 5.8 minimum
- PHP 7.4 minimum
- Une clé API Mistral AI (gratuite pour tester sur [console.mistral.ai](https://console.mistral.ai))

---

## 1. Obtenir une clé API Mistral

1. Allez sur [console.mistral.ai](https://console.mistral.ai) et créez un compte gratuit
2. Mistral vous demande un code de confirmation sur votre mobile
3. Une fois connecté : allez dans **API Keys** → créez une nouvelle clé
4. **Copiez-la immédiatement** — vous ne pourrez plus la consulter après

*Temps estimé : 2 minutes*

---

## 2. Télécharger le plugin

Via Git :
```bash
cd wp-content/plugins/
git clone https://github.com/Jean-Christophe-Gilbert/ia1-plugin.git
```

Ou téléchargez le ZIP depuis la [dernière release GitHub](https://github.com/Jean-Christophe-Gilbert/ia1-plugin/releases).

*Temps estimé : 30 secondes*

---

## 3. Installer sur WordPress

Dans votre administration WordPress :

1. Allez dans **Extensions → Ajouter**
2. Cliquez sur **Téléverser une extension**
3. Sélectionnez le fichier ZIP
4. Cliquez sur **Installer maintenant**
5. Cliquez sur **Activer**

*Temps estimé : 1 minute*

---

## 4. Configurer IA1

Allez dans **Réglages → IA1** :

| Paramètre | Quoi faire |
|-----------|------------|
| Clé API Mistral | Collez la clé obtenue à l'étape 1 |
| Modèle | Gardez **Mistral Large** (recommandé). Si les réponses sont trop lentes, basculez sur Mistral Small |
| Température | 0.5 par défaut — bon compromis cohérence/créativité |
| Nombre de contextes | 5 par défaut — augmentez si les réponses manquent de détail |

Cliquez sur **Enregistrer les modifications**.

*Temps estimé : 1 minute*

---

## 5. Personnaliser votre assistant

Dans **IA1 → Configuration**, section Personnalisation :

- **Nom de l'assistant** : remplacez "IA1" par ce que vous voulez
- **Sous-titre** : description courte sous le nom
- **Message d'accueil** : premier message affiché au visiteur
- **Couleur principale** : couleur de l'en-tête et des boutons
- **Initiales avatar** : 1–3 caractères dans l'avatar

La prévisualisation se met à jour en temps réel pendant que vous tapez.

---

## 6. Indexer votre contenu

1. Allez dans **IA1 → Indexation**
2. Cliquez sur **Réindexer tout le contenu**
3. Attendez la fin de l'indexation

IA1 indexe automatiquement les nouveaux contenus à la publication, mais cette première indexation manuelle récupère tout ce qui existe déjà.

---

## 7. Intégrer le chat sur votre site

1. Créez une nouvelle **Page** dans WordPress
2. Donnez-lui un titre (ex : "Assistant IA", "Aide", "Chatbot")
3. Dans le contenu, ajoutez le shortcode :

```
[ia1_chat]
```

Options disponibles :
```
[ia1_chat placeholder="Posez votre question..." height="400px"]
```

4. Publiez la page

*Temps estimé : 30 secondes*

---

## 8. Tester

Visitez votre page et posez une question. Exemples :

- "Quels sont les derniers articles du site ?"
- "Que propose ce site ?"
- "Comment vous contacter ?"

---

## Résolution de problèmes

### "Classe IA1_Settings not found"

Vérifiez que tous les fichiers sont bien uploadés dans la bonne structure (`includes/`, `admin/`, `public/`).

### "Permission denied"

```bash
chmod 644 ia1-ai-plugin.php
chmod -R 755 includes/ admin/ public/
```

### Le widget ne s'affiche pas

Ouvrez l'inspecteur (F12) → onglet Console → vérifiez les erreurs JavaScript.

### Les réponses sont très lentes

Basculez le modèle sur **Mistral Small** dans IA1 → Réglages. Ou vérifiez les contraintes FastCGI de votre hébergeur (connu sur OVH).

### Activer le mode debug

Dans `wp-config.php` :
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Les erreurs apparaîtront dans `wp-content/debug.log`.

---

## Support

- **Email** : jc@ia1.fr
- **Téléphone** : 06 40 75 53 92
- **GitHub Issues** : [ouvrir un ticket](https://github.com/Jean-Christophe-Gilbert/ia1-plugin/issues)

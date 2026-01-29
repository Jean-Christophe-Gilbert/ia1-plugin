# Guide de Migration : Lydia → IA1

## 🔄 Pourquoi ce changement de nom ?

Le nom "Lydia" était déjà utilisé et déposé par d'autres services (notamment l'application de paiement mobile Lydia). Pour éviter toute confusion juridique et commerciale, nous avons rebaptisé le plugin **IA1** (Intelligence Artificielle 1).

## ✅ Migration Automatique

**Bonne nouvelle** : La migration est **100% automatique** ! Vous n'avez rien à faire de particulier.

### Ce qui est automatiquement migré

- ✅ Votre clé API Mistral AI
- ✅ Tous vos réglages (modèle, température, etc.)
- ✅ Votre index de contenu
- ✅ Vos préférences de configuration

### Étapes de migration

1. **Désactiver** l'ancien plugin "Lydia" (ne pas supprimer)
2. **Télécharger** le nouveau plugin "IA1"
3. **Installer** IA1 dans `wp-content/plugins/`
4. **Activer** le plugin IA1

C'est tout ! 🎉

## 📝 Shortcode

### Ancien shortcode (toujours supporté)

```
[lydia_chat]
```

Ce shortcode **continue de fonctionner** pour assurer la compatibilité.

### Nouveau shortcode (recommandé)

```
[ia1_chat]
```

Nous recommandons d'utiliser `[ia1_chat]` pour les nouvelles installations, mais **il n'y a pas d'urgence** à changer.

## 🗂️ Changements techniques

### Fichiers et dossiers

| Avant (Lydia) | Après (IA1) |
|---------------|-------------|
| `lydia-ai-plugin.php` | `ia1-ai-plugin.php` |
| `lydia-debug.log` | `ia1-debug.log` |
| Options WordPress : `lydia_*` | Options WordPress : `ia1_*` |

### Fonctions PHP

Si vous aviez des personnalisations dans votre thème :

| Avant | Après |
|-------|-------|
| `lydia_log()` | `ia1_log()` |
| `class Lydia_WordPress` | `class IA1_WordPress` |
| `do_shortcode('[lydia_chat]')` | `do_shortcode('[ia1_chat]')` (ou garder l'ancien) |

## 🔧 Dépannage

### Le chat ne s'affiche plus

1. Vérifiez que le plugin IA1 est bien **activé**
2. Vérifiez que votre shortcode est bien présent dans la page
3. Videz le cache de votre site si vous utilisez un plugin de cache

### L'indexation ne fonctionne plus

1. Allez dans **IA1 → Indexation**
2. Cliquez sur "Réindexer tout le contenu"
3. Attendez la fin de l'indexation

### Ma clé API ne fonctionne plus

1. Allez dans **IA1 → Réglages**
2. Vérifiez que votre clé API est toujours présente
3. Si elle a disparu, collez-la à nouveau et sauvegardez

### Les logs ne s'affichent plus

Les logs sont maintenant dans `ia1-debug.log` au lieu de `lydia-debug.log`. L'ancien fichier de logs n'est plus utilisé.

## 📞 Support

Si vous rencontrez des problèmes lors de la migration :

- **Email** : jc@ia1.fr
- **Téléphone** : 06 40 75 53 92
- **GitHub Issues** : [Ouvrir un ticket](https://github.com/Jean-Christophe-Gilbert/ia1-plugin/issues)

## ❓ FAQ

### Est-ce que je dois reconfigurer le plugin ?

**Non.** Tous vos réglages sont automatiquement migrés.

### Est-ce que je vais perdre mon index ?

**Non.** Votre index est conservé. Si jamais il y a un problème, vous pouvez simplement réindexer.

### Est-ce que mes visiteurs vont voir une différence ?

**Oui**, légèrement. L'interface affichera "IA1" au lieu de "Lydia", mais l'expérience reste identique.

### Dois-je modifier mes pages ?

**Non**, pas immédiatement. Le shortcode `[lydia_chat]` continue de fonctionner. Vous pouvez changer progressivement vers `[ia1_chat]` si vous le souhaitez.

### Est-ce que l'ancien plugin Lydia sera maintenu ?

**Non.** À partir de la version 3.0.0, seul **IA1** sera maintenu et mis à jour. Lydia 2.x ne recevra plus de mises à jour.

### Combien de temps le shortcode [lydia_chat] sera-t-il supporté ?

Au moins jusqu'à la version 4.0.0, soit **minimum 12 mois**. Nous vous préviendrons largement à l'avance avant toute dépréciation.

---

**Merci de votre confiance !** 🙏

Si vous avez des questions, n'hésitez pas à nous contacter.

**L'équipe IA1**  
jc@ia1.fr • 06 40 75 53 92

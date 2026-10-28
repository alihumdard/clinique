# Documentation technique
## Clinique Juridique FSJP-UAM — plateforme numérique

Ce document s'adresse au service informatique de l'UAM et à toute personne qui
reprendra le code. Le guide de l'administrateur, à côté, s'adresse à la Clinique.

---

## 1. Pile technique

| | |
| --- | --- |
| PHP | 8.2 ou plus récent |
| Framework | Laravel 12 |
| Base de données | MySQL/MariaDB en production, SQLite en développement |
| Frontend | Blade, Tailwind CSS 4, JavaScript natif |
| Build | Vite 7 (Node 20 ou plus récent) |
| Serveur | nginx + php-fpm |

**Aucun script distant.** Pas de jQuery, pas de police hébergée ailleurs, pas de
bibliothèque de graphiques, pas de widget de traduction. La politique de sécurité
envoyée avec chaque page l'impose (`script-src 'self'`), et la version anglaise
du site est servie depuis `lang/en.json` plutôt que par un service tiers.

---

## 2. Deux bases de données

C'est la contrainte structurante du projet, et elle vient du plan : *« le
registre dispose de ses propres comptes et de sa propre base de données ; les
données confidentielles des dossiers ne sont pas stockées dans la base du site
public »*.

| Connexion | Contenu |
| --- | --- |
| `default` | Site public et CMS : pages, actualités, médias, comptes du CMS, abonnés, messages de contact |
| `registre` | Tout le confidentiel : demandeurs, dossiers, affectations, échéances, documents, journal d'accès, **et les pré-saisines reçues** |

Les pré-saisines sont écrites dans la base du registre bien que ce soit le site
public qui les reçoive : un nom, un numéro de téléphone et le récit d'un problème
sont exactement ce que la règle ci-dessus vise.

Conséquence à connaître : **aucune clé étrangère ne traverse les deux bases.** Le
domaine juridique d'un dossier est recopié en texte plutôt que joint à la table
du site public — et c'est aussi ce qu'il faut : un dossier garde la formulation
sous laquelle il a été ouvert.

### Variables d'environnement

```dotenv
# Site public
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=clinique
DB_USERNAME=
DB_PASSWORD=

# Registre — base distincte, compte distinct, sans droits sur la précédente
REGISTRE_DB_DRIVER=mysql
REGISTRE_DB_HOST=127.0.0.1
REGISTRE_DB_DATABASE=clinique_registre
REGISTRE_DB_USERNAME=
REGISTRE_DB_PASSWORD=
```

Laissées vides, les variables `REGISTRE_*` font suivre au registre le moteur du
site public et un fichier SQLite séparé — pratique en développement, à ne pas
laisser ainsi en production.

### Migrations

```bash
# Site public
php artisan migrate --force

# Registre — chemin et connexion explicites
php artisan migrate --database=registre --path=database/migrations/registre --force
```

⚠️ **`scripts/deploy.sh` ne lance aujourd'hui que la première.** La seconde ligne
devra y être ajoutée le jour où le registre est mis en service, une fois la base
créée.

---

## 3. Authentification

Deux gardes Laravel distincts, chacun sur sa table :

| Garde | Table | Connexion | Entrée |
| --- | --- | --- | --- |
| `web` | `users` | défaut | `/admin/login` |
| `registre` | `registre_users` | registre | `/registre/connexion` |

Être connecté à l'un ne donne rien sur l'autre ; deux tests le vérifient.

Dans les deux cas : limitation des tentatives (5 essais, blocage 5 minutes, par
adresse e-mail **et** par IP), régénération de l'identifiant de session à la
connexion, invalidation complète à la déconnexion, et fin de session après
**30 minutes d'inactivité**.

Un compte désactivé échoue à la connexion comme un mauvais mot de passe :
`is_active` fait partie des identifiants, ce n'est pas une vérification faite
ensuite.

---

## 4. Fichiers

| Disque | Chemin | Contenu |
| --- | --- | --- |
| `public` | `storage/app/public` | Images des contenus publics |
| `local` | `storage/app/private` | **Tout le reste** |

Le disque privé est **hors de la racine web**. Rien n'y est jamais lié
directement : un contrôleur vérifie les droits du lecteur, inscrit l'accès au
journal, puis diffuse le fichier.

```
documents/…                     fiches pratiques et rapports publiés
presaisine/{année}/{réf}/…      pièces jointes d'une demande en attente
presaisine/brouillons/{jeton}/… formulaire commencé, jamais envoyé
dossiers/{année}/{réf}/…        pièces d'un dossier
```

---

## 5. Tâches planifiées

```cron
* * * * * cd /var/www/clinique && php artisan schedule:run >> /dev/null 2>&1
```

Sans cette ligne, rien ne s'exécute automatiquement. Elle fait tourner :

**`clinique:conservation`** — chaque nuit à 3 h 30. Applique trois règles de
conservation du module 4 :

- pré-saisines jamais acceptées : supprimées après **6 mois**, pièces jointes
  comprises ;
- journal d'accès : conservé **12 mois** ;
- dossiers de brouillon abandonnés : balayés après **7 jours**.

`php artisan clinique:conservation --dry-run` montre ce qui serait supprimé sans
rien supprimer.

> Les deux règles qui touchent aux dossiers eux-mêmes — cinq ans pour un dossier
> clos, un an pour un dossier rejeté — **ne sont pas implémentées** : toutes deux
> comptent à partir d'une clôture que les treize statuts de la Clinique
> définissent, et ces statuts sont encore attendus.

---

## 6. Courriel

Deux messages partent à l'enregistrement d'une pré-saisine : un accusé de
réception au demandeur s'il a donné une adresse, et un avis à la Clinique.

Ni l'un ni l'autre ne reprend ce que la personne a écrit. Un courriel circule en
clair et arrive dans une boîte que la Clinique ne maîtrise pas : l'accusé porte
la référence, l'avis porte la référence, le domaine et le nombre de pièces
jointes, et le détail reste dans le registre.

L'envoi a lieu **après** l'enregistrement et ne peut pas le défaire : un serveur
en panne est journalisé, et le visiteur reçoit quand même sa référence.

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="Clinique Juridique FSJP-UAM"
```

Tant que `MAIL_MAILER=log`, les messages partent dans `storage/logs`.

---

## 7. Sécurité

Appliqué sur chaque réponse HTML (`SecurityHeaders`) :

```
Content-Security-Policy      default-src 'self' ; script-src 'self' ; …
X-Content-Type-Options       nosniff
X-Frame-Options              SAMEORIGIN
Referrer-Policy              strict-origin-when-cross-origin
Cross-Origin-Opener-Policy   same-origin
Permissions-Policy           camera=(), microphone=(), geolocation=(), …
```

`frame-src` n'ouvre que les hébergeurs vidéo utilisés par la Web TV, et les
lecteurs ne se chargent qu'au clic.

Par ailleurs : CSRF sur chaque formulaire, champs pièges et limitation de débit
sur les formulaires publics, administration et registre exclus de l'indexation,
`Referrer-Policy: no-referrer` sur le registre.

### HTTPS

La plateforme est servie en HTTPS sur un nom d'hôte sslip.io :

```
https://clinique.40-160-97-168.sslip.io
```

sslip.io résout `<n'importe quoi>.40-160-97-168.sslip.io` vers 40.160.97.168 :
aucune zone DNS à tenir, ce qui suffit pour une démonstration. Le certificat
vient de Let's Encrypt et se renouvelle tout seul.

La configuration est dans `/etc/nginx/sites-available/clinique-sslip`, un
fichier à part : ni le bloc historique de la clinique (port 8080) ni celui de
sanem ne sont touchés. Le bloc de sanem écoute le port 80 avec `_` en
server_name, et nginx préfère un nom exact à ce fourre-tout.

`http://40.160.97.168:8080` continue de répondre : le nom d'hôte s'ajoute, il ne
remplace rien.

`upgrade-insecure-requests` s'ajoute de lui-même à la politique de sécurité
depuis que les requêtes sont chiffrées.

**Pour la mise en service**, sslip.io ne convient pas : il faut le domaine de
l'UAM. Le changement se limitera alors au `server_name`, au certificat et à
`APP_URL`.

---

## 8. Déploiement

```bash
sudo bash scripts/deploy.sh
```

Le script enchaîne : `git pull`, `composer install --no-dev`, migrations,
génération des variantes d'images, `npm ci && npm run build`, mise en cache de la
configuration, des routes et des vues, droits sur les fichiers, puis
**rechargement de php-fpm**.

Ce dernier point n'est pas optionnel : OPcache garde les vues Blade compilées en
mémoire partagée. Deux déploiements sont partis sans lui, et le site a continué
de servir les anciennes pages alors que chaque fichier sur le disque était à
jour.

`public/build` est ignoré par git : le serveur construit lui-même les assets, et
`npm ci` doit installer les dépendances de développement (Vite et Tailwind en
sont).

### À ajouter avant la mise en service du registre

1. Créer la base `clinique_registre` et son compte, sans droits sur l'autre.
2. Renseigner les `REGISTRE_DB_*`.
3. Ajouter la migration du registre au script de déploiement.
4. Réserver le sous-domaine du registre.
5. Installer la ligne de cron du planificateur.

---

## 9. Tests

```bash
php vendor/bin/phpunit
```

**522 tests.** Ils tournent sur SQLite en mémoire, avec une base distincte pour
le registre — de sorte qu'un test ne puisse pas démontrer la séparation contre
une base unique.

Un test qui a besoin des tables du registre le déclare par le trait
`RefreshesRegistreDatabase` : rien du schéma du registre n'existe dans un test
qui ne l'a pas demandé.

---

## 10. Ce qui reste en attente

**De la Clinique :**

- les **libellés exacts des treize statuts** et leurs transitions autorisées —
  le cycle de vie fonctionne aujourd'hui sur des statuts provisoires numérotés,
  que leur liste remplacera sans changer une ligne de code ;
- la **matrice des droits** des quatre profils du registre — tout compte actif
  accède pour l'instant à l'ensemble du registre, et chaque geste est journalisé ;
- les identifiants SMTP, le nom de l'hébergeur, les contenus et chiffres réels.

**De l'UAM :**

- nom de domaine et certificat HTTPS ;
- sous-domaine distinct pour le registre ;
- base de données distincte pour le registre ;
- sauvegardes chiffrées quotidiennes, conservées 30 jours, avec restauration
  testée ;
- ligne de cron du planificateur.

# Guide de l'administrateur
## Clinique Juridique FSJP-UAM — plateforme numérique

Ce guide s'adresse aux personnes de la Clinique qui tiennent le site à jour et,
le moment venu, le registre des dossiers. Il n'y a rien de technique ici : tout
se fait depuis un navigateur.

---

## 1. Les deux espaces, et pourquoi ils sont séparés

La plateforme compte **deux espaces distincts**, avec **des comptes différents**.

| | **Administration du site** | **Registre numérique** |
| --- | --- | --- |
| À quoi ça sert | Le contenu du site public | Les dossiers juridiques |
| Adresse | `/admin` | `/registre` |
| Comptes | Les siens | Les siens |

Un compte de l'administration **n'ouvre pas** le registre, et l'inverse est tout
aussi vrai. Ce n'est pas une contrainte technique : les dossiers contiennent des
informations confidentielles sur des personnes, et le plan de développement
demande que les deux soient séparés jusque dans la base de données.

Les deux espaces se ferment seuls après **trente minutes sans activité**. Les
postes de la Clinique sont partagés ; une session oubliée ne reste pas ouverte.

---

## 2. Administration du site

### 2.1 Les deux profils

| Profil | Ce qu'il peut faire |
| --- | --- |
| **Éditeur de contenu** | Rédiger, modifier, publier, dépublier et mettre à la corbeille tous les contenus |
| **Administrateur** | Tout ce qui précède, plus les paramètres, les référentiels, les messages de contact, la newsletter et la suppression définitive |

Un éditeur ne voit pas le groupe « Administration » dans le menu : les écrans
qu'il ne peut pas ouvrir ne lui sont pas proposés.

### 2.2 Les quatre états d'un contenu

Chaque actualité, activité, fiche, capsule ou page passe par quatre états :

- **Brouillon** — en cours d'écriture, invisible du public
- **Publié** — visible, à condition d'avoir une date de publication passée
- **Programmé** — daté dans le futur ; il se publie tout seul le moment venu
- **Archivé** — retiré du site sans être supprimé

Un contenu publié mais daté de demain n'est visible de personne, quelle que soit
l'adresse employée. C'est ce qui permet de préparer une annonce à l'avance.

### 2.3 La corbeille

Chaque section a la sienne. Un contenu supprimé y reste et se restaure en un
clic. Seul un administrateur peut vider définitivement une corbeille — et cette
suppression-là est irréversible.

### 2.4 Ce que vous pouvez tenir à jour

**Contenus** — actualités, Web TV, fiches pratiques, rapports annuels, pages,
appels à candidatures, activités et leurs galeries photos, équipe, partenaires.

**Référentiels** (administrateur) — la grille tarifaire, les domaines juridiques
et les rubriques dans lesquels les autres écrans puisent. Modifier une rubrique
ici la change partout où elle apparaît.

**Paramètres du site** (administrateur) — adresse, téléphone, courriel,
horaires, réseaux sociaux, logo, et le **bandeau d'annonce** affiché en haut de
chaque page. Ce bandeau sert aux fermetures exceptionnelles et aux changements
d'horaires : écrivez le message, choisissez la couleur, activez-le. Pensez à le
désactiver ensuite.

**Messages de contact** (administrateur) — les messages déposés par le
formulaire du site. Vous pouvez les marquer résolus et les supprimer.

**Newsletter** (administrateur) — la liste des abonnés, exportable en CSV pour
votre outil d'envoi. Un abonné désinscrit reste enregistré comme tel, afin qu'un
import ultérieur ne le réinscrive pas.

### 2.5 Les comptes

**Utilisateurs** (administrateur) — créer un compte, changer un rôle,
réinitialiser un mot de passe, désactiver un accès.

Un compte **se désactive, il ne se supprime pas**. Le supprimer effacerait
l'auteur de tout ce qu'il a publié : quelqu'un qui part garde son nom sur son
travail et perd son entrée.

Quatre refus volontaires, et c'est la même raison quatre fois — il doit toujours
rester un administrateur capable de se connecter :

- vous ne pouvez pas désactiver votre propre compte ;
- le dernier administrateur actif ne peut pas être désactivé ;
- il ne peut pas non plus être rétrogradé en éditeur ;
- un compte désactivé échoue à la connexion exactement comme un mauvais mot de
  passe.

**Mon compte** — accessible à tout le monde, en haut à droite. Chacun y change
son nom et son mot de passe. Le mot de passe actuel est demandé : une session
restée ouverte ne doit pas suffire à enfermer dehors le titulaire du compte.

Un mot de passe fait **douze caractères au minimum**. Lorsqu'un administrateur
en pose un pour quelqu'un d'autre, demandez à l'intéressé de le changer depuis
« Mon compte » dès sa première connexion : un mot de passe que quelqu'un d'autre
connaît encore n'est pas un mot de passe.

---

## 3. Registre numérique

> Cet espace est en cours de développement. Ce qui suit fonctionne ; les
> libellés des statuts sont provisoires et seront remplacés par les vôtres.

### 3.1 Demandes en ligne

Les pré-saisines déposées sur le site public arrivent ici. Pour chacune, vous
voyez le demandeur, l'objet, le récit, les questions et les pièces jointes.

Deux décisions :

- **Accepter** — ouvre un dossier qui reprend la demande, y transfère les pièces
  jointes, puis les supprime du serveur de réception ;
- **Refuser** — n'ouvre aucun dossier et supprime les pièces jointes.

Dans les deux cas, la ligne de la demande reste : ce qui est arrivé reste au
dossier, même quand les fichiers n'y sont plus.

### 3.2 Dossiers

La liste se cherche par référence, par nom, par téléphone, par objet, et se
filtre par statut, par urgence et par retard. **Exporter (CSV)** produit un
fichier contenant exactement ce qui était à l'écran.

Un dossier s'ouvre aussi **à la main**, pour une personne reçue à la permanence :
bouton « Nouveau dossier ».

Sur la fiche d'un dossier :

- **Statut** — seuls les passages autorisés sont proposés. Un statut terminal
  clôture le dossier ; en sortir le rouvre.
- **Affectations** — un ou plusieurs étudiants et un encadrant. Une affectation
  se termine, elle ne se supprime pas : qui a travaillé sur un dossier fait
  partie de son histoire.
- **Échéances** — une date et un intitulé. Ce qui est dépassé apparaît en rouge
  et remonte dans le filtre « en retard ».
- **Documents** — mandat signé, analyse, pièce jointe. Rien ne se supprime d'un
  dossier.
- **Analyse** — se rédige, se soumet, puis **se valide par l'encadrant**. Une
  analyse validée ne se réécrit plus, et le retour au demandeur reste bloqué
  tant que la validation n'est pas faite.

### 3.3 Journal

Toute consultation d'un dossier, tout téléchargement, toute décision et tout
export y sont inscrits, avec leur auteur et leur heure. Le journal est en
**lecture seule** : rien dans l'application ne modifie ni ne supprime une ligne.

### 3.4 Comptes du registre

Quatre profils sont prévus : Coordinateur, Superviseur/Encadrant, Étudiant
clinicien, Administrateur technique.

> **En attente** : les droits exacts de chaque profil doivent être arrêtés par
> la Clinique. D'ici là, tout compte actif accède à l'ensemble du registre — et
> chacun de ses gestes est inscrit au journal.

---

## 4. Ce que la plateforme fait toute seule

- Un contenu programmé se publie à sa date.
- Une pré-saisine jamais acceptée est supprimée au bout de **six mois**, avec ses
  pièces jointes.
- Le journal d'accès est conservé **douze mois**.
- Les fichiers déposés sur un formulaire jamais envoyé sont balayés au bout d'une
  semaine.
- Les deux espaces se ferment après trente minutes d'inactivité.

---

## 5. Questions courantes

**J'ai supprimé un contenu par erreur.** Il est dans la corbeille de sa section.

**Une actualité ne s'affiche pas sur le site.** Vérifiez son état et sa date de
publication : un contenu publié mais daté de demain reste invisible.

**Quelqu'un a quitté la Clinique.** Désactivez son compte ; ne le supprimez pas.

**Je ne vois pas l'écran Paramètres.** Votre compte est éditeur. Un
administrateur peut changer cela depuis Utilisateurs.

**Le site affiche « Non sécurisé ».** Le certificat HTTPS dépend du nom de
domaine, attendu du service informatique de l'UAM.

**Je n'ai pas reçu de notification de pré-saisine.** L'envoi de courriels attend
les identifiants SMTP de la Clinique. Les demandes sont bien enregistrées et
visibles dans le registre.

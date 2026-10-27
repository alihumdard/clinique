# Clinique Juridique FSJP-UAM
## Plateforme numérique — Livraison du module 2

**Adresse de consultation :** http://40.160.97.168:8080
**Date de cette version :** 28 octobre 2026
**Statut :** module 2 terminé, ouvert à la relecture

---

## 1. Avant de commencer

Trois points à connaître avant d'ouvrir les pages.

**Le navigateur affichera « Non sécurisé ».** Le site est servi en HTTP sur une
adresse IP ; un certificat HTTPS ne peut être délivré que pour un nom de
domaine. C'est sans conséquence pour une relecture, mais le domaine et le
certificat seront nécessaires avant toute mise en service réelle.

**Les contenus sont fictifs.** Noms, dates, chiffres, photographies et
documents sont des exemples destinés à juger la mise en page et le
fonctionnement. Les contenus réels de la Clinique seront intégrés dès qu'ils
nous seront transmis.

**Les contenus sont désormais modifiables.** Contrairement à la version
précédente, les écrans d'administration enregistrent réellement : ce qui est
ajouté, modifié, publié ou mis à la corbeille depuis l'administration apparaît
immédiatement sur le site public.

---

## 2. Site public — 24 pages

### Pages institutionnelles

| Page | Adresse |
| --- | --- |
| Accueil | http://40.160.97.168:8080/ |
| À propos de la Clinique | http://40.160.97.168:8080/a-propos |
| Notre équipe | http://40.160.97.168:8080/a-propos/equipe |
| Nos partenaires | http://40.160.97.168:8080/partenaires |

### Services et informations pratiques

| Page | Adresse |
| --- | --- |
| Nos services juridiques | http://40.160.97.168:8080/services |
| Processus de consultation | http://40.160.97.168:8080/services/processus-de-consultation |
| Conditions d'accès et gratuité | http://40.160.97.168:8080/services/conditions-acces-gratuite |
| Tarifs administratifs | http://40.160.97.168:8080/services/tarifs-administratifs |

### Actualités et activités

| Page | Adresse |
| --- | --- |
| Actualités | http://40.160.97.168:8080/actualites |
| Détail d'une actualité | http://40.160.97.168:8080/actualites/lancement-de-la-caravane-juridique-a-zinder |
| Activités et caravanes juridiques | http://40.160.97.168:8080/activites |
| Détail d'une activité | http://40.160.97.168:8080/activites/caravane-juridique-a-dosso |
| Galerie photos | http://40.160.97.168:8080/galerie |

### Ressources

| Page | Adresse |
| --- | --- |
| Fiches pratiques juridiques | http://40.160.97.168:8080/ressources/fiches-pratiques |
| Détail d'une fiche pratique | http://40.160.97.168:8080/ressources/fiches-pratiques/guide-pratique-droits-de-la-famille |
| Rapports annuels d'activité | http://40.160.97.168:8080/ressources/rapports-annuels |

### Web TV

| Page | Adresse |
| --- | --- |
| Web TV | http://40.160.97.168:8080/web-tv |
| Détail d'une capsule | http://40.160.97.168:8080/web-tv/lancement-des-activites-de-la-clinique-juridique-fsjp-uam |

### Engagement et contact

| Page | Adresse |
| --- | --- |
| Rejoindre la Clinique | http://40.160.97.168:8080/rejoindre |
| Contact | http://40.160.97.168:8080/contact |

### Pages obligatoires et utilitaires

| Page | Adresse |
| --- | --- |
| Mentions légales | http://40.160.97.168:8080/mentions-legales |
| Conditions d'utilisation | http://40.160.97.168:8080/conditions-utilisation |
| Politique de confidentialité | http://40.160.97.168:8080/politique-confidentialite |
| Résultats de recherche | http://40.160.97.168:8080/recherche |
| Page introuvable (404) | http://40.160.97.168:8080/page-inexistante |

---

## 3. Espace d'administration — 15 écrans

L'accès est protégé : toute adresse sous `/admin` renvoie vers la page de
connexion tant que la session n'est pas ouverte.

### Accès de démonstration

L'adresse de connexion est http://40.160.97.168:8080/admin/login. Les
identifiants de relecture sont transmis séparément : ils ne figurent pas dans
ce document, qui est conservé avec le code source.

Ces comptes sont fictifs et réservés à la relecture. Les comptes réels seront
créés avant la mise en service, avec un mot de passe d'au moins douze
caractères renouvelé à la première connexion.

### Deux profils

| Profil | Ce qu'il peut faire |
| --- | --- |
| **Éditeur de contenu** | Rédiger, modifier, publier, dépublier et mettre à la corbeille tous les contenus du site. |
| **Administrateur** | Tout ce qui précède, plus les paramètres du site, les référentiels, les messages de contact, la newsletter et la suppression définitive. |

Un compte « éditeur » peut être fourni sur demande pour comparer les deux
vues : la barre latérale d'un éditeur ne propose pas le groupe
« Administration », et une adresse saisie à la main y répond « Accès refusé ».

### Écrans

| Écran | Adresse |
| --- | --- |
| Connexion | http://40.160.97.168:8080/admin/login |
| Tableau de bord | http://40.160.97.168:8080/admin |
| Gestion des actualités | http://40.160.97.168:8080/admin/actualites |
| Gestion de la Web TV | http://40.160.97.168:8080/admin/web-tv |
| Gestion des fiches pratiques | http://40.160.97.168:8080/admin/fiches-pratiques |
| Gestion des rapports annuels | http://40.160.97.168:8080/admin/rapports-annuels |
| Gestion des pages | http://40.160.97.168:8080/admin/pages |
| Activités et galerie | http://40.160.97.168:8080/admin/activites |
| Appels à candidatures | http://40.160.97.168:8080/admin/appels |
| Gestion de l'équipe | http://40.160.97.168:8080/admin/equipe |
| Gestion des partenaires | http://40.160.97.168:8080/admin/partenaires |
| Messages de contact | http://40.160.97.168:8080/admin/messages |
| Gestion de la newsletter | http://40.160.97.168:8080/admin/newsletter |
| Référentiels | http://40.160.97.168:8080/admin/referentiels |
| Paramètres du site | http://40.160.97.168:8080/admin/parametres |

Trois de ces écrans ne figuraient pas dans l'inventaire des douze : les
**messages de contact** (sans quoi les messages déposés par le public seraient
enregistrés sans que personne puisse les lire ni les supprimer), les **appels à
candidatures** (dont la page publique « Rejoindre la Clinique » tire son
contenu) et les **référentiels**, qui réunissent la grille tarifaire, les
domaines juridiques et les rubriques dans lesquels les autres écrans puisent.

---

## 4. Ce que la Clinique peut faire elle-même

- Ajouter, modifier, publier, programmer, dépublier et archiver : actualités,
  capsules Web TV, fiches pratiques, rapports annuels, activités et leurs
  galeries, appels à candidatures, membres de l'équipe, partenaires et pages.
- Téléverser des images et des documents PDF. Les PDF sont stockés hors de la
  racine web : on n'y accède que par le site, qui vérifie d'abord que le
  document est publié et compte chaque téléchargement.
- Récupérer un contenu supprimé par erreur : chaque section a sa corbeille.
- Lire, marquer comme traités et supprimer les messages du formulaire de
  contact ; consulter et exporter les abonnés à la newsletter.
- Modifier depuis « Paramètres du site » : adresse, téléphone, courriel,
  horaires, réseaux sociaux, logo, et le bandeau d'annonce affiché en haut de
  chaque page (fermeture exceptionnelle, changement d'horaires).
- Tenir à jour la grille tarifaire, les domaines juridiques et les rubriques.

Un contenu passe par quatre états : **brouillon**, **publié**, **programmé**
(daté dans le futur, publié tout seul le moment venu) et **archivé**. Seul un
contenu publié et daté est visible du public, quelle que soit l'adresse
employée.

---

## 5. Ce qui reste à faire, et par qui

### À transmettre par la Clinique

| Élément | Pourquoi |
| --- | --- |
| Textes, photographies et logos définitifs | Les contenus affichés sont des exemples. |
| Coordonnées de l'équipe et des partenaires | Pour remplacer les fiches d'exemple. |
| Rapports, fiches pratiques et liens des capsules | Les fichiers actuels sont fictifs. |
| **Chiffres clés** (personnes accompagnées, étudiants, activités) | Les nombres affichés sur l'accueil et « À propos » sont des exemples et doivent être validés avant publication. |
| Identifiants d'envoi de courriel (SMTP) | Les messages du formulaire de contact sont enregistrés et lisibles dans l'administration, mais aucune notification n'est envoyée tant qu'un expéditeur n'est pas configuré. |
| **Les treize états des dossiers** et la **matrice des droits** | Ils conditionnent le démarrage du module 3. Le plan demande explicitement de ne pas les inventer. |

### À confirmer par le service informatique de l'UAM

| Élément | Pourquoi |
| --- | --- |
| Nom de domaine et certificat HTTPS | Le site est aujourd'hui servi en HTTP sur une adresse IP. |
| Hébergeur | Les mentions légales portent « à confirmer » en attendant. |
| Sous-domaine distinct du registre | Le module 3 est une application séparée, avec sa propre base. |
| Sauvegardes et restauration | Sauvegardes chiffrées quotidiennes conservées trente jours. |

### Modules suivants

**Module 3 — pré-saisine et registre numérique.** Le parcours public
d'orientation et de dépôt de demande, puis le registre confidentiel des
dossiers : application distincte, base de données distincte, comptes distincts.
Il ne peut commencer sans les treize états et la matrice des droits.

**Module 4 — sécurité, recette, documentation et déploiement.** Gestion des
comptes utilisateurs, sauvegardes, règles de conservation des données, recette
complète et mise en production avec le service informatique de l'UAM.

---

## 6. Performance et sécurité

| Exigence du plan | Mesure sur cette version |
| --- | --- |
| Page d'accueil sous 500 Ko | Respectée |
| Page intérieure sous 400 Ko | Respectée |
| Aucun script distant | Aucun : ni jQuery, ni police d'icônes, ni bibliothèque de graphiques |
| Feuille de style et script du site | 90 Ko et 10 Ko, compressés à 17 Ko et 3 Ko |
| Lecteur vidéo au clic | Confirmé : aucune requête vers la plateforme vidéo avant le clic |
| Images | Format WebP avec repli JPEG, et plusieurs largeurs : un téléphone ne télécharge pas l'image prévue pour un écran de bureau |
| Accessibilité AA | Contraste, navigation au clavier, textes de remplacement |

Côté sécurité : en-têtes de sécurité sur chaque réponse, limitation des
tentatives de connexion, déconnexion automatique après trente minutes
d'inactivité, déconnexion par requête POST uniquement, administration exclue de
l'indexation, documents confidentiels hors de la racine web, et séparation des
droits entre éditeur et administrateur.

Le chiffrement HTTPS n'est pas actif sur cette version de démonstration, faute
de nom de domaine. Il sera nécessaire avant la mise en service.

---

## 7. Points de maquette à trancher

| Point | Détail |
| --- | --- |
| Données marocaines dans les maquettes | Les maquettes portent « Université Al Akhawayn, Ifrane, Maroc », un numéro en +212 et des partenaires marocains. Le site livré utilise les données du Niger ; les maquettes approuvées méritent une correction. |
| Pages Cookies, Accessibilité, Crédits | Présentes dans la barre latérale des maquettes juridiques, absentes de l'inventaire des 24 pages. À construire ou à retirer. |
| Éditeur de texte enrichi | La saisie se fait aujourd'hui en texte simple, les sauts de ligne étant conservés. Une bibliothèque d'édition pèse entre 45 et 200 Ko, ce qui s'oppose au budget de performance : à arbitrer. |

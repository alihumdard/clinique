# Clinique Juridique FSJP-UAM
## Plateforme numérique — Livraison de la phase 2

**Adresse de consultation :** http://40.160.97.168:8080
**Date de mise en ligne :** 22 octobre 2026
**Statut :** version de démonstration, ouverte à la relecture

---

## 1. Avant de commencer

Trois points à connaître avant d'ouvrir les pages.

**Le navigateur affichera « Non sécurisé ».** Le site est servi en HTTP sur une
adresse IP ; un certificat HTTPS ne peut être délivré que pour un nom de
domaine. C'est sans conséquence pour une relecture, mais le domaine et le
certificat seront nécessaires avant toute mise en service réelle.

**Les contenus sont fictifs.** Noms, dates, chiffres, photographies et
documents sont des exemples destinés à juger la mise en page. Les contenus
réels de la Clinique seront intégrés en phase 4.

**Les formulaires n'enregistrent rien encore.** Le formulaire de contact, les
écrans d'administration, les filtres et la recherche s'affichent et répondent,
mais aucune donnée n'est enregistrée : la partie serveur du module 2 reste à
développer.

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
| Détail d'une actualité | http://40.160.97.168:8080/actualites/conference-acces-droit-personnes-vulnerables |
| Activités et caravanes juridiques | http://40.160.97.168:8080/activites |
| Galerie photos | http://40.160.97.168:8080/galerie |

### Ressources

| Page | Adresse |
| --- | --- |
| Fiches pratiques juridiques | http://40.160.97.168:8080/ressources/fiches-pratiques |
| Détail d'une fiche pratique | http://40.160.97.168:8080/ressources/fiches-pratiques/droits-de-la-famille |
| Rapports annuels d'activité | http://40.160.97.168:8080/ressources/rapports-annuels |

### Web TV

| Page | Adresse |
| --- | --- |
| Web TV | http://40.160.97.168:8080/web-tv |
| Détail d'une capsule | http://40.160.97.168:8080/web-tv/caravane-juridique-tillaberi |

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

## 3. Espace d'administration — 12 écrans

L'accès est protégé : toute adresse sous `/admin` renvoie vers la page de
connexion tant que la session n'est pas ouverte.

### Identifiants de démonstration

```
Adresse    http://40.160.97.168:8080/admin/login
E-mail     admin@clinique-fsjp.ne
Mot de passe   lgeZXO76xBUk2FV4GAqT!Aa9
```

Ce compte est fictif et réservé à la relecture. Les comptes réels seront créés
en phase 4, avec un mot de passe d'au moins douze caractères renouvelé à la
première connexion.

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
| Ajouter une page | http://40.160.97.168:8080/admin/pages/nouvelle |
| Catégories de pages | http://40.160.97.168:8080/admin/pages/categories |
| Activités et galerie | http://40.160.97.168:8080/admin/activites |
| Ajouter une activité | http://40.160.97.168:8080/admin/activites/nouvelle |
| Gestion de l'équipe | http://40.160.97.168:8080/admin/equipe |
| Gestion des partenaires | http://40.160.97.168:8080/admin/partenaires |
| Gestion de la newsletter | http://40.160.97.168:8080/admin/newsletter |
| Paramètres du site | http://40.160.97.168:8080/admin/parametres |

---

## 4. Ce qui fonctionne, et ce qui reste à développer

### Fonctionne

- Les 24 pages publiques et les 12 écrans d'administration s'affichent.
- La navigation, le fil d'Ariane et l'affichage mobile.
- La connexion à l'administration, avec limitation des tentatives et
  déconnexion automatique après trente minutes d'inactivité.
- Les lecteurs vidéo ne se chargent qu'au clic : rien n'est demandé à la
  plateforme vidéo tant que le visiteur n'a pas appuyé sur lecture.
- Les onglets, les accordéons et les graphiques du tableau de bord
  fonctionnent sans JavaScript actif.

### Reste à développer — module 2

Les écrans d'administration sont dessinés ; la partie serveur ne l'est pas
encore. Concrètement, la Clinique ne peut pas encore ajouter, modifier ou
supprimer un contenu : les tableaux affichent des exemples inscrits dans les
pages, et les formulaires n'enregistrent rien.

Restent à produire : le schéma de base de données, les modèles, les
contrôleurs, les opérations de création et de modification, la gestion des
fichiers téléversés, et le branchement des pages publiques sur ces données.

### Reste à développer — module 3

La pré-saisine (filtre d'orientation, formulaire, récapitulatif, confirmation)
et le registre numérique des dossiers, qui constitue une application distincte
sur son propre sous-domaine, avec sa propre base de données et ses propres
comptes.

---

## 5. Points en attente de décision

| Point | Détail |
| --- | --- |
| Données marocaines dans les maquettes | La maquette de la page d'accueil porte « Université Al Akhawayn, Ifrane, Maroc », un numéro en +212 et des partenaires marocains. Les données du Niger ont été retenues ; les maquettes approuvées méritent une correction. |
| Hébergeur des mentions légales | La maquette indique OVHcloud, le plan prévoit l'infrastructure de l'Université. La mention porte « à confirmer » en attendant la réponse du service informatique. |
| Pages Cookies, Accessibilité, Crédits | Présentes dans la barre latérale des maquettes juridiques, absentes de l'inventaire des 24 pages. À construire ou à retirer. |
| Éditeur de texte enrichi | Les barres de mise en forme sont affichées mais inactives. Une bibliothèque d'édition pèse entre 45 et 200 Ko, ce qui s'oppose au budget de performance ; un champ en texte simple reste possible. |
| Treize états des dossiers | Leurs libellés exacts conditionnent le démarrage du module 3. Le plan demande explicitement de ne pas les inventer. |
| Matrice des droits | Les droits précis des quatre profils du registre, également nécessaires au module 3. |

---

## 6. Performance et sécurité

| Exigence du plan | Mesure |
| --- | --- |
| Page d'accueil sous 500 Ko | 118 Ko |
| Page intérieure sous 400 Ko | 40 à 135 Ko |
| Aucun script distant | Aucun : ni jQuery, ni police d'icônes, ni bibliothèque de graphiques |
| Lecteur vidéo au clic | Confirmé : aucune requête vers la plateforme vidéo avant le clic |
| Accessibilité AA | Contraste, navigation au clavier, textes de remplacement |

Côté sécurité : en-têtes de sécurité sur chaque réponse, limitation des
tentatives de connexion, déconnexion après inactivité, déconnexion par requête
POST uniquement, et administration exclue de l'indexation.

Le chiffrement HTTPS n'est pas actif sur cette version de démonstration, faute
de nom de domaine. Il sera nécessaire avant la mise en service.

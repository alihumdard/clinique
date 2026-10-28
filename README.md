# Clinique Juridique FSJP-UAM — plateforme numérique

Faculté des Sciences Juridiques et Politiques
Université Abdou Moumouni de Niamey

Trois composants dans une seule application Laravel, séparés là où il faut :

| | |
| --- | --- |
| **Site public** | 28 pages en français : présentation, services, actualités, activités, ressources, Web TV, contact, et le parcours de pré-saisine |
| **Administration (CMS)** | 20 écrans ; le contenu du site, tenu par la Clinique |
| **Registre numérique** | Les dossiers juridiques : sa propre base, ses propres comptes, son propre espace |

---

## Documentation

| Pour qui | Document |
| --- | --- |
| La Clinique | [Guide de l'administrateur](docs/GUIDE-ADMINISTRATEUR.md) |
| Le service informatique de l'UAM | [Documentation technique](docs/DOCUMENTATION-TECHNIQUE.md) |
| Le client, à la relecture | [Livraison du module 2](LIVRAISON.md) |

---

## Démarrer en local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# Site public
php artisan migrate --seed

# Registre : sa propre base, son propre chemin de migrations
php artisan migrate --database=registre --path=database/migrations/registre
php artisan db:seed --class=RegistreSeeder
php artisan db:seed --class=RegistreDossierSeeder

npm run build
php artisan serve
```

Les données semées sont **fictives**, comme le plan de développement l'exige
pendant le développement.

### Comptes de développement

| Espace | Adresse | Compte |
| --- | --- | --- |
| Administration | `/admin/login` | `adminclinique@gmail.com` · administrateur |
| | | `editeurclinique@gmail.com` · éditeur |
| Registre | `/registre/connexion` | `coordination@registre.test` et trois autres profils |

Les mots de passe sont dans `DatabaseSeeder` et `RegistreSeeder`. Ils ne valent
que pour le développement.

---

## Deux bases, et pourquoi

Le plan demande que « les données confidentielles des dossiers ne soient pas
stockées dans la base du site public ». Le registre a donc sa propre connexion,
pilotée par les variables `REGISTRE_DB_*`, et les pré-saisines y sont écrites
bien que ce soit le site public qui les reçoive.

Conséquence : aucune clé étrangère ne traverse les deux bases, et tout modèle du
registre porte le trait `OnRegistreConnection` pour qu'un modèle ajouté plus tard
ne retombe pas discrètement sur la base publique.

---

## Tests

```bash
php vendor/bin/phpunit
```

522 tests, sur SQLite en mémoire — avec une base distincte pour le registre,
afin qu'un test ne puisse pas démontrer la séparation contre une base unique.

---

## Déploiement

```bash
sudo bash scripts/deploy.sh
```

Le rechargement de php-fpm à la fin n'est pas optionnel : OPcache garde les vues
Blade compilées en mémoire partagée. Voir la documentation technique pour ce qui
reste à faire avant la mise en service du registre.

---

## État

| Module | |
| --- | --- |
| 1 — Architecture et fondations | Terminé |
| 2 — Site public, Web TV et CMS | Terminé |
| 3 — Pré-saisine et registre | Fonctionnel sur des statuts provisoires |
| 4 — Sécurité, recette, déploiement | En cours |

Ce qui reste attendu de la Clinique et de l'UAM est listé à la fin de la
[documentation technique](docs/DOCUMENTATION-TECHNIQUE.md).

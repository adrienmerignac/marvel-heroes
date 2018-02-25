# Marvel Heroes

## Informations
- Projet Symfony 4
- Compatible IE10+ (Bootstrap 4)

## Installation

### Pré-requis
- PHP 7+
- Composer

### Lancement de l'application

#### Récupération du projet
- `git clone https://github.com/adrienmerignac/marvel-heroes.git`

#### Marvel API
Dans le fichier de configuration `services.yaml` remplacer les paramètres :
- `api_key` par votre public key
- `private_key` par votre private key

#### Lancement du serveur
- `composer update`
- `php bin/console server:run`
- [http://localhost:8000](http://localhost:8000)

### Lancement des Tests Unitaires
`php bin/phpunit`

## Utilisation

L'application Marvel Heroes se découpe en 2 pages :
- Page principale
- Le détails d'un héros

### Page principale
La page se divise en 2 sections :
- A gauche : La liste des héros
- A droite : La liste des héros favoris

#### Liste des héros
- Il s'agit d'une liste paginée des héros contenant l'image et le nom de chacun.  
- La liste démarre volontairement page 6 (à partir du 100e personnage exclu) afin de respécter l'énnoncé.
- Le clic sur un héros amène vers sa page de détails.
- Les boutons suivants et précédents situés en dessous de la liste permettent de naviguer dans la pagination

#### Héros Favoris
- Il s'agit d'une liste des héros favoris contenus dans un cookie de l'application (si ce dernier est supprimé la liste redevient vide).
- Dans un soucis d'optimisation du stockage dans le cookie, seul le nom et l'identifiant du personnage sont sauvegardés.
- Le clic sur un héros amène vers sa page de détails.

### Détails d'un héros
La page de détails du héros se divise en 3 blocs :
- Le nom du personnage précédé de son image, ainsi qu'une étoile permettant d'ajouter le héros dans ses favoris (5 maximum). Si l'ajout est effectué avec succès, un message d'information apparait.
- La description du personnage si disponible
- Les 3 premiers comics du personnage triés par ordre de date de mise en vente.  
Le titre du bloc contient également le nombre total de comics dans lesquels il apparait.  
Le clic sur un comic ouvre un nouvel onglet vers sa page de détails officiel.
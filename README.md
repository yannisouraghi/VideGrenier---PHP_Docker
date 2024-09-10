# Vide Grenier en Ligne

Ce Readme.md est à destination des futurs repreneurs du site-web Vide Grenier en Ligne.

## Prérequis

- Docker engine > 27.2.1 ([https://docs.docker.com/engine/install/](https://docs.docker.com/engine/install/))

## Démarrage du projet en prod

1. Copy `.env.default` to `.env` and edit environment variables*


2. Lancez la commande

```bash
docker compose up
```

## Démarrage du projet en dev

1. Copy `.docker/dev/.env.default` to `.docker/dev/.env` and edit environment variables


2. Lancez la commande

```bash
docker compose -f .docker/dev/docker-compose.yml up -d
```

## Routing

Le [Router](Core/Router.php) traduit les URLs.

Les routes sont ajoutées via la méthode `add`.

En plus des **controllers** et **actions**, vous pouvez spécifier un paramètre comme pour la route suivante:

```php
$router->add('product/{id:\d+}', ['controller' => 'Product', 'action' => 'show']);
```

## Vues

Les vues sont rendues grâce à **Twig**.
Vous les retrouverez dans le dossier `App/Views`.

```php
View::renderTemplate('Home/index.html', [
    'name'    => 'Toto',
    'colours' => ['rouge', 'bleu', 'vert']
]);
```

## Models

Les modèles sont utilisés pour récupérer ou stocker des données dans l'application. Les modèles héritent de `Core
\Model
` et utilisent [PDO](http://php.net/manual/en/book.pdo.php) pour l'accès à la base de données.

```php
$db = static::getDB();
```

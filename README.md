# Xcl Table Tennis League

A web app to keep track of wins and losses in the Xcl Table Tennis League (and pretty much any other game)

## Remarks

- The test environment is configured NOT to commit transactions, which means that tests do not actually alter the test database.

## Development environment setup

This section describes how to set up a development environment from scratch.

### Homebrew

```
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### PHP

```
brew install php
```

### Composer

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

### Symfony

```
brew install symfony-cli/tap/symfony-cli
```

### Doctrine

```
composer require symfony/orm-pack
```

When asked "Do you want to include Docker configuration from recipes?", answer "n".

```
composer require --dev symfony/maker-bundle
composer require symfony/serializer-pack
```

### Test environment

```
composer require --dev symfony/test-pack
composer require --dev dama/doctrine-test-bundle
```

When asked "Do you want to execute this recipe?", answer "Y".

```
composer require api
composer require --dev symfony/http-client
```

### Useful commands

Create the database based on the settings in the `.env` file.

```
php bin/console doctrine:database:create
```

Create the database based on the settings in the `.env.test` file.

```
php bin/console --env=test doctrine:database:create
```

Create an entity.

```
php bin/console make:entity
```

Create the database schema.

```
php bin/console doctrine:schema:create
```

Create the test database schema.

```
php bin/console --env=test doctrine:schema:create
```

Create a migration.

```
php bin/console make:migration
```

Execute the migrations.

```
php bin/console doctrine:migrations:migrate
```

Create a controller.

```
php bin/console make:controller XXXXController
```

Execute the tests.

```
php bin/phpunit
```

Drop the database.

```
php bin/console doctrine:database:drop --force
```

Drop the test database.

```
php bin/console --env=test doctrine:database:drop --force
```

Start the Symfony server.

```
symfony server:start
```

## Getting started

Build the containers using the following command.

```
docker compose -p xttl build --no-cache
```

Start the containers using the following command.

```
docker compose -p xttl up -d --pull always --wait
```

You should now be able to connect to the xttl database hosted on the MySQL container using the following command.

````
docker exec -it xttl-database sh -c "mysql --user=root --password=xttl"
````

Eventually, you will be able to stop the containers using the following command.

```
docker compose -p xttl down
```


# Xcl Table Tennis League

A web app to keep track of wins and losses in the Xcl Table Tennis League (and pretty much any other game)

## Folder structure

The project is structured as follows.

| Folder                      | Description                                                                                |
|-----------------------------|--------------------------------------------------------------------------------------------|
| bend/src/Controller         | The controllers exposing a set of RESTful API consumed by the frontend.                    |
| bend/src/DataTransferObject | The classes used to serialize and deserialize data sent to and received from the frontend. |
| bend/src/Entity             | The classes abstracting the data entities.                                                 |
| bend/src/Repository         | The classes interacting with the database.                                                 |
| bend/tests/Controller       | The integration tests of the controllers.                                                  |

## Remarks

The test environment is configured NOT to commit transactions, which means that tests do not actually alter the test database.

## Getting started

This section describes how to set up the containers and the development environment.

### Containers

Build the containers using the following command.

```
docker compose -p xttl build --no-cache
```

Start the containers using the following command.

```
docker compose -p xttl up -d --pull always --wait
```

You should now be able to connect to MySQL using the following command.

````
docker exec -it xttl-database sh -c "mysql --user=root --password=xttl"
````

Eventually, you will be able to stop the containers using the following command.

```
docker compose -p xttl down
```

### Development environment setup

This section describes how to set up a development environment from scratch.

#### Homebrew

```
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

#### PHP

```
brew install php
```

#### Composer

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

#### Symfony

```
brew install symfony-cli/tap/symfony-cli
```

#### Database

Create the **development** database (based on the settings in the `.env` file), and execute the migrations.

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Drop the **development** database (based on the settings in the `.env` file), recreate it, and execute the migrations.

```
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Create the **test** database (based on the settings in the `.env.test` file), and execute the migrations.

```
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:migrations:migrate
```

Drop the **test** database (based on the settings in the `.env.test` file), recreate it, and execute the migrations.

```
php bin/console --env=test doctrine:database:drop --force
php bin/console --env=test doctrine:database:create
php bin/console --env=test doctrine:migrations:migrate
```

#### PHPUnit

Run all the tests.

```
php bin/phpunit
```

#### Integrate PHPUnit with IntelliJ IDEA

The following screenshot shows the configuration of PHPUnit in IntelliJ IDEA.

![Integrate PHPUnit with IntelliJ IDEA](images/Integrate PHPUnit with IntelliJ IDEA.png)

## How to

This section describes how to perform common tasks.

### Add a field to an entity

When adding a field to an entity:

- Add a parameter to the constructor of the related data transfer object class
- Initialize the field in the initFromData method of the entity class
- Create a migration and apply it
- Add integration tests as applicable


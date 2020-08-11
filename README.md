# Emmobilier:

Emmobilier is an application where authenticated users can upload images to get photoshoped with specified retouches for Real Estate purposes.

Emmobilier est une application où des utilisateurs authentifiés peuvent mettre des images afin de les modifiés avec des retouches spécifiques pour le commerce immobilier.

## Project Requirements:

In order to get the project running you need to install:

* php7.1(or highier)
* symfony3.4
* composer

#### Install Php7.1:

###### Step 1: Enable PPA

``` bash
sudo apt-get install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update

```

###### Step 2: Install php7.1 & php7 modules

``` bash
sudo apt-get install php7.1
```

You have to install other modules as well:(these are the most common)

``` bash
sudo apt-get install php7.1 php7.1-cli php7.1-common php7.1-json php7.1-opcache php7.1-mysql php7.1-mbstring php7.1-mcrypt php7.1-gd php7.1-zip php7.1-xml php7.1-fpm
```

If you need to install a specific module you can search for it:

``` bash
sudo apt-cache search php7.1
```

### Install Composer:

#### Step 1: Download the Composer executable

``` bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

#### Step 2: Install Composer globally

``` bash
mv composer.phar /usr/local/bin/composer
```

You need to be at the same directory to move the file.

## Setting the Project Locally:

#### Cloning the project:

Once you have all the needed requirements installed, clone the project from gitlab:

``` bash
git clone https://gitlab.com/simatai/Emmobilier-V2.git
```

#### Install the Symfony dependencies:

Now that the database connection is installed, running the following command should install all the symfony dependencies:

``` bash
composer install
```

#### Configure the database:

Before you can run the project you need to connect to a database, make sure that the needed driver is installed:

``` bash
apt install php7.1-mysql // for mysql
apt install php7.1-pgsql // or for postgres
```

After that, you need to change the config file __config/packages/doctrine.yaml__ to match your database name etc.:

``` yaml
doctrine:
    dbal:
        # configure these for your database server
        driver: pdo_mysql
        #driver: pdo_sqlite

        # With Symfony 3.3, remove the `resolve:` prefix
        url: pdo_driver://username:password@localhost:port/db_name
        #url: '%env(resolve:DATABASE_URL)%'
...
```

If you want to use mysql set pdo_driver = mysql and port = 3306.
If you want to use postgreSql set pdo_driver = pgsql and port = 5432.

The commented lines are an example to access an sqlite database.

Now we need to migrate the database:

``` bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

If you get the following error:

``` bash
[Doctrine\DBAL\DBALException]
Unknown database type enum requested, Doctrine\DBAL\Platforms\MySqlPlatform may not support it.
```

You should add the following to __doctrine.yaml__ (pay attention to the indent):

``` yaml
...
    dbal:
    ...
        mapping_types:
            enum: string
...
```

#### Run the Project:

to run the project type:

``` bash
php bin/console server:run
```

Check 127.0.0.1:8000 on your browser!

That's it.

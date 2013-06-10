# FluxBB 1.5 External Authentication

A package that allows for external authentication with FluxBB 1.5, integrating
directly with the Laravel 4 framework.

## Installation

### Step 1: Install package through Composer

Add this line to the `require` section of your `composer.json`:

    "franzliedke/auth-fluxbb": "1.0.*"

Alternately, you can use the Composer command-line tool by running this command:

    composer require franzliedke/auth-fluxbb

Next, run `composer install` to actually install the package.
(I will keep the version number up-to-date as we release new versions of FluxBB.)

### Step 2: Register the service provider

In your Laravel application, edit the `app/config/app.php` file and add this
line to the `providers` array:

    'FranzLiedke\AuthFluxBB\AuthFluxBBServiceProvider',

### Step 3: Configure the location of your FluxBB installation

In order to read some configuration values, the path to your FluxBB installation
needs to be configured.

To copy the package configuration file, run this command:

    php artisan config:publish franzliedke/auth-fluxbb

You can then edit `app/config/packages/franzliedke/auth-fluxbb/fluxbb.php`.
Change the `path` option to point to the root directory of your FluxBB
installation. Make sure it ends with a slash.

### Step 4: Enable the new authentication adapter

In your application, edit the `app/config/auth.php` file and set the `driver`
option to "fluxbb", so that it looks like this:

    'driver' => 'fluxbb',

## Usage

Once installed, you can use the authentication feature of Laravel as you always
do, with Laravel magically using FluxBB's database and cookie behind the scenes.

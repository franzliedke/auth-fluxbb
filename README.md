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

### Step 2: Register the service provider

In your Laravel application, edit the `app/config/app.php` file and add this
line to the `providers` array:

    'FranzLiedke\AuthFluxBB\AuthFluxBBServiceProvider',

### Step 3: Configure the location of your FluxBB installation

In order to read some configuration values, the path to your FluxBB installation
needs to be configured.

To copy the package configuration file, run this command:

    php artisan config:publish franzliedke/auth-fluxbb

You can then edit `app/config/packages/franzliedke/auth-fluxbb/config.php`.
Change the `path` option to point to the root directory of your FluxBB
installation. Make sure it ends with a slash.

### Step 4: Enable the new authentication adapter

In your application, edit the `app/config/auth.php` file and set the `driver`
option to "fluxbb1", so that it looks like this:

    'driver' => 'fluxbb1',

### Step 5 (optional): Set up views for resetting passwords

The reset system that comes with Laravel works almost out of the box. Just change the reset callback function so that `sha1` is used rather than `Hash::make`, and use a DB query rather than Eloquent's `save`.

In case you want the same process as in FluxBB behind the scenes (not using a separate table for reset tokens, and generating a new password rather than letting the user choose), bind `FluxReminderRepository` as `'auth.reminder.repository'` in a service provider. Do note that this requires extra work, since you'll need to tweak the controller (only one POST method will be used).

## Usage

Once installed, you can use the authentication feature of Laravel as you always
do, with Laravel magically using FluxBB's database and cookie behind the scenes.

**Note**: This package will not work if your FluxBB installation uses a SQLite2 database, as this database type is not supported by Laravel.

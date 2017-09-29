<p align="center"><img src="http://finetunecms.co.uk/identity.svg"></p>

## Finetune Content Management System

[![Latest Stable Version](https://poser.pugx.org/finetune/finetune/v/stable)](https://packagist.org/packages/finetune/finetune)
[![Total Downloads](https://poser.pugx.org/finetune/finetune/downloads)](https://packagist.org/packages/finetune/finetune)
[![Latest Unstable Version](https://poser.pugx.org/finetune/finetune/v/unstable)](https://packagist.org/packages/finetune/finetune)
[![License](https://poser.pugx.org/finetune/finetune/license)](https://packagist.org/packages/finetune/finetune)
[![Monthly Downloads](https://poser.pugx.org/finetune/finetune/d/monthly)](https://packagist.org/packages/finetune/finetune)
[![Daily Downloads](https://poser.pugx.org/finetune/finetune/d/daily)](https://packagist.org/packages/finetune/finetune)


Laravel Installation

This is a laravel plugin for laravel 5.4 or greater, to install laravel follow this guide https://laravel.com/docs/5.4/installation.

To quickly install laravel, use this command

composer create-project --prefer-dist laravel/laravel .

This creates a default laravel installation in the folder your in, it presumes you have composer, if you don't follow the laravel installation guide linked above.

Install Finetune

Install Finetune via composer is the easiest way :

composer require finetune/finetune:dev-master

There are different releases to choose from check the github page for more information on releases.

Once installed you need to add the finetune service provider and the route service provider, the route service provider always needs to below the finetune service provider and any finetune plugins, but above the laravel AppServiceProvider. This allows the finetune routes to take precedence over the laravel routes file. 

Finetune\Finetune\FinetuneServiceProvider::class,
Finetune\Finetune\FinetuneRoutesServiceProvider::class,


Once these are installed you need to run some artisan commands, this will publish all the config, views and migrations you need to use finetune. 

php artisan vendor:publish --force

You need to overwrite the files already present so we use the force tag to insure this, if you have made any config apart from the config/app.php then you will need to add it again.

Installing the database

As this uses laravel any database that laravel supports finetune supports to, you configure your database in the same way as laravel, you can follow the laravel install guide (https://laravel.com/docs/5.5/database) or follow these steps (in this example we are using mysql with the username and database name of finetune)

1) Login to mysql via a command line : mysql -u finetune -p

2) Create database : CREATE DATABASE finetune;

3) Exit from mysql terminal : exit;

Then configure your environment settings in the .env file, this is found in your root project area where you install laravel.

There will be settings that look like this

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret

You need to configure these settings for your database settings, for the user and database above it would be:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finetune
DB_USERNAME=finetune
DB_PASSWORD=secret

Once thats complete it's time to make a session table using laravels migration and then run the migrations 

php artisan session:table
php artisan migrate

This will then add all the tables to the database you need, now its time to run the install script.

Install script

The final stage, this is where we add our superadmin user and the first sites domain, we will be using http:://www.finetunecms.co.uk for this example, you should just replace the information with your own.

In the Command line interface type

php artisan finetune:install

This will start the install script, will seed the database with some default content to help you on your way to create your first website.

It will ask a few questions the first bit is about your superadmin user, the following is the questions it asks and the data we used to create superadmin

Username 
> Superadmin

Firstname 
> Super

Lastname 
> admin

Email 
> Superadmin@admin.com

Password 
> secret


This will now add your superadmin to the database to allow you to login with finetune.

The next part of the setup script will create your first site

Title of the new site 
> FientuneCMS

The dscpn of the new site 
>FinetuneCMS allows you to install a user friendly cms on top of laravel

The tag of the new site 
>finetune

Company Name 
>finetune

Company Person 
> Christopher Thompson

Company Email 
> chris@finetunecms.co.uk

Company Street 
> 123 Somewhere road

Company Town 
> Finetune Town

Company Postcode 
> 12345

Company Tel 
> 000000

Company Region 
> United Kingdom

This information is required at this time but can be changed later, it doesn't have much validation on it so you can input any string.

Once this is complete it will make the uploads folder with the site tag in your storage area of the laravel application, this will store all your images and files that need to be used.

We now need to add permissions for laravel's bootstrap and the storage directory, I would recommend these on *nix OS's

sudo chgrp -R www-data storage bootstrap/cache 

sudo chmod -R ug+rwx storage bootstrap/cache

Web server

Now you have completed the finetune install you need to setup a webserver, you will need to configure your webserver with certain cetains to get pretty urls, follow the laravel guide here https://laravel.com/docs/5.5/installation#web-server-configuration






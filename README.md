# ![Laravel Asteroid Neo Stats App]

# Getting started

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.4/installation#installation)

Clone the repository

    git clone git@github.com:AnkitaPatil9200/laravel-vue-asteroid-neo-stats.git

Switch to the repo folder

    cd laravel-vue-asteroid-neo-stats

Install all the dependencies using composer

    composer install

Run below commands to install frontend dependancies

    npm install
    npm run dev

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

**command list**

    git clone git@github.com:AnkitaPatil9200/laravel-asteroid-neo-stats.git
    cd laravel-asteroid-neo-stats
    composer install
    npm install
    npm run dev
    cp .env.example .env
    php artisan key:generate

## Installation of npm Vue.js and Vue Loader

Run below command

    npm install vue@next vue-loader@next

Install Vue.js plugin for Vite

    npm i @vitejs/plugin-vue

## API Specification

Create your own API KEY from Nasa's website https://api.nasa.gov in order to be able to test with below rate limits.

    Hourly Limit: 1,000 requests per hour

If you do not create API KEY, DEMO_KEY will be used as API KEY which has below rate limits.

    Hourly Limit: 30 requests per IP address per hour
    Daily Limit: 50 requests per IP address per day

Once you create API KEY, assign the new key to API_KEY environment variable in .env file 
(Refer .env.example file)

# Code overview

## Files

- `routes/web.php` - Contains routes
- `app/Http/Controllers/HomeController.php` - Contains all the backend functions
- `app/Helper/HomeHelper.php` - Contains cURL request
- `resources/views/app.blade.php` - Laravel App Blade file
- `resources/js/App.vue` - Vue.js Main App file
- `resources/js/app.js` - Vue.js Main JS file
- `resources/css/app.css` - Vue.js Main CSS file
- `resources/js/Layouts/TheHeader.vue` - Contains page header
- `resources/js/Pages/TheForm.vue` - Contains main UI and JS code of the app
- `tailwind.config.js` - Tailwind configuration file
- `vite.config.js` - Vite configuration file

## Environment variables

- `.env` - Environment variables can be set in this file
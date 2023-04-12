# MailerLite Subsciber Management Laravel App 

## Intro

Utilizes the MailerLitePHP Library and Laravel to give you an easy way to manage your subscribers in your own wweb app.

## Requirements

### Laravel requirements:
PHP >= 7.4
Laravel 8.x
Composer

### PHP Requirements:
PHP >= 7.4

### Database Requirements:
MySQL >= 5.7

## Installation

Clone the repository
```
git clone https://github.com/hernandezharries08/sub_management_mailerlite.git
```

Install dependencies:
```
composer install
npm install
```

Generate an application key:
```
php artisan key:generate
```

Set your MailerLite API key in the .env file: (only needed if you are running tests)
```
MAILERLITE_API_KEY=your-api-key
```

Set up your database in the .env file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=example
DB_USERNAME=root
DB_PASSWORD=
```

Run the database migrations:
```
php artisan migrate
```

Start the development server:
```
php artisan serve
```

Access the application in your browser at http://localhost:8000.

To run the PHPUnit tests, use the following command:
```
php artisan test
```

That's it! You should now have the project set up and running on your local environment.
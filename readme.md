

## Project Setup

### Clone the repository
```
$ git clone https://github.com/omedale/shop-api-laravel.git
```

### Switch to the repo folder
```
$ cd shop-api-laravel
```

### Install all the dependencies using composer
```
$ composer install
```

### Copy the example env file and make the required configuration changes in the .env file
```
$ cp .env.example .env
$ cp .env.example .env.testing
```

### Environment variables

- `.env` - Environment variables can be set in this file for development
- `.env.testing` - Environment variables can be set in this file for testting

### Database Setup
```
$ mysql -u root

> create database dev
> create database test
> exit

$ cd database/db
$ mysql -u root dev  < tshirtshop.sql
$ mysql -u root test  < tshirtshop.sql


$ php artisan migrate
```

### Generate a new application key
```
$ php artisan key:generate
```

### Generate a new JWT authentication secret key
```
$ php artisan jwt:generate
```

### Run your tests
```
$ composer test
```

### Testing API

Run the laravel development server
```
$ php artisan serve
```

# Authentication
 
This applications uses JSON Web Token (JWT) to handle authentication. The token is passed with each request using the `API-KEY` header with `Bearer token` scheme. The JWT authentication middleware handles the validation and authentication of the token.

## Vacation Module

Laravel API for creating and managing requests for vacations.

## Install

requires: php 8+, mysql 8, composer, git

- git clone git@github.com:DimitrijeD/vacation_module.git
- cd vacation_module
- composer install
- cp .env.example .env
    - edit .env file by setting database name, connection.. and URL
        - APP_URL
        - DB_CONNECTION
        - DB_HOST
        - DB_PORT
        - DB_DATABASE
        - DB_USERNAME
        - DB_PASSWORD
- create database with same name (DB_DATABASE)
- php artisan migrate
- php artisan test

If tests pass everything is setup correctly.

The tests in the "/tests/Feature/" directory define several use cases for the API. 
Each test creates a new database state by refreshing the database. 
This ensures that the tests are independent and can predictably test the API's behavior.

## Flow

- The user registers an account and logs in. 
- They can then use the token received from either of these two responses to request a vacation for a desired period. 
- While a vacation request is pending, the user can update it by setting new dates for the vacation period, as long as they pass validation. 
- Another user with the role of manager can then approve or reject the request. 
- The user can always see the current status of their pending vacation requests, as well as the number of vacation days they have available.

## License

vacation-module is licensed under the [MIT license](https://opensource.org/licenses/MIT).

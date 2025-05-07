# Commission Fee Calculator

This application calculates commission fees for financial operations based on defined business rules.

## Small Laravel quirks & project general info
All logic related to commands in Laravel is stored in app\Console\Commands

All Models(entities) in Laravel are stored in app\Models 

All logic is served through services stored in app\Services, those are also commonly known as plugins for example in other frameworks

Inside ExchangeRate service isTestingEnvironment() uses environment('testing'): by default APP_ENV is taken from .env file APP_ENV=local, but the value of "testing" for unit tests is overriden and taken from phpunit.xml settings

## Configuration

All business rules related constants and settings are configured in `config/variables.php`:
e.g.
- Commission rates
- Free withdrawal limits
- Currency settings
- Exchange rate configurations

## Requirements

- PHP 8.0 or higher
- Composer
- Laravel framework

## Installation
Install dependencies:

```bash
composer install
```

## Usage

### Running the Application

The application reads operations from a CSV file and calculates commission fees for each operation.

To run the application:

```bash
php artisan commission:calculate input.csv
```

Where `input.csv` is a file in the root directory containing operations in the following format:
```
operation_date,user_id,user_type,operation_type,amount,currency
```

Example:
```
2016-01-05,1,private,deposit,200.00,EUR
```

### Running Tests

You can run tests with:

```bash
php artisan test --testsuite=Unit --stop-on-failure
```

Or:

```bash
./vendor/bin/phpunit tests/Unit/CommissionCalculatorTest.php
```

## Special Mentions

- JPY currency is displayed without decimal places (e.g., 100 instead of 100.00)
- All other currencies are displayed with 2 decimal places

## 3rd party API Integration for exchange rate

The application connects to an exchange rate API to convert between currencies:

To use the alternative API endpoint, add your API endpoint to the config file - using an api key by adding it to `config/variables.php` is also possible.


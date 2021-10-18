# Commission Manager app

## Situation
A Company allows private and business clients to deposit and withdraw funds to and from company accounts in multiple currencies. Clients may be charged a commission fee.

You have to create an application that handles operations provided in CSV format and calculates a commission fee based on defined rules.

## Commission fee calculation

Commission fee is always calculated in the currency of the operation. For example, if you withdraw or deposit in US dollars then commission fee is also in US dollars.
Commission fees are rounded up to currency's decimal places. For example, 0.023 EUR should be rounded up to 0.03 EUR.

### Deposit rule
All deposits are charged 0.03% of deposit amount.

### Withdraw rules
There are different calculation rules for withdraw of private and business clients.

### Private Clients
Commission fee - 0.3% from withdrawn amount.

1000.00 EUR for a week (from Monday to Sunday) is free of charge. Only for the first 3 withdraw operations per a week. 4th and the following operations are calculated by using the rule above (0.3%). If total free of charge amount is exceeded them commission is calculated only for the exceeded amount (i.e. up to 1000.00 EUR no commission fee is applied).
For the second rule you will need to convert operation amount if it's not in Euros. Please use rates provided by api.exchangeratesapi.io.

### Business Clients
Commission fee - 0.5% from withdrawn amount.

## Input data
Operations are given in a CSV file. In each line of the file the following data is provided:

operation date in format Y-m-d
user's identificator, number
user's type, one of private or business
operation type, one of deposit or withdraw
operation amount (for example 2.12 or 3)
operation currency, one of EUR, USD, JPY

## Expected result
Output of calculated commission fees for each operation.

In each output line only final calculated commission fee for a specific operation must be provided without currency.



## Installation ##

### Server Requirements

The Laravel framework has a few system requirements. All of these requirements are satisfied by the Laravel Homestead virtual machine, so it's highly recommended that you use Homestead as your local Laravel development environment.

However, if you are not using Homestead, you will need to make sure your server meets the following requirements:

- PHP >= 7.2.5
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

### Setup

Please run composer update for setup the application.

`composer update`

### Directory Permissions
After installing Laravel, you may need to configure some permissions.
Directories within the `storage` and the `bootstrap/cache` directories should be
writable by your web server or Laravel will not run. If you are using
the Homestead virtual machine, these permissions should already be set.


## Running application ##

To run this application go to the root directory of the project 
and run the command in the command line:

`php artisan calculate:commission data.csv`

In this command *data.csv* csv file will be used to run the app.

Feel free to modify `public/input/data.csv` or use alternative input file to test different transaction sequences 
and different scenarios.

## Configuration
If you want to update the configuration or change any rules value as, like deposit percentage, private withdraw
limit or add a new currency you can easily do it by changing the configuration value from our configuration file.
For doing it please open the `config/commissionSetup.php` file and set your value.

Please note that, if you change any value you have to run the following command.Without that the change wont have any effect.

`php artisan config:cache`

## Running tests

Run the following command from terminal to run the full feature test:

`php artisan test`

or

`vendor/bin/phpunit`


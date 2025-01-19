A web application to manage your bank account online! (( Run composer install when you retrieve the files to create the missing ones as well as composer require webapp ))

To create the database, after downloading the files, make a copy of the .env file and rename this copy to .env.local. Then, comment out line 29 and uncomment line 27, replacing the information with your MySQL details. Create a folder called migrations, then run the command symfony console doctrine:database:create. Next, run the command php bin/console make:migration, and finally, run the command php bin/console doctrine:migration:migrate. The database is now ready to be used.

The goal of BankShop is to allow clients to check their account, make withdrawals, deposits, and transfers in an intuitive and secure environment. Each person can open up to 5 accounts (savings or current) while benefiting from maximum security ensuring the well-being of the clients.

Prerequisites: Unit Tests (please enable the mbstring extension in your php.ini and remove the semicolon, then run this command at the root of the project: php bin/phpunit tests/Controller/BaseTemplateTest.php and adjust with the test names present in this folder)

Our main features are:

Checking deposits and withdrawals from your account

Checking users and transactions for admins

Secure registration and login

Backend: Symfony (PHP) Database: Frontend: Twig

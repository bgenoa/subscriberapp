# Subscriber Management

- REST API for managing two resources Subscriber and Field.  Subscriber has a one-to-many relationship with Field.
- Vue.js and Vuetify front-end that allows authenticated users to manage subscribers and fields.

## Installation and Setup

1. Clone the repository into your local development environment

```bash
git clone https://github.com/bgenoa/subscriberapp.git
```

2. Install the composer and npm dependencies

```bash
composer install
```
```bash
npm install
```

3. Set up your vhost for the application.  Example:

```
<VirtualHost *:80>
  DocumentRoot "c:/pathtoapplication/public"
  ServerName subscriberapp.localhost
  <Directory "c:/pathtoapplication/public">
    Options FollowSymLinks MultiViews
    AllowOverride All
    Order allow,deny
    Allow from all
    Require all granted
  </Directory>
</VirtualHost>
```

4. Copy .env.example to .env:

```bash
cp .env.example .env
```

5. Run the following:

```bash
php artisan key:generate
```

6. Set up a local mysql database and add the database details to your .env file.

7. Run migrations

```bash
php artisan migrate
```

8. Install Passport for authentication

```bash
php artisan passport:install
```

9. Add the Password grant client id and secret to your env file:

```
PASSPORT_CLIENT_ID=2
PASSPORT_CLIENT_SECRET=secretgoeshere
```

10. Update your .env with your application URL from step 3:

```
APP_URL=http://susbcriberapp.localhost
```

11. Seed your database.  In addition to Subscriber and Field records, this will create a test user (admin@test.com):

```bash
php artisan db:seed
```

12. You're all set.  Use the admin@test.com user to test the APIs through Postman or to log into the application in your browser.

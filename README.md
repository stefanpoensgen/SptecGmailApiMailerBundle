# [WIP] Gmail Api Mailer Bundle

Symfony Mailer Transport for Gmail API including Google OAuth2 authentication.

## Requirements
* Symfony >= 5.4

## Installation

#### Step 1: Download the Bundle
Use [Composer](http://getcomposer.org/) to install this bundle:

```
composer require sptec/gmail-api-mailer-bundle
```

#### Step 2: Enable the Bundle
```php
// config/bundles.php

return [
    // ...
    Sptec\GmailApiMailerBundle\SptecGmailApiMailerBundle => ['all' => true],
];
```

## Configuration
```yaml
sptec_gmail_api_mailer:
  client_id: <your-client-id>
  client_secret: <your-client-secret>
```

### .env
```dotenv
###> symfony/mailer ###
MAILER_DSN=gmail+api://null
###< symfony/mailer ###
```

## Usage
```
bin/console sptec:google:auth
```
Your Google access token will be stored as json environment variable `GOOGLE_ACCESS_TOKEN` 
by using [Symfony's secrets management system](https://symfony.com/doc/current/configuration/secrets.html).

## Google Credentials
1. Open the [Google Cloud console](https://console.cloud.google.com/).
2. At the top-left, click Menu menu > APIs & Services > Credentials.
3. Click Create Credentials > OAuth client ID.
4. Click Application type > Web application.
5. Enter a name for the OAuth client ID. (e.g. Symfony Gmail API Mailer)
6. Add authorized redirect URIs. (default: http://localhost)
7. Click Create.
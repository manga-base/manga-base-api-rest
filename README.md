# Manga Base RESTful API

Per posar en funcionament el servidor de Manga Base simplement s'ha de descarregar aquest repositori, executar la comanda `composer install`, crear l'arxiu `settings.php` dins el directori `/app/config/`, amb el següent contingut:
```php
<?php
return [
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => '[ip del host de la base de dades]',
            'database' => '[nom de la base de dades]',
            'username' => "[nom de l'usuari amb permisos a la base de dades]",
            'password' => "[contrasenya de l'usuari]",
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ],
        'jwt' => [
            'secret' => '[codi secret alearori]'
        ]
    ],
];

```
I finalment s'han de crear els directoris següents:

/public/**upload**

/public/upload/**images**

/public/upload/images/**avatars**

/public/upload/images/**banners**

/public/upload/images/**manga**


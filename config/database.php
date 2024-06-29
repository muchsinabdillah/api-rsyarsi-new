<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlsrv'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'DESKTOP-PHVSNUJ'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
        # 1st Database - MasterData
        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],

        # 2nd Database - Apotik_V1.1SQL
        'sqlsrv2' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_2', '127.0.0.1'),
            'port' => env('DB_PORT_2', '1433'),
            'database' => env('DB_DATABASE_2', 'forge'),
            'username' => env('DB_USERNAME_2', 'forge'),
            'password' => env('DB_PASSWORD_2', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],

        # 3rd Database - PerawatanSQL
        'sqlsrv3' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_3', '127.0.0.1'),
            'port' => env('DB_PORT_3', '1433'),
            'database' => env('DB_DATABASE_3', 'forge'),
            'username' => env('DB_USERNAME_3', 'forge'),
            'password' => env('DB_PASSWORD_3', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],

        # 4rd Database - Keuangan
        'sqlsrv4' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_4', '127.0.0.1'),
            'port' => env('DB_PORT_4', '1433'),
            'database' => env('DB_DATABASE_4', 'forge'),
            'username' => env('DB_USERNAME_4', 'forge'),
            'password' => env('DB_PASSWORD_4', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],
        # 5rd Database - Keuangan
        'sqlsrv5' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_5', '127.0.0.1'),
            'port' => env('DB_PORT_5', '1433'),
            'database' => env('DB_DATABASE_5', 'forge'),
            'username' => env('DB_USERNAME_5', 'forge'),
            'password' => env('DB_PASSWORD_5', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],
        # 6rd Database - DashboardData
        'sqlsrv6' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_6', '127.0.0.1'),
            'port' => env('DB_PORT_6', '1433'),
            'database' => env('DB_DATABASE_6', 'forge'),
            'username' => env('DB_USERNAME_6', 'forge'),
            'password' => env('DB_PASSWORD_6', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],

         # 7rd Database - LaboratoriumSQL
         'sqlsrv7' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_7', '127.0.0.1'),
            'port' => env('DB_PORT_7', '1433'),
            'database' => env('DB_DATABASE_7', 'forge'),
            'username' => env('DB_USERNAME_7', 'forge'),
            'password' => env('DB_PASSWORD_7', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],
         # 8rd Database - RadiologiSQL
         'sqlsrv8' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_8', '127.0.0.1'),
            'port' => env('DB_PORT_8', '1433'),
            'database' => env('DB_DATABASE_8', 'forge'),
            'username' => env('DB_USERNAME_8', 'forge'),
            'password' => env('DB_PASSWORD_8', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],
         # 6rd Database - RawatInapSQL
         'sqlsrv9' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_9', '127.0.0.1'),
            'port' => env('DB_PORT_9', '1433'),
            'database' => env('DB_DATABASE_9', 'forge'),
            'username' => env('DB_USERNAME_9', 'forge'),
            'password' => env('DB_PASSWORD_9', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],
        'sqlsrv10' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_10', '127.0.0.1'),
            'port' => env('DB_PORT_10', '1433'),
            'database' => env('DB_DATABASE_10', 'forge'),
            'username' => env('DB_USERNAME_10', 'forge'),
            'password' => env('DB_PASSWORD_10', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],
        'sqlsrv11' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_11', '127.0.0.1'),
            'port' => env('DB_PORT_11', '1433'),
            'database' => env('DB_DATABASE_11', 'forge'),
            'username' => env('DB_USERNAME_11', 'forge'),
            'password' => env('DB_PASSWORD_11', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],
        'Syslog' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_Syslog', '127.0.0.1'),
            'port' => env('DB_PORT_Syslog', '1433'),
            'database' => env('DB_DATABASE_Syslog', 'forge'),
            'username' => env('DB_USERNAME_Syslog', 'forge'),
            'password' => env('DB_PASSWORD_Syslog', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],
        'Apotik_V2' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST_Apotik_V2', '127.0.0.1'),
            'port' => env('DB_PORT_Apotik_V2', '1433'),
            'database' => env('DB_DATABASE_Apotik_V2', 'forge'),
            'username' => env('DB_USERNAME_Apotik_V2', 'forge'),
            'password' => env('DB_PASSWORD_Apotik_V2', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'encrypt' => 'yes',
            'trust_server_certificate' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];

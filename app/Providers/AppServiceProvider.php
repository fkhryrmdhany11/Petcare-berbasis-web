<?php

namespace App\Providers;

use App\Database\OracleConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use PDO;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        DB::extend('oracle', function (array $config, string $name) {
            $config['name'] = $name;

            $pdoFactory = function () use ($config) {
                $host = $config['host'] ?? '127.0.0.1';
                $port = $config['port'] ?? '1522';
                $service = $config['service_name'] ?? $config['database'] ?? 'XE';
                $charset = $config['charset'] ?? 'AL32UTF8';

                $dsn = "oci:dbname=//{$host}:{$port}/{$service};charset={$charset}";
                $pdo = new PDO(
                    $dsn,
                    $config['username'] ?? 'PET_CARE',
                    $config['password'] ?? 'pet_care',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_CASE => PDO::CASE_LOWER,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                    ]
                );

                // Ensure standard ISO date/timestamp formatting in Oracle session for Eloquent / Carbon
                $pdo->exec("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
                $pdo->exec("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
                $pdo->exec("ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '.,'");

                return $pdo;
            };

            return new OracleConnection(
                $pdoFactory,
                $config['database'] ?? 'XE',
                $config['prefix'] ?? '',
                $config
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

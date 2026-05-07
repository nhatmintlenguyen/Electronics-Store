<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'electronics_store';

const SITE_NAME = 'Electronics Store';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getDBConnection(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    try {
        $connection = new PDO(
            'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        return $connection;
    } catch (PDOException $exception) {
        http_response_code(500);
        exit('Database connection failed: ' . $exception->getMessage());
    }
}

function appBaseUrl(): string
{
    static $baseUrl = null;

    if ($baseUrl !== null) {
        return $baseUrl;
    }

    $scriptFilename = isset($_SERVER['SCRIPT_FILENAME']) ? realpath((string) $_SERVER['SCRIPT_FILENAME']) : false;
    $publicPath = realpath(PUBLIC_PATH);
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $scriptDir = str_replace('\\', '/', dirname($scriptName));

    if (!$scriptFilename || !$publicPath) {
        $baseUrl = rtrim($scriptDir, '/');
        return $baseUrl === '/' ? '' : $baseUrl;
    }

    $relativeDirectory = trim(
        str_replace(
            str_replace('\\', '/', $publicPath),
            '',
            str_replace('\\', '/', dirname($scriptFilename))
        ),
        '/'
    );

    $baseUrl = rtrim($scriptDir, '/');

    if ($relativeDirectory !== '') {
        $suffix = '/' . $relativeDirectory;
        if (str_ends_with($baseUrl, $suffix)) {
            $baseUrl = substr($baseUrl, 0, -strlen($suffix));
        }
    }

    if ($baseUrl === '/' || $baseUrl === '.') {
        $baseUrl = '';
    }

    return $baseUrl;
}

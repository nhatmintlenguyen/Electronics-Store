<?php
declare(strict_types=1);

use App\Core\Database;

const SITE_NAME = 'Electronics Store';

if (session_status() === PHP_SESSION_NONE) {
    if (defined('STORAGE_PATH')) {
        $sessionPath = STORAGE_PATH . '/sessions';
        if (is_dir($sessionPath) && is_writable($sessionPath)) {
            session_save_path($sessionPath);
        }
    }

    session_start();
}

function getDBConnection(): PDO
{
    return Database::connection();
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

    if (str_ends_with($scriptDir, '/public')) {
        $scriptDir = substr($scriptDir, 0, -strlen('/public'));
    }

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

<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

abstract class Model
{
    protected function db(): PDO
    {
        return Database::connection();
    }
}

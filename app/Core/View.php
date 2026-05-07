<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main.php'): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require VIEW_PATH . '/' . ltrim($template, '/');
        $content = (string) ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        require VIEW_PATH . '/' . ltrim($layout, '/');
    }
}

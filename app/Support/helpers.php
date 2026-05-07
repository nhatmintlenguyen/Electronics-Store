<?php
declare(strict_types=1);

function url(string $path = ''): string
{
    $path = ltrim($path, '/');

    return $path === ''
        ? appBaseUrl()
        : appBaseUrl() . '/' . $path;
}

function asset(string $path): string
{
    return url(ltrim($path, '/'));
}

function redirectTo(string $path): void
{
    header('Location: ' . url($path));
    exit();
}

function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require VIEW_PATH . '/' . ltrim($template, '/');
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirectTo('login.php');
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        redirectTo('index.php');
    }
}

function sanitize(string $data): string
{
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatPrice(float|int|string $price): string
{
    return number_format((float) $price, 0, ',', '.') . '₫';
}

function productsPageUrl(?int $categoryFilter, string $search, string $sort, int $page): string
{
    $params = [
        'sort' => $sort,
        'page' => $page,
    ];

    if ($categoryFilter) {
        $params['category'] = $categoryFilter;
    }

    if ($search !== '') {
        $params['search'] = $search;
    }

    return url('products.php?' . http_build_query($params));
}

function categoryIconName(string $categoryName): string
{
    $normalized = strtolower(trim($categoryName));

    return match ($normalized) {
        'smartphones', 'smartphone', 'phones', 'phone', 'mobile' => 'smartphone',
        'laptops', 'laptop', 'notebooks', 'notebook' => 'laptop_mac',
        'tablets', 'tablet', 'ipad', 'ipads' => 'tablet_mac',
        'audio', 'headphones', 'earbuds', 'speakers' => 'headphones',
        'accessories', 'accessory', 'gadgets' => 'cable',
        'monitors', 'monitor', 'display', 'displays' => 'desktop_windows',
        'smartwatches', 'smartwatch', 'watch', 'watches' => 'watch',
        default => 'category',
    };
}

function authText(string $vi, string $en): string
{
    return getCurrentLanguage() === 'vi' ? $vi : $en;
}

function passwordValidationError(string $password): string
{
    if (strlen($password) < 8) {
        return authText('Mật khẩu phải có ít nhất 8 ký tự.', 'Password must be at least 8 characters long.');
    }

    return '';
}

function hashPassword(string $password): string
{
    return hash('sha256', $password);
}

function verifyPassword(string $password, string $hash): bool
{
    return hash('sha256', $password) === $hash;
}

function normalizeProductDescriptionHtml(?string $html): string
{
    $html = trim((string) $html);

    if ($html === '') {
        return '';
    }

    if (!class_exists('DOMDocument')) {
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html) ?? $html;
        $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html) ?? $html;
        $html = preg_replace('/<noscript\b[^>]*>.*?<\/noscript>/is', '', $html) ?? $html;
        $html = preg_replace('/<div[^>]*class="[^"]*(?:block-content-product-right|cps-block-boxProductTvc|cps-block-content_btn-showmore)[^"]*"[^>]*>.*?<\/div>/is', '', $html) ?? $html;
        $html = preg_replace('/\sstyle="[^"]*"/i', '', $html) ?? $html;
        $html = preg_replace('/\s(?:format|provider|loading|id|class)="[^"]*"/i', '', $html) ?? $html;

        return trim((string) strip_tags(
            $html,
            '<p><br><ul><ol><li><strong><b><em><i><u><a><h1><h2><h3><h4><h5><h6><table><thead><tbody><tr><th><td><div><span><img>'
        ));
    }

    $previousUseInternalErrors = libxml_use_internal_errors(true);
    $document = new DOMDocument('1.0', 'UTF-8');
    $wrappedHtml = '<?xml encoding="utf-8" ?><div id="product-description-root">' . $html . '</div>';
    $document->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $xpath = new DOMXPath($document);

    foreach ([
        '//script',
        '//style',
        '//noscript',
        '//*[@style[contains(translate(., "ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz"), "display:none")]]',
        '//*[contains(@class, "cps-block-content_btn-showmore")]',
        '//*[contains(@class, "block-content-product-right")]',
        '//*[contains(@class, "cps-block-boxProductTvc")]',
        '//*[@id="cpsContent" and @style]',
        '//*[@class and contains(@class, "ksp-content")]',
    ] as $query) {
        $nodes = $xpath->query($query);
        if ($nodes instanceof DOMNodeList) {
            for ($index = $nodes->length - 1; $index >= 0; $index -= 1) {
                $node = $nodes->item($index);
                if ($node !== null && $node->parentNode !== null) {
                    $node->parentNode->removeChild($node);
                }
            }
        }
    }

    $allowedAttributes = ['href', 'src', 'alt', 'title', 'target', 'rel', 'colspan', 'rowspan'];
    $nodes = $xpath->query('//*');
    if ($nodes instanceof DOMNodeList) {
        foreach ($nodes as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }

            for ($index = $node->attributes->length - 1; $index >= 0; $index -= 1) {
                $attribute = $node->attributes->item($index);
                if ($attribute === null) {
                    continue;
                }

                if (!in_array($attribute->name, $allowedAttributes, true)) {
                    $node->removeAttribute($attribute->name);
                }
            }

            if ($node->tagName === 'a' && $node->getAttribute('target') === '_blank' && !$node->hasAttribute('rel')) {
                $node->setAttribute('rel', 'noopener noreferrer');
            }
        }
    }

    $root = $document->getElementById('product-description-root');
    $normalizedHtml = '';

    if ($root instanceof DOMElement) {
        foreach ($root->childNodes as $childNode) {
            $normalizedHtml .= $document->saveHTML($childNode);
        }
    }

    libxml_clear_errors();
    libxml_use_internal_errors($previousUseInternalErrors);

    return trim($normalizedHtml);
}

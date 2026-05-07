<?php
declare(strict_types=1);

class CartController
{
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $cartTotal = 0;

        if ($cart) {
            $products = Product::findManyWithCategory(getDBConnection(), array_keys($cart));

            foreach ($products as $product) {
                $quantity = max(1, (int) ($cart[$product['id']] ?? 1));
                $lineTotal = (float) $product['price'] * $quantity;
                $cartTotal += $lineTotal;
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ];
            }
        }

        $_SESSION['cart_count'] = array_sum(array_map('intval', $_SESSION['cart'] ?? []));

        view('pages/cart.php', [
            'page_title' => t('cart'),
            'page_description' => 'Giỏ hàng lưu trong session với danh sách sản phẩm đã chọn.',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
        ]);
    }
}

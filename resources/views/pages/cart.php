
<section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <h1 class="text-3xl font-black text-slate-900 dark:text-white"><?php echo t('cart'); ?></h1>
    <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">
        Your current cart is stored in the session for this project demo.
    </p>
</section>

<?php if ($cartItems): ?>
    <section class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-[1.15fr_0.85fr]">
        <div class="space-y-4">
            <?php foreach ($cartItems as $item): $product = $item['product']; ?>
                <article class="flex gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-xl bg-slate-50 p-3 dark:bg-slate-800">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-full object-contain">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-slate-300">image</span>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-primary"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <h2 class="mt-1 text-sm font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($product['name']); ?></h2>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Quantity: <?php echo $item['quantity']; ?></p>
                        <p class="mt-2 text-lg font-black text-slate-900 dark:text-white"><?php echo formatPriceVND($item['line_total']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white"><?php echo t('checkout'); ?></h2>
            <div class="mt-5 flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
                <span>Total</span>
                <span class="text-xl font-black text-slate-900 dark:text-white"><?php echo formatPriceVND($cartTotal); ?></span>
            </div>
        </aside>
    </section>
<?php else: ?>
    <section class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center dark:border-slate-700 dark:bg-slate-900">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Your cart is empty</h2>
    </section>
<?php endif; ?>

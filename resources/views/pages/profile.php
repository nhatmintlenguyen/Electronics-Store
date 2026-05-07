<?php include VIEW_PATH . '/layouts/header.php'; ?>

<section class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-primary"><?php echo t('profile'); ?></p>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white"><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></h1>
        </div>
        <a href="<?php echo url('logout.php'); ?>" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:text-slate-200">
            <?php echo t('logout'); ?>
        </a>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400"><?php echo t('full_name'); ?></p>
            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($user['full_name'] ?: '-'); ?></p>
        </div>
        <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Username</p>
            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($user['username']); ?></p>
        </div>
        <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400"><?php echo t('email'); ?></p>
            <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Vai trò</p>
            <p class="mt-2 text-sm font-semibold capitalize text-slate-900 dark:text-white"><?php echo htmlspecialchars($user['role']); ?></p>
        </div>
    </div>
</section>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>

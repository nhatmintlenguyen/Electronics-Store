function endpointUrl(path) {
    const base = document.body?.dataset.appBaseUrl || '';
    const normalizedPath = String(path || '').replace(/^\/+/, '');
    return base ? `${base}/${normalizedPath}` : `/${normalizedPath}`;
}

function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');

    if (isDark) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark');
    }
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transition-all transform translate-y-0 opacity-100';

    const bgColors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };

    toast.classList.add(bgColors[type] || bgColors.info);
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('fade-in');
    }, 10);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(1rem)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

async function addToCart(productId, quantity = 1) {
    try {
        const response = await fetch(endpointUrl('add_to_cart.php'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}`
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message || 'Đã thêm sản phẩm vào giỏ hàng.', 'success');
            updateCartCount(data.cart_count);
        } else {
            showToast(data.message || 'Không thể thêm sản phẩm vào giỏ hàng.', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Đã xảy ra lỗi.', 'error');
    }
}

function updateCartCount(count) {
    const cartBadges = document.querySelectorAll('[data-cart-count]');
    cartBadges.forEach(badge => {
        badge.textContent = count;
        if (count > 0) {
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    });
}

async function addToWishlist(productId) {
    try {
        const response = await fetch(endpointUrl('add_to_wishlist.php'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message || 'Đã thêm vào yêu thích.', 'success');
        } else {
            showToast(data.message || 'Không thể thêm vào yêu thích.', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Đã xảy ra lỗi.', 'error');
    }
}

function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('img[data-src]');

    if (!('IntersectionObserver' in window)) {
        images.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
        return;
    }

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
});

function updatePriceRange(minPrice, maxPrice) {
    console.log(`Price range: ${minPrice} - ${maxPrice}`);
}

document.addEventListener('DOMContentLoaded', function () {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(el => {
        el.addEventListener('mouseenter', function () {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg';
            tooltip.textContent = this.dataset.tooltip;
            document.body.appendChild(tooltip);

            const rect = this.getBoundingClientRect();
            tooltip.style.top = `${rect.top - tooltip.offsetHeight - 5}px`;
            tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;

            this.addEventListener('mouseleave', () => tooltip.remove(), { once: true });
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.querySelector('[data-search-form]');
    const searchInput = document.querySelector('[data-search-input]');
    const searchResults = document.querySelector('[data-search-results]');
    const searchResultsList = document.querySelector('[data-search-results-list]');

    if (!searchForm || !searchInput || !searchResults || !searchResultsList) {
        return;
    }

    let activeController = null;

    function hideSearchResults() {
        searchResults.classList.add('hidden');
        searchResultsList.innerHTML = '';
    }

    function showSearchResults() {
        searchResults.classList.remove('hidden');
    }

    function renderSearchResults(products, query) {
        if (!products.length) {
            searchResultsList.innerHTML = `
                <div class="px-4 py-5 text-sm text-slate-500 dark:text-slate-400">
                    Không tìm thấy sản phẩm cho "<span class="font-semibold">${escapeHtml(query)}</span>".
                </div>
            `;
            showSearchResults();
            return;
        }

        const items = products.map(product => `
            <a
                href="${endpointUrl(`product_detail.php?id=${product.id}`)}"
                class="flex items-center gap-3 border-b border-slate-100 px-4 py-3 transition-colors last:border-b-0 hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800"
            >
                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100 p-2 dark:bg-slate-800">
                    ${product.image_url
                        ? `<img src="${escapeAttribute(product.image_url)}" alt="${escapeAttribute(product.name)}" class="h-full w-full object-contain">`
                        : '<span class="material-symbols-outlined text-slate-400">image</span>'}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="mb-1 text-[10px] font-bold uppercase tracking-widest text-primary">${escapeHtml(product.category_name)}</p>
                    <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">${escapeHtml(product.name)}</p>
                    <p class="mt-1 text-sm font-bold text-slate-900 dark:text-white">${formatCurrency(product.price)}</p>
                </div>
            </a>
        `).join('');

        searchResultsList.innerHTML = items;
        showSearchResults();
    }

    async function fetchSearchResults(query) {
        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();

        try {
            const url = new URL(searchInput.dataset.searchEndpoint || endpointUrl('search_products.php'), window.location.origin);
            url.searchParams.set('q', query);

            const response = await fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json'
                },
                signal: activeController.signal
            });

            if (!response.ok) {
                throw new Error(`Yêu cầu tìm kiếm thất bại với mã ${response.status}`);
            }

            const data = await response.json();
            renderSearchResults(Array.isArray(data.products) ? data.products : [], query);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error('Search error:', error);
            searchResultsList.innerHTML = `
                <div class="px-4 py-5 text-sm text-red-500">
                    Không thể tải kết quả tìm kiếm.
                </div>
            `;
            showSearchResults();
        }
    }

    const debouncedSearch = debounce(value => {
        const query = value.trim();

        if (query.length === 0) {
            hideSearchResults();
            return;
        }

        fetchSearchResults(query);
    }, 250);

    searchInput.addEventListener('input', event => {
        debouncedSearch(event.target.value);
    });

    searchInput.addEventListener('focus', () => {
        if (searchResultsList.children.length > 0) {
            showSearchResults();
        }
    });

    document.addEventListener('click', event => {
        if (!searchForm.contains(event.target)) {
            hideSearchResults();
        }
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            hideSearchResults();
        }
    });
});

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function escapeAttribute(value) {
    return escapeHtml(value);
}

document.addEventListener('DOMContentLoaded', function () {
    const categoryButtons = document.querySelectorAll('[data-category-button]');
    const categoryFilters = document.querySelector('[data-category-filters]');
    const productGrid = document.querySelector('[data-product-grid]');
    const totalProductsValue = document.querySelector('[data-total-products-value]');
    const pagination = document.querySelector('[data-pagination]');

    if (!categoryButtons.length || !productGrid || !categoryFilters) {
        return;
    }

    const initialGridMarkup = productGrid.innerHTML;
    const initialTotalProductsText = totalProductsValue ? totalProductsValue.textContent : '';
    const initialPaginationMarkup = pagination ? pagination.innerHTML : '';
    const language = document.documentElement.lang === 'vi' ? 'vi' : 'en';
    let activeCategoryId = 'all';

    categoryButtons.forEach(button => {
        button.addEventListener('click', async function () {
            const categoryId = this.dataset.categoryId || 'all';
            activeCategoryId = categoryId;

            categoryButtons.forEach(item => {
                item.classList.remove('is-active');
                item.setAttribute('aria-pressed', 'false');
            });

            this.classList.add('is-active');
            this.setAttribute('aria-pressed', 'true');

            productGrid.classList.add('product-grid-loading');

            try {
                await fetchProductsByCategory(categoryId, {
                    productGrid,
                    totalProductsValue,
                    pagination,
                    initialGridMarkup,
                    initialTotalProductsText,
                    initialPaginationMarkup,
                    endpoint: categoryFilters.dataset.categoryEndpoint || endpointUrl('filter_products.php'),
                    language,
                    categoryId,
                    page: 1
                });
            } finally {
                window.setTimeout(() => {
                    productGrid.classList.remove('product-grid-loading');
                }, 220);
            }
        });
    });

    if (pagination) {
        pagination.addEventListener('click', async event => {
            const pageButton = event.target.closest('[data-category-page]');

            if (!pageButton) {
                return;
            }

            event.preventDefault();

            const nextPage = Number(pageButton.dataset.categoryPage || 1);
            if (!Number.isInteger(nextPage) || nextPage < 1) {
                return;
            }

            productGrid.classList.add('product-grid-loading');

            try {
                await fetchProductsByCategory(activeCategoryId, {
                    productGrid,
                    totalProductsValue,
                    pagination,
                    initialGridMarkup,
                    initialTotalProductsText,
                    initialPaginationMarkup,
                    endpoint: categoryFilters.dataset.categoryEndpoint || endpointUrl('filter_products.php'),
                    language,
                    categoryId: activeCategoryId,
                    page: nextPage
                });
            } finally {
                window.setTimeout(() => {
                    productGrid.classList.remove('product-grid-loading');
                }, 220);
            }
        });
    }
});

async function fetchProductsByCategory(categoryId, options) {
    const {
        productGrid,
        totalProductsValue,
        pagination,
        initialGridMarkup,
        initialTotalProductsText,
        initialPaginationMarkup,
        endpoint,
        language,
        page = 1
    } = options;

    if (categoryId === 'all' && page === 1) {
        productGrid.innerHTML = initialGridMarkup;

        if (totalProductsValue) {
            totalProductsValue.textContent = initialTotalProductsText;
        }

        if (pagination) {
            pagination.innerHTML = initialPaginationMarkup;
            pagination.classList.remove('hidden');
        }

        return;
    }

    const url = new URL(endpoint, window.location.origin);
    url.searchParams.set('category_id', categoryId);
    url.searchParams.set('page', String(page));

    const response = await fetch(url.toString(), {
        headers: {
            'Accept': 'application/json'
        }
    });

    if (!response.ok) {
        throw new Error(`Category request failed with status ${response.status}`);
    }

    const data = await response.json();
    const products = Array.isArray(data.products) ? data.products : [];

    productGrid.innerHTML = products.length
        ? products.map(product => renderProductCard(product, language)).join('')
        : renderEmptyProducts(language);

    if (totalProductsValue) {
        totalProductsValue.textContent = new Intl.NumberFormat(language === 'vi' ? 'vi-VN' : 'en-US')
            .format(Number(data.total_products || 0));
    }

    if (pagination) {
        pagination.innerHTML = renderCategoryPagination({
            currentPage: Number(data.current_page || 1),
            totalPages: Number(data.total_pages || 1),
            language
        });

        if (Number(data.total_pages || 1) > 1) {
            pagination.classList.remove('hidden');
        } else {
            pagination.classList.add('hidden');
        }
    }
}

function renderProductCard(product, language = 'vi') {
    const categoryName = escapeHtml(product.category_name || '');
    const productName = escapeHtml(product.name || '');
    const imageUrl = product.image_url ? escapeAttribute(product.image_url) : '';
    const productId = Number(product.id);
    const productPrice = formatCurrency(product.price || 0);
    const productRating = Number(product.rating || 0).toFixed(1);
    const addToCartLabel = language === 'vi' ? 'Them vao gio' : 'Add to cart';

    return `
        <div class="product-card group relative overflow-hidden rounded-xl border border-slate-100 bg-white transition-all duration-300 hover:shadow-xl dark:border-slate-700 dark:bg-slate-800">
            <a href="${endpointUrl(`product_detail.php?id=${productId}`)}" class="block">
                <div class="relative flex aspect-square items-center justify-center overflow-hidden bg-slate-50 p-8 dark:bg-slate-900">
                    <div class="absolute right-2 top-2 z-10 flex flex-col gap-2">
                        <button onclick="event.preventDefault(); addToWishlist(${productId});" class="rounded-full bg-white/80 p-1.5 text-slate-600 shadow-sm backdrop-blur-sm transition-colors hover:text-red-500">
                            <span class="material-symbols-outlined text-xl">favorite</span>
                        </button>
                    </div>
                    ${imageUrl
                        ? `<img src="${imageUrl}" alt="${productName}" class="max-h-full object-contain transition-transform duration-500 group-hover:scale-110">`
                        : '<div class="text-slate-300"><span class="material-symbols-outlined" style="font-size: 80px;">image</span></div>'}
                    <div class="cart-button absolute inset-x-0 bottom-0 translate-y-4 p-4 opacity-0 transition-all duration-300">
                        <button onclick="event.preventDefault(); addToCart(${productId});" class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-2.5 font-bold text-white shadow-lg shadow-primary/20">
                            <span class="material-symbols-outlined text-lg">add_shopping_cart</span> ${addToCartLabel}
                        </button>
                    </div>
                </div>
            </a>
            <div class="p-5">
                <span class="mb-1 block text-[10px] font-bold uppercase tracking-widest text-primary">${categoryName}</span>
                <h2 class="mb-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                    <a href="${endpointUrl(`product_detail.php?id=${productId}`)}" class="line-clamp-2">${productName}</a>
                </h2>
                <div class="mb-3 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm text-yellow-400" style="font-variation-settings: 'FILL' 1">star</span>
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400">${productRating}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-lg font-black text-slate-900 dark:text-white">${productPrice}</span>
                </div>
            </div>
        </div>
    `;
}

function renderEmptyProducts(language = 'vi') {
    const title = 'Không có sản phẩm trong danh mục này';
    const description = 'Hãy thử chọn một danh mục khác.';

    return `
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center dark:border-slate-700 dark:bg-slate-900">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">${title}</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">${description}</p>
        </div>
    `;
}

function renderCategoryPagination({ currentPage = 1, totalPages = 1, language = 'vi' }) {
    if (totalPages <= 1) {
        return '';
    }

    const prevLabel = language === 'vi' ? 'Trước' : 'Previous';
    const nextLabel = language === 'vi' ? 'Sau' : 'Next';
    const range = 2;
    const startPage = Math.max(1, currentPage - range);
    const endPage = Math.min(totalPages, currentPage + range);
    const parts = [];

    if (currentPage > 1) {
        parts.push(renderCategoryPaginationButton(currentPage - 1, prevLabel));
    }

    if (startPage > 1) {
        parts.push(renderCategoryPaginationButton(1, '1'));
        if (startPage > 2) {
            parts.push('<span class="px-2 text-sm text-slate-400">...</span>');
        }
    }

    for (let page = startPage; page <= endPage; page += 1) {
        parts.push(renderCategoryPaginationButton(page, String(page), page === currentPage));
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            parts.push('<span class="px-2 text-sm text-slate-400">...</span>');
        }
        parts.push(renderCategoryPaginationButton(totalPages, String(totalPages)));
    }

    if (currentPage < totalPages) {
        parts.push(renderCategoryPaginationButton(currentPage + 1, nextLabel));
    }

    return parts.join('');
}

function renderCategoryPaginationButton(page, label, isActive = false) {
    const classes = isActive
        ? 'rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-lg shadow-primary/20'
        : 'rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200';

    return `
        <button
            type="button"
            data-category-page="${page}"
            class="${classes}"
            ${isActive ? 'aria-current="page"' : ''}
        >
            ${escapeHtml(label)}
        </button>
    `;
}

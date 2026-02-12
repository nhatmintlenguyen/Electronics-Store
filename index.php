<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Home';
include 'includes/header.php';

// Fetch featured products
$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    ORDER BY p.rating DESC 
    LIMIT 8
");
$stmt->execute();
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories
$stmt = $conn->prepare("SELECT * FROM categories LIMIT 5");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">Welcome to Electronics Store</h1>
        <p class="lead mb-4">Discover the latest in technology and electronics</p>
        <a href="products.php" class="btn btn-light btn-lg">Shop Now</a>
    </div>
</section>

<div class="container mt-5">
    <!-- Categories Section -->
    <section class="mb-5">
        <h2 class="text-center mb-4">Browse Categories</h2>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-md-4 col-lg-2">
                    <a href="products.php?category=<?php echo $category['id']; ?>" class="text-decoration-none">
                        <div class="card category-card text-center p-3">
                            <div class="card-body">
                                <i class="bi bi-laptop fs-1 text-primary"></i>
                                <h5 class="card-title mt-2"><?php echo htmlspecialchars($category['name']); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Featured Products Section -->
    <section class="mb-5">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card">
                        <?php if ($product['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div class="card-img-top product-image bg-light d-flex align-items-center justify-content-center">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="text-muted small"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <div class="product-rating mb-2">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="bi bi-star-fill<?php echo $i < $product['rating'] ? '' : ' text-muted'; ?>"></i>
                                <?php endfor; ?>
                                <span class="ms-1">(<?php echo $product['rating']; ?>)</span>
                            </div>
                            <p class="price mb-3"><?php echo formatPrice($product['price']); ?></p>
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary w-100">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="mb-5">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                <h4>Free Shipping</h4>
                <p class="text-muted">On orders over 5,000,000₫</p>
            </div>
            <div class="col-md-4">
                <i class="bi bi-shield-check fs-1 text-primary mb-3"></i>
                <h4>Secure Payment</h4>
                <p class="text-muted">100% secure transactions</p>
            </div>
            <div class="col-md-4">
                <i class="bi bi-headset fs-1 text-primary mb-3"></i>
                <h4>24/7 Support</h4>
                <p class="text-muted">Dedicated customer service</p>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>

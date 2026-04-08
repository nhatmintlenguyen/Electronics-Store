<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Products';
include 'includes/header.php';

$conn = getDBConnection();

// Get filter parameters
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($category_filter) {
    $query .= " AND p.category_id = :category";
    $params[':category'] = $category_filter;
}

if ($search) {
    $query .= " AND p.name LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

$query .= " ORDER BY p.name ASC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for filter
$stmt = $conn->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1 class="mb-4">Products</h1>
    
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search products..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Products Grid -->
    <?php if (count($products) > 0): ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card h-100">
                        <?php if ($product['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div class="card-img-top product-image bg-light d-flex align-items-center justify-content-center">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="text-muted small"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <div class="product-rating mb-2">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="bi bi-star-fill<?php echo $i < $product['rating'] ? '' : ' text-muted'; ?>"></i>
                                <?php endfor; ?>
                                <span class="ms-1">(<?php echo $product['rating']; ?>)</span>
                            </div>
                            <p class="price mb-3"><?php echo formatPrice($product['price']); ?></p>
                            <div class="mt-auto">
                                <a href="product_detail.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No products found.
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

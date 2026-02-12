<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Store Locations';
include 'includes/header.php';

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM locations ORDER BY name");
$stmt->execute();
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1 class="mb-4">Our Store Locations</h1>
    
    <div class="row g-4">
        <?php foreach ($locations as $location): ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <?php echo htmlspecialchars($location['name']); ?>
                        </h5>
                        <p class="card-text">
                            <i class="bi bi-map"></i>
                            <?php echo htmlspecialchars($location['address']); ?>
                        </p>
                        <?php if ($location['map_link']): ?>
                            <a href="<?php echo htmlspecialchars($location['map_link']); ?>" 
                               target="_blank" 
                               class="btn btn-primary">
                                <i class="bi bi-map"></i> View on Map
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="alert alert-info mt-4">
        <i class="bi bi-info-circle"></i>
        <strong>Store Hours:</strong> Monday - Friday: 9:00 AM - 9:00 PM | Saturday - Sunday: 10:00 AM - 8:00 PM
    </div>
</div>

<?php include 'includes/footer.php'; ?>

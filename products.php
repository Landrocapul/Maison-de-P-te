<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Handle add to cart
if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    if (!isLoggedIn()) {
        header('Location: login.php?message=Please login to add to cart');
        exit();
    }
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $product_id = intval($_POST['product_id']);
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    header('Location: products.php?added=1');
    exit();
}

// Handle filters
$search = $_GET['search'] ?? '';
$categories = $_GET['categories'] ?? [];
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// Get all categories for filter
try {
    $categories_stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''");
    $all_categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $all_categories = [];
}

// Get filtered products
try {
    $query = "SELECT * FROM products WHERE 1";
    $params = [];

    if ($search) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }

    if (!empty($categories)) {
        $placeholders = str_repeat('?,', count($categories) - 1) . '?';
        $query .= " AND category IN ($placeholders)";
        $params = array_merge($params, $categories);
    }

    if ($min_price !== '') {
        $query .= " AND price >= ?";
        $params[] = $min_price;
    }

    if ($max_price !== '') {
        $query .= " AND price <= ?";
        $params[] = $max_price;
    }

    $query .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-100 p-6">
            <h2 class="text-xl font-semibold mb-4">Filters</h2>
            <form method="get">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search products..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categories</label>
                    <?php foreach ($all_categories as $cat): ?>
                        <label class="block">
                            <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($cat); ?>" <?php if (in_array($cat, $categories)) echo 'checked'; ?> class="mr-2">
                            <?php echo htmlspecialchars($cat); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Price</label>
                    <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" step="0.01" placeholder="0.00" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Price</label>
                    <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" step="0.01" placeholder="100.00" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Apply Filters</button>
                <a href="products.php" class="block text-center mt-2 text-indigo-600 hover:text-indigo-800">Clear Filters</a>
            </form>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <h1 class="text-4xl font-bold mb-8">Our Products</h1>

            <?php if (isset($_GET['added'])): ?>
                <p class="text-green-500 mb-4">Product added to cart!</p>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <?php if ($product['image_path']): ?>
                            <img src="<?php echo $product['image_path']; ?>" alt="<?php echo sanitize($product['name']); ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">No Image</div>
                        <?php endif; ?>
                        <div class="p-6">
                            <h2 class="text-xl font-semibold mb-2"><?php echo sanitize($product['name']); ?></h2>
                            <p class="text-gray-600 mb-2"><?php echo sanitize($product['description']); ?></p>
                            <p class="text-lg font-bold text-indigo-600 mb-2">$<?php echo number_format($product['price'], 2); ?></p>
                            <p class="text-sm text-gray-500 mb-4">Stock: <?php echo $product['stock']; ?></p>
                            <form method="post">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <?php if ($product['stock'] > 0): ?>
                                    <button type="submit" name="add_to_cart" class="w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Add to Cart</button>
                                <?php else: ?>
                                    <button disabled class="w-full bg-gray-400 text-white py-2 px-4 rounded cursor-not-allowed">Out of Stock</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($products)): ?>
                <p class="text-center text-gray-600">No products match your filters.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

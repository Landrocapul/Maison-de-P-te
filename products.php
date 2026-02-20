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
$sort = $_GET['sort'] ?? 'created_at DESC';

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

    $allowed_sorts = ['name ASC', 'name DESC', 'price ASC', 'price DESC', 'created_at DESC'];
    if (in_array($sort, $allowed_sorts)) {
        $query .= " ORDER BY $sort";
    } else {
        $query .= " ORDER BY created_at DESC";
    }

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .product-card:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-[#f3b93d] p-6 animate__animated animate__fadeInLeft">
            <h2 class="text-xl font-semibold mb-4">Filters</h2>
            <form method="get">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        Search
                    </label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search products..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
                        Categories
                    </label>
                    <?php foreach ($all_categories as $cat): ?>
                        <label class="block">
                            <input type="checkbox" name="categories[]" value="<?php echo htmlspecialchars($cat); ?>" <?php if (in_array($cat, $categories)) echo 'checked'; ?> class="mr-2">
                            <?php echo htmlspecialchars($cat); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Min Price
                    </label>
                    <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" step="0.01" placeholder="0.00" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Max Price
                    </label>
                    <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" step="0.01" placeholder="100.00" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                </div>
                <button type="submit" class="w-full bg-[#4c2b1b] text-white py-2 px-4 rounded hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b] flex items-center justify-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Apply Filters
                </button>
                <a href="products.php" class="block text-center text-[#4c2b1b] hover:text-[#3a1f14] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    Clear Filters
                </a>
            </form>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8 animate__animated animate__fadeInRight">
            <h1 class="text-4xl font-bold mb-8">Our Products</h1>

            <div class="mb-4 flex justify-end">
                <form method="get" class="flex items-center">
                    <label class="mr-2 text-gray-700">Sort by:</label>
                    <select name="sort" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                        <option value="created_at DESC" <?php echo ($sort == 'created_at DESC') ? 'selected' : ''; ?>>Newest</option>
                        <option value="name ASC" <?php echo ($sort == 'name ASC') ? 'selected' : ''; ?>>Name A-Z</option>
                        <option value="name DESC" <?php echo ($sort == 'name DESC') ? 'selected' : ''; ?>>Name Z-A</option>
                        <option value="price ASC" <?php echo ($sort == 'price ASC') ? 'selected' : ''; ?>>Price Low-High</option>
                        <option value="price DESC" <?php echo ($sort == 'price DESC') ? 'selected' : ''; ?>>Price High-Low</option>
                    </select>
                    <!-- Hidden inputs for filters -->
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <?php foreach ($categories as $cat): ?>
                        <input type="hidden" name="categories[]" value="<?php echo htmlspecialchars($cat); ?>">
                    <?php endforeach; ?>
                    <input type="hidden" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>">
                    <input type="hidden" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>">
                </form>
            </div>

            <?php if (isset($_GET['added'])): ?>
                <p class="text-green-500 mb-4">Product added to cart!</p>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-lg shadow-xl overflow-hidden animate__animated animate__zoomIn product-card">
                        <?php if ($product['image_path']): ?>
                            <img src="<?php echo $product['image_path']; ?>" alt="<?php echo sanitize($product['name']); ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                            </div>
                        <?php endif; ?>
                        <div class="p-6">
                            <h2 class="text-xl font-semibold mb-2"><?php echo sanitize($product['name']); ?></h2>
                            <p class="text-sm text-gray-500 mb-1"><?php echo sanitize($product['category'] ?? 'Uncategorized'); ?></p>
                            <p class="text-gray-600 mb-2"><?php
                                $desc = sanitize($product['description']);
                                echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                            ?></p>
                            <p class="text-lg font-bold text-[#4c2b1b] mb-2">$<?php echo number_format($product['price'], 2); ?></p>
                            <form method="post">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="add_to_cart" class="w-full bg-[#4c2b1b] text-white py-2 px-4 rounded hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b] flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm0-6a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12 6a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm0-6a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                                    Add to Cart
                                </button>
                            </form>
                            <button onclick="openQuickView(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', '<?php echo addslashes($product['description']); ?>', '<?php echo $product['price']; ?>', '<?php echo addslashes($product['category'] ?? 'Uncategorized'); ?>', '<?php echo $product['image_path']; ?>')" class="mt-2 w-full bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Quick View
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($products)): ?>
                <p class="text-center text-gray-600">No products match your filters.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick View Modal -->
    <div id="quick-view-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h2 id="modal-title" class="text-2xl font-bold"></h2>
                <button onclick="closeQuickView()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="flex">
                <div class="w-48 h-48 flex items-center justify-center bg-gray-200 rounded mr-6">
                    <img id="modal-image" src="" alt="Product Image" class="w-full h-full object-cover rounded" style="display: none;">
                    <svg id="modal-placeholder" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                </div>
                <div class="flex-1">
                    <p id="modal-category" class="text-sm text-gray-500 mb-2"></p>
                    <p id="modal-description" class="text-gray-600 mb-4"></p>
                    <p id="modal-price" class="text-lg font-bold text-[#4c2b1b] mb-4"></p>
                    <form method="post">
                        <input type="hidden" id="modal-product-id" name="product_id" value="">
                        <button type="submit" name="add_to_cart" class="bg-[#4c2b1b] text-white py-2 px-4 rounded hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b]">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
<script>
function openQuickView(id, name, desc, price, category, image) {
    document.getElementById('modal-title').textContent = name;
    document.getElementById('modal-description').textContent = desc;
    document.getElementById('modal-price').textContent = '$' + parseFloat(price).toFixed(2);
    document.getElementById('modal-category').textContent = category;
    if (image) {
        document.getElementById('modal-image').src = image;
        document.getElementById('modal-image').style.display = 'block';
        document.getElementById('modal-placeholder').style.display = 'none';
    } else {
        document.getElementById('modal-image').style.display = 'none';
        document.getElementById('modal-placeholder').style.display = 'block';
    }
    document.getElementById('modal-product-id').value = id;
    document.getElementById('quick-view-modal').classList.remove('hidden');
}

function closeQuickView() {
    document.getElementById('quick-view-modal').classList.add('hidden');
}
</script>
</html>

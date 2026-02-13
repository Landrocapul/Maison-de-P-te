<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$message = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Handle delete
if ($action === 'delete' && $id) {
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if ($product && $product['image_path']) {
            unlink('../' . $product['image_path']);
        }
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Product deleted successfully.';
        $action = 'list';
    } catch (PDOException $e) {
        $message = 'Delete failed: ' . $e->getMessage();
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $category = sanitize($_POST['category']);
    $stock = intval($_POST['stock']);

    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'uploads/' . $file_name;
            } else {
                $message = 'Failed to upload image.';
            }
        } else {
            $message = 'Only JPG, JPEG, PNG & GIF files are allowed.';
        }
    }

    if (!$message) {
        try {
            if ($action === 'edit' && $id) {
                if ($image_path) {
                    // Delete old image if new uploaded
                    $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
                    $stmt->execute([$id]);
                    $old_product = $stmt->fetch();
                    if ($old_product && $old_product['image_path']) {
                        unlink('../' . $old_product['image_path']);
                    }
                    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, stock = ?, image_path = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $price, $category, $stock, $image_path, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, stock = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $price, $category, $stock, $id]);
                }
                $message = 'Product updated successfully.';
            } elseif ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, stock, image_path) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $category, $stock, $image_path]);
                $message = 'Product added successfully.';
            }
            $action = 'list';
        } catch (PDOException $e) {
            $message = 'Operation failed: ' . $e->getMessage();
        }
    }
}

// Get product for edit
$product = null;
if ($action === 'edit' && $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
    } catch (PDOException $e) {
        $message = 'Failed to load product.';
    }
}

// Get all products for list
$products = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = 'Failed to load products.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800">Admin</h1>
            </div>
            <nav class="mt-6">
                <a href="dashboard.php" class="flex items-center py-2 px-6 text-gray-600 hover:bg-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="products.php" class="flex items-center py-2 px-6 text-gray-600 hover:bg-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Manage Products
                </a>
                <a href="orders.php" class="flex items-center py-2 px-6 text-gray-600 hover:bg-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Manage Orders
                </a>
                <a href="users.php" class="flex items-center py-2 px-6 text-gray-600 hover:bg-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Manage Users
                </a>
                <a href="../logout.php" onclick="return confirm('Are you sure you want to logout?')" class="flex items-center py-2 px-6 text-gray-600 hover:bg-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <h2 class="text-3xl font-bold mb-6">Manage Products</h2>

            <?php if ($message): ?>
                <p class="text-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-500 mb-4"><?php echo $message; ?></p>
            <?php endif; ?>

            <?php if ($action === 'list'): ?>
                <a href="products.php?action=add" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 mb-4 inline-block">Add New Product</a>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($products as $prod): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($prod['image_path']): ?>
                                            <img src="../<?php echo $prod['image_path']; ?>" alt="Product" class="w-16 h-16 object-cover">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo sanitize($prod['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">$<?php echo number_format($prod['price'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $prod['stock']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="products.php?action=edit&id=<?php echo $prod['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <a href="products.php?action=delete&id=<?php echo $prod['id']; ?>" class="text-red-600 hover:text-red-900 ml-4" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <h3 class="text-xl font-semibold mb-4"><?php echo $action === 'edit' ? 'Edit' : 'Add'; ?> Product</h3>
                <form method="post" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" required value="<?php echo $product ? sanitize($product['name']) : ''; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"><?php echo $product ? sanitize($product['description']) : ''; ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                        <input type="number" step="0.01" name="price" id="price" required value="<?php echo $product ? $product['price'] : ''; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <input type="text" name="category" id="category" value="<?php echo $product ? sanitize($product['category']) : ''; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                        <input type="number" name="stock" id="stock" required value="<?php echo $product ? $product['stock'] : 0; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                        <?php if ($product && $product['image_path']): ?>
                            <img src="../<?php echo $product['image_path']; ?>" alt="Current Image" class="w-32 h-32 object-cover mb-2">
                        <?php endif; ?>
                        <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full">
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"><?php echo $action === 'edit' ? 'Update' : 'Add'; ?> Product</button>
                    <a href="products.php" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

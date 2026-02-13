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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-6 animate__animated animate__fadeInDown">Manage Products</h2>

        <?php if ($message): ?>
            <p class="text-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-500 mb-4 animate__animated animate__fadeIn"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <a href="products.php?action=add" class="bg-[#4c2b1b] text-white px-4 py-2 rounded hover:bg-[#3a1f14] transition duration-300 mb-4 inline-block animate__animated animate__fadeInUp">Add New Product</a>
            <div class="bg-white rounded-lg shadow-xl overflow-hidden animate__animated animate__fadeInUp">
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
                                <tr class="animate__animated animate__zoomIn">
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
                                        <a href="products.php?action=edit&id=<?php echo $prod['id']; ?>" class="text-[#4c2b1b] hover:text-[#3a1f14] hover:underline transition duration-300">Edit</a>
                                        <a href="products.php?action=delete&id=<?php echo $prod['id']; ?>" class="text-[#4c2b1b] hover:text-[#3a1f14] hover:underline transition duration-300 ml-4" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <h3 class="text-xl font-semibold mb-4 animate__animated animate__fadeInDown"><?php echo $action === 'edit' ? 'Edit' : 'Add'; ?> Product</h3>
                <form method="post" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-xl animate__animated animate__fadeInUp">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" required value="<?php echo $product ? sanitize($product['name']) : ''; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]"><?php echo $product ? sanitize($product['description']) : ''; ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                        <input type="number" step="0.01" name="price" id="price" required value="<?php echo $product ? $product['price'] : ''; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                    </div>
                    <div class="mb-4">
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <input type="text" name="category" id="category" value="<?php echo $product ? sanitize($product['category']) : ''; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                    </div>
                    <div class="mb-4">
                        <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                        <input type="number" name="stock" id="stock" required value="<?php echo $product ? $product['stock'] : 0; ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                    </div>
                    <div class="mb-4">
                        <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                        <?php if ($product && $product['image_path']): ?>
                            <img src="../<?php echo $product['image_path']; ?>" alt="Current Image" class="w-32 h-32 object-cover mb-2">
                        <?php endif; ?>
                        <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full">
                    </div>
                    <button type="submit" class="bg-[#4c2b1b] text-white px-4 py-2 rounded hover:bg-[#3a1f14] transition duration-300"><?php echo $action === 'edit' ? 'Update' : 'Add'; ?> Product</button>
                    <a href="products.php" class="ml-4 text-[#4c2b1b] hover:text-[#3a1f14] hover:underline transition duration-300">Cancel</a>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>

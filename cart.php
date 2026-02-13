<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

// Handle remove from cart
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header('Location: cart.php');
    exit();
}

// Handle update cart
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $qty) {
        $qty = intval($qty);
        if ($qty > 0) {
            $_SESSION['cart'][$product_id] = $qty;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header('Location: cart.php?updated=1');
    exit();
}

// Handle checkout
if (isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        header('Location: cart.php?error=No items in cart');
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Calculate total and check stock
        $total = 0;
        $items = [];
        $stock_errors = [];
        foreach ($_SESSION['cart'] as $product_id => $qty) {
            $stmt = $pdo->prepare("SELECT name, price, stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            if ($product) {
                if ($product['stock'] < $qty) {
                    $stock_errors[] = "Insufficient stock for {$product['name']} (available: {$product['stock']})";
                }
                $subtotal = $product['price'] * $qty;
                $total += $subtotal;
                $items[] = ['product_id' => $product_id, 'quantity' => $qty, 'price' => $product['price'], 'name' => $product['name']];
            }
        }

        if ($stock_errors) {
            $pdo->rollBack();
            header('Location: cart.php?error=' . implode(', ', $stock_errors));
            exit();
        }

        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $order_id = $pdo->lastInsertId();

        // Insert order items
        foreach ($items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        }

        // Deduct stock
        foreach ($items as $item) {
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }

        $pdo->commit();

        // Clear cart
        unset($_SESSION['cart']);

        header('Location: dashboard.php?order_placed=1');
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        header('Location: cart.php?error=Checkout failed: ' . $e->getMessage());
        exit();
    }
}

// Get cart items
$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $qty) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        if ($product) {
            $subtotal = $product['price'] * $qty;
            $total += $subtotal;
            $cart_items[] = array_merge($product, ['quantity' => $qty, 'subtotal' => $subtotal]);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="container mx-auto px-4 py-6 flex justify-between items-center">
            <a href="index.php" class="text-3xl font-bold text-gray-800">Maison de Pâte</a>
            <nav class="space-x-4">
                <a href="index.php" class="text-gray-600 hover:text-gray-800">Home</a>
                <a href="products.php" class="text-gray-600 hover:text-gray-800">Products</a>
                <a href="dashboard.php" class="text-gray-600 hover:text-gray-800">Dashboard</a>
                <a href="cart.php" class="text-gray-600 hover:text-gray-800">Cart (<?php echo count($_SESSION['cart'] ?? []); ?>)</a>
                <a href="logout.php" class="text-gray-600 hover:text-gray-800">Logout</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-8">Your Cart</h1>

        <?php if (isset($_GET['updated'])): ?>
            <p class="text-green-500 text-center mb-4">Cart updated!</p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p class="text-red-500 text-center mb-4"><?php echo sanitize($_GET['error']); ?></p>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <p class="text-center text-gray-600">Your cart is empty. <a href="products.php" class="text-indigo-600 hover:text-indigo-500">Browse products</a></p>
        <?php else: ?>
            <form method="post" class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <?php if ($item['image_path']): ?>
                                                <img src="<?php echo $item['image_path']; ?>" alt="<?php echo sanitize($item['name']); ?>" class="w-16 h-16 object-cover mr-4">
                                            <?php endif; ?>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?php echo sanitize($item['name']); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo sanitize($item['description']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="w-16 px-2 py-1 border border-gray-300 rounded">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="cart.php?remove=<?php echo $item['id']; ?>" class="text-red-600 hover:text-red-900">Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 flex justify-between items-center">
                    <button type="submit" name="update_cart" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Update Cart</button>
                    <div class="text-right">
                        <p class="text-lg font-semibold">Total: $<?php echo number_format($total, 2); ?></p>
                        <button type="submit" name="checkout" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 mt-2">Checkout</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2023 Maison de Pâte. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

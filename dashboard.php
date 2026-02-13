<?php
require_once 'includes/functions.php';
requireLogin();
require_once 'includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = sanitize($_POST['fullname']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    if (strlen($password) > 0 && strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    // Check email uniqueness if changed
    if ($email !== $_SESSION['email']) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $errors[] = 'Email already exists.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error.';
        }
    }

    if (empty($errors)) {
        try {
            if ($password) {
                $hashed = hashPassword($password);
                $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$fullname, $email, $hashed, $_SESSION['user_id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ? WHERE id = ?");
                $stmt->execute([$fullname, $email, $_SESSION['user_id']]);
            }
            $_SESSION['email'] = $email; // Update session
            $message = 'Profile updated successfully.';
        } catch (PDOException $e) {
            $message = 'Update failed: ' . $e->getMessage();
        }
    } else {
        $message = implode(' ', $errors);
    }
}

if (isAdmin()) {
    header('Location: admin/dashboard.php');
    exit();
}

$user = getUser($pdo, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-6 animate__animated animate__fadeInDown">Welcome, <?php echo sanitize($user['username']); ?>!</h2>

        <?php if ($message): ?>
            <p class="text-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-500 mb-4 animate__animated animate__fadeIn"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Profile Section -->
        <div class="bg-white p-6 rounded-lg shadow-xl mb-6 animate__animated animate__fadeInUp">
            <h3 class="text-xl font-semibold mb-4">Your Profile</h3>
            <p><strong>Username:</strong> <?php echo sanitize($user['username']); ?></p>
            <p><strong>Full Name:</strong> <?php echo sanitize($user['fullname'] ?? ''); ?></p>
            <p><strong>Email:</strong> <?php echo sanitize($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo sanitize($user['role']); ?></p>
            <p><strong>Joined:</strong> <?php echo sanitize($user['created_at']); ?></p>
        </div>

        <!-- Edit Profile Section -->
        <div class="bg-white p-6 rounded-lg shadow-xl mb-6 animate__animated animate__fadeInUp">
            <h3 class="text-xl font-semibold mb-4">Edit Profile</h3>
                <form method="post">
                    <div class="mb-4">
                        <label for="fullname" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="fullname" id="fullname" value="<?php echo sanitize($user['fullname'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required value="<?php echo sanitize($user['email']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                    </div>
                    <div class="mb-6">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                    </div>
                    <button type="submit" class="bg-[#4c2b1b] text-white px-4 py-2 rounded hover:bg-[#3a1f14] transition duration-300">Update Profile</button>
                </form>
            </div>

            <!-- Orders Section -->
            <div class="bg-white p-6 rounded-lg shadow-xl animate__animated animate__fadeInUp">
                <h3 id="orders" class="text-xl font-semibold mb-4">Your Orders</h3>
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
                    $stmt->execute([$_SESSION['user_id']]);
                    $orders = $stmt->fetchAll();
                    if ($orders): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($orders as $order): ?>
                                        <?php
                                        // Fetch order items
                                        $items_stmt = $pdo->prepare("SELECT oi.quantity, oi.price, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                                        $items_stmt->execute([$order['id']]);
                                        $items = $items_stmt->fetchAll();
                                        ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['id']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">$<?php echo number_format($order['total'], 2); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><?php echo ucfirst($order['status']); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap"><button onclick="toggleDetails(<?php echo $order['id']; ?>)" class="text-[#4c2b1b] hover:text-[#3a1f14] hover:underline transition duration-300">View Details</button></td>
                                        </tr>
                                        <tr id="details-<?php echo $order['id']; ?>" style="display:none;">
                                            <td colspan="5" class="px-6 py-4">
                                                <div class="bg-gray-50 p-4 rounded">
                                                    <h4 class="font-semibold mb-2">Order Items:</h4>
                                                    <ul class="space-y-1">
                                                        <?php foreach ($items as $item): ?>
                                                            <li><?php echo sanitize($item['name']); ?> - Quantity: <?php echo $item['quantity']; ?> - Price: $<?php echo number_format($item['price'], 2); ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>You haven't placed any orders yet.</p>
                        <a href="products.php" class="text-[#4c2b1b] hover:text-[#3a1f14] hover:underline transition duration-300">Browse products to place your first order</a>
                    <?php endif;
                } catch (PDOException $e) {
                    echo '<p class="text-red-500">Failed to load orders.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

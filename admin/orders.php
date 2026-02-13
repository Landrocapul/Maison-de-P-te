<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = sanitize($_POST['status']);
    $valid_statuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
    if (in_array($status, $valid_statuses)) {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $order_id]);
            $message = 'Order status updated successfully.';
        } catch (PDOException $e) {
            $message = 'Failed to update status: ' . $e->getMessage();
        }
    } else {
        $message = 'Invalid status.';
    }
}

// Get all orders with user info
try {
    $stmt = $pdo->query("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC");
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
    $message = 'Failed to load orders.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Maison de Pâte</title>
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
            <h2 class="text-3xl font-bold mb-6">Manage Orders</h2>

            <?php if ($message): ?>
                <p class="text-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-500 mb-4"><?php echo $message; ?></p>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['id']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo sanitize($order['username']); ?> (<?php echo sanitize($order['email']); ?>)</td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">$<?php echo number_format($order['total'], 2); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="post" class="inline">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1">
                                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <!-- Add view details link if needed -->
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($orders)): ?>
                <p class="text-center text-gray-600 mt-4">No orders found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

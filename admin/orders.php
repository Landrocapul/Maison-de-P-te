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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-6 animate__animated animate__fadeInDown">Manage Orders</h2>

        <?php if ($message): ?>
            <p class="text-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-500 mb-4 animate__animated animate__fadeIn"><?php echo $message; ?></p>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-xl overflow-hidden animate__animated animate__fadeInUp">
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
                        <tr class="animate__animated animate__zoomIn">
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo sanitize($order['username']); ?> (<?php echo sanitize($order['email']); ?>)</td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">$<?php echo number_format($order['total'], 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="post" class="inline">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b] transition duration-300">
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
                                <a href="#" class="text-[#4c2b1b] hover:text-[#3a1f14] hover:underline transition duration-300">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($orders)): ?>
            <p class="text-center text-gray-600 mt-4 animate__animated animate__fadeIn">No orders found.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>

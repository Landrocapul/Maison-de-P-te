<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$message = '';

// Helper function for status badges
function getStatusClass($status) {
    switch ($status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'confirmed': return 'bg-blue-100 text-blue-800';
        case 'shipped': return 'bg-orange-100 text-orange-800';
        case 'delivered': return 'bg-green-100 text-green-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

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

// Get order items for each order
$order_items = [];
if (!empty($orders)) {
    try {
        foreach ($orders as $order) {
            $stmt = $pdo->prepare("SELECT oi.quantity, oi.price, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
            $stmt->execute([$order['id']]);
            $order_items[$order['id']] = $stmt->fetchAll();
        }
    } catch (PDOException $e) {
        // Handle error if needed
    }
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
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    <?php include '../includes/navbar.php'; ?>

    <div class="flex-grow">
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
                        <tr class="animate__animated animate__zoomIn hover:bg-gray-50 transition duration-200">
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo $order['id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo sanitize($order['username']); ?> (<?php echo sanitize($order['email']); ?>)</td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">$<?php echo number_format($order['total'], 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="post" class="inline mb-2">
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
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium <?php echo getStatusClass($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="toggleDetails(<?php echo $order['id']; ?>)" class="bg-[#4c2b1b] text-white px-3 py-1 rounded hover:bg-[#3a1f14] transition duration-300 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    View Details
                                </button>
                            </td>
                        </tr>
                        <tr id="details-<?php echo $order['id']; ?>" style="display:none;">
                            <td colspan="6" class="px-6 py-4">
                                <div class="bg-gray-50 p-4 rounded animate__animated animate__fadeIn">
                                    <h4 class="font-semibold mb-2">Order Items:</h4>
                                    <?php if (!empty($order_items[$order['id']])): ?>
                                        <ul class="space-y-1">
                                            <?php foreach ($order_items[$order['id']] as $item): ?>
                                                <li><?php echo sanitize($item['name']); ?> - Quantity: <?php echo $item['quantity']; ?> - Price: $<?php echo number_format($item['price'], 2); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>No items found.</p>
                                    <?php endif; ?>
                                </div>
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

    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
<script>
function toggleDetails(orderId) {
    const details = document.getElementById('details-' + orderId);
    if (details.style.display === 'none' || details.style.display === '') {
        details.style.display = 'table-row';
    } else {
        details.style.display = 'none';
    }
}
</script>
</html>

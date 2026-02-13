<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$user = getUser($pdo, $_SESSION['user_id']);

// Get some stats
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
    $total_products = $stmt->fetch()['total_products'];

    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users WHERE role = 'customer'");
    $total_customers = $stmt->fetch()['total_users'];

    $stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM orders");
    $total_orders = $stmt->fetch()['total_orders'];
} catch (PDOException $e) {
    $total_products = $total_customers = $total_orders = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-6 animate__animated animate__fadeInDown">Welcome, <?php echo sanitize($user['username']); ?>!</h2>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 animate__animated animate__fadeInUp">
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <h3 class="text-xl font-semibold mb-2">Total Products</h3>
                <p class="text-3xl font-bold text-[#4c2b1b]"><?php echo $total_products; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <h3 class="text-xl font-semibold mb-2">Total Customers</h3>
                <p class="text-3xl font-bold text-[#f7be43]"><?php echo $total_customers; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <h3 class="text-xl font-semibold mb-2">Total Orders</h3>
                <p class="text-3xl font-bold text-[#f3b93d]"><?php echo $total_orders; ?></p>
            </div>
        </div>

        <!-- Recent Activity or something -->
        <div class="bg-white p-6 rounded-lg shadow-xl animate__animated animate__fadeInUp">
            <h3 class="text-xl font-semibold mb-4">Quick Actions</h3>
            <a href="products.php?action=add" class="bg-[#4c2b1b] text-white px-4 py-2 rounded hover:bg-[#3a1f14] transition duration-300">Add New Product</a>
            <a href="products.php" class="bg-[#f7be43] text-white px-4 py-2 rounded hover:bg-[#e6c28a] transition duration-300 ml-4">View All Products</a>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>

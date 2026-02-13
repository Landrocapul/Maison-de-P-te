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
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
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
            <h2 class="text-3xl font-bold mb-6">Welcome, <?php echo sanitize($user['username']); ?>!</h2>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2">Total Products</h3>
                    <p class="text-3xl font-bold text-indigo-600"><?php echo $total_products; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2">Total Customers</h3>
                    <p class="text-3xl font-bold text-green-600"><?php echo $total_customers; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2">Total Orders</h3>
                    <p class="text-3xl font-bold text-orange-600"><?php echo $total_orders; ?></p>
                </div>
            </div>

            <!-- Recent Activity or something -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-semibold mb-4">Quick Actions</h3>
                <a href="products.php?action=add" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Add New Product</a>
                <a href="products.php" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 ml-4">View All Products</a>
            </div>
        </div>
    </div>
</body>
</html>

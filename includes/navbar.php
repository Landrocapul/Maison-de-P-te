<!-- Header -->
<header class="bg-white shadow">
    <div class="container mx-auto px-4 py-6 flex justify-between items-center">
        <div class="flex items-center">
            <a href="index.php" class="flex items-center">
                <img src="images/logos/maisondepate_logo_high.png" alt="Maison de Pâte Logo" class="h-12 mr-2">
                <img src="images/logos/maisondepate_word_high.png" alt="Maison de Pâte" class="h-10">
            </a>
        </div>
        <nav class="space-x-4">
            <a href="index.php" class="text-gray-600 hover:text-gray-800">Home</a>
            <a href="products.php" class="text-gray-600 hover:text-gray-800">Products</a>
            <a href="about.php" class="text-gray-600 hover:text-gray-800">About</a>
            <a href="contact.php" class="text-gray-600 hover:text-gray-800">Contact</a>
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php" class="text-gray-600 hover:text-gray-800">Dashboard</a>
                <?php if (isAdmin()): ?>
                    <a href="admin/orders.php" class="text-gray-600 hover:text-gray-800">Orders</a>
                <?php endif; ?>
                <a href="cart.php" class="text-gray-600 hover:text-gray-800">Cart (<?php echo count($_SESSION['cart'] ?? []); ?>)</a>
                <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')" class="text-gray-600 hover:text-gray-800">Logout</a>
            <?php else: ?>
                <a href="login.php" class="text-gray-600 hover:text-gray-800">Login</a>
                <a href="register.php" class="text-gray-600 hover:text-gray-800">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

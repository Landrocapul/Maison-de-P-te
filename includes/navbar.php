<!-- Header -->
<header class="bg-[#f7be43] shadow">
    <div class="container mx-auto px-4 py-6 flex justify-between items-center">
        <div class="flex items-center">
            <a href="index.php" class="flex items-center">
                <img src="images/logos/maisondepate_logo_high.png" alt="Maison de Pâte Logo" class="h-12 mr-2">
                <img src="images/logos/maisondepate_word_high.png" alt="Maison de Pâte" class="h-10">
            </a>
        </div>
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <nav class="space-x-4">
            <a href="index.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'index.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Home</a>
            <a href="products.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'products.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Products</a>
            <a href="about.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'about.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">About</a>
            <a href="contact.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'contact.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Contact</a>
            <?php if (isLoggedIn()): ?>
                <a href="dashboard.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'dashboard.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Profile</a>
                <?php if (isAdmin()): ?>
                    <a href="admin/orders.php" class="px-3 py-2 rounded font-medium text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]">Orders</a>
                <?php endif; ?>
                <a href="cart.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'cart.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Cart (<?php echo count($_SESSION['cart'] ?? []); ?>)</a>
                <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')" class="px-3 py-2 rounded font-medium text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]">Logout</a>
            <?php else: ?>
                <a href="login.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'login.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Login</a>
                <a href="register.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'register.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

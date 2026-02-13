<!-- Header -->
<header class="bg-[#f7be43] shadow">
    <div class="container mx-auto px-4 py-6 flex justify-between items-center">
        <div class="flex items-center">
            <a href="<?php echo $base_path; ?>index.php" class="flex items-center">
                <img src="images/logos/maisondepate_logo_high.png" alt="Maison de Pâte Logo" class="h-12 mr-2">
                <img src="images/logos/maisondepate_word_high.png" alt="Maison de Pâte" class="h-10">
            </a>
        </div>
        <?php $current_page = basename($_SERVER['PHP_SELF']); $base_path = basename(dirname($_SERVER['PHP_SELF'])) == 'admin' ? '../' : ''; $is_admin_page = strpos($_SERVER['PHP_SELF'], '/admin/') !== false; ?>
        <nav class="space-x-4">
            <a href="<?php echo $base_path; ?>index.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'index.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Home</a>
            <a href="<?php echo $base_path; ?>products.php" class="px-3 py-2 rounded font-medium <?php echo (!$is_admin_page && $current_page == 'products.php') ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Products</a>
            <?php if (isAdmin()): ?>
                <a href="<?php echo basename(dirname($_SERVER['PHP_SELF'])) == 'admin' ? 'products.php' : 'admin/products.php'; ?>" class="px-3 py-2 rounded font-medium <?php echo ($is_admin_page && $current_page == 'products.php') ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Manage Products</a>
            <?php endif; ?>
            <?php if (!isAdmin()): ?>
                <a href="<?php echo $base_path; ?>about.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'about.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">About</a>
                <a href="<?php echo $base_path; ?>contact.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'contact.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Contact</a>
            <?php endif; ?>
            <?php if (isLoggedIn()): ?>
                <a href="<?php echo basename(dirname($_SERVER['PHP_SELF'])) == 'admin' ? '../dashboard.php' : 'dashboard.php'; ?>" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'dashboard.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Profile</a>
                <?php if (isAdmin()): ?>
                    <a href="<?php echo basename(dirname($_SERVER['PHP_SELF'])) == 'admin' ? 'orders.php' : 'admin/orders.php'; ?>" class="px-3 py-2 rounded font-medium <?php echo ($is_admin_page && $current_page == 'orders.php') ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Orders</a>
                <?php endif; ?>
                <?php if (!isAdmin()): ?>
                    <a href="<?php echo $base_path; ?>cart.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'cart.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Cart (<?php echo count($_SESSION['cart'] ?? []); ?>)</a>
                <?php endif; ?>
                <a href="<?php echo basename(dirname($_SERVER['PHP_SELF'])) == 'admin' ? '../logout.php' : 'logout.php'; ?>" onclick="return confirm('Are you sure you want to logout?')" class="px-3 py-2 rounded font-medium text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>login.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'login.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Login</a>
                <a href="<?php echo $base_path; ?>register.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'register.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

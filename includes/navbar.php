<?php require_once 'db.php'; require_once 'functions.php'; ?>
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
<?php
if (isLoggedIn() && !isAdmin()) {
    $cart_items = [];
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $qty) {
            $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            if ($product) {
                $subtotal = $product['price'] * $qty;
                $total += $subtotal;
                $cart_items[] = ['id' => $product_id, 'name' => $product['name'], 'price' => $product['price'], 'quantity' => $qty, 'subtotal' => $subtotal];
            }
        }
    }
}
?>
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
                    <button onclick="toggleCartSidebar()" class="px-3 py-2 rounded font-medium text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]">Cart (<?php echo count($_SESSION['cart'] ?? []); ?>)</button>
                <?php endif; ?>
                <a href="<?php echo basename(dirname($_SERVER['PHP_SELF'])) == 'admin' ? '../logout.php' : 'logout.php'; ?>" onclick="return confirm('Are you sure you want to logout?')" class="px-3 py-2 rounded font-medium text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>login.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'login.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Login</a>
                <a href="<?php echo $base_path; ?>register.php" class="px-3 py-2 rounded font-medium <?php echo $current_page == 'register.php' ? 'bg-[#4c2b1b] text-white' : 'text-[#4c2b1b] hover:bg-[#e6c28a] hover:text-[#3a1f14]'; ?>">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<div id="cart-sidebar" class="fixed top-0 right-0 h-full w-80 bg-white shadow-lg transform translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
    <div class="p-4">
        <button onclick="toggleCartSidebar()" class="float-right text-xl font-bold">&times;</button>
        <h2 class="text-xl font-bold mb-4">Your Cart</h2>
        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty.</p>
            <a href="products.php" class="text-[#4c2b1b] hover:text-[#3a1f14]">Browse products</a>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($cart_items as $item): ?>
                    <div class="border-b pb-2 mb-2">
                        <div class="flex justify-between items-center">
                            <span class="font-medium"><?php echo sanitize($item['name']); ?></span>
                            <a href="cart.php?remove=<?php echo $item['id']; ?>" class="text-red-500 text-sm">Remove</a>
                        </div>
                        <div class="text-sm text-gray-600">
                            Quantity: <?php echo $item['quantity']; ?> | Price: $<?php echo number_format($item['price'], 2); ?> | Subtotal: $<?php echo number_format($item['subtotal'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="border-t pt-4">
                    <p class="font-bold text-lg">Total: $<?php echo number_format($total, 2); ?></p>
                    <a href="cart.php" class="block bg-[#4c2b1b] text-white px-4 py-2 rounded mt-2 text-center hover:bg-[#3a1f14]">View Full Cart</a>
                    <form method="post" action="cart.php" class="mt-2">
                        <button type="submit" name="checkout" class="w-full bg-[#f3b93d] text-white px-4 py-2 rounded hover:bg-[#e6c28a]">Checkout</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function toggleCartSidebar() {
    const sidebar = document.getElementById('cart-sidebar');
    sidebar.classList.toggle('translate-x-full');
}
</script>

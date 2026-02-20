<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

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
    if ($email !== $user['email']) {
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
            $user = getUser($pdo, $_SESSION['user_id']); // Refresh user data
        } catch (PDOException $e) {
            $message = 'Update failed: ' . $e->getMessage();
        }
    } else {
        $message = implode(' ', $errors);
    }
}

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
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    <?php include '../includes/navbar.php'; ?>

    <div class="flex-grow">
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-6 animate__animated animate__fadeInDown">Welcome, <?php echo sanitize($user['username']); ?>!</h2>

        <!-- Profile Section -->
        <div class="bg-white p-6 rounded-lg shadow-xl mb-6 animate__animated animate__fadeInUp">
            <h3 class="text-xl font-semibold mb-4">Admin Profile</h3>
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 bg-[#4c2b1b] rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    <?php echo strtoupper(substr(sanitize($user['username']), 0, 1)); ?>
                </div>
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-2">
                    <p><strong>Username:</strong> <?php echo sanitize($user['username']); ?></p>
                    <p><strong>Full Name:</strong> <?php echo sanitize($user['fullname'] ?? ''); ?></p>
                    <p><strong>Email:</strong> <?php echo sanitize($user['email']); ?></p>
                    <p><strong>Role:</strong> <?php echo sanitize($user['role']); ?></p>
                    <p><strong>Joined:</strong> <?php echo sanitize($user['created_at']); ?></p>
                </div>
            </div>
        </div>

        <!-- Edit Profile Section -->
        <div class="bg-white p-6 rounded-lg shadow-xl mb-6 animate__animated animate__fadeInUp animate__delay-1s">
            <h3 class="text-xl font-semibold mb-4">Edit Profile</h3>
            <?php if ($message): ?>
                <p class="text-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-500 mb-4"><?php echo $message; ?></p>
            <?php endif; ?>
            <form method="post">
                <div class="mb-4">
                    <label for="fullname" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        <span class="ml-2">Full Name</span>
                    </label>
                    <input type="text" name="fullname" id="fullname" value="<?php echo sanitize($user['fullname'] ?? ''); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <span class="ml-2">Email</span>
                    </label>
                    <input type="email" name="email" id="email" required value="<?php echo sanitize($user['email']); ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>
                        <span class="ml-2">New Password (leave blank to keep current)</span>
                    </label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                </div>
                <div class="mb-6">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>
                        <span class="ml-2">Confirm New Password</span>
                    </label>
                    <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                </div>
                <button type="submit" class="bg-[#4c2b1b] text-white px-4 py-2 rounded hover:bg-[#3a1f14] transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    <span>Update Profile</span>
                </button>
            </form>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 animate__animated animate__fadeInUp">
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2 text-[#4c2b1b]"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-2.25-1.313L16.5 7.5V2.25a.75.75 0 00-.75-.75H8.25a.75.75 0 00-.75.75V7.5L4.5 6.187 2.25 7.5V18a.75.75 0 00.75.75h16.5a.75.75 0 00.75-.75V7.5z" /></svg>
                    <h3 class="text-xl font-semibold">Total Products</h3>
                </div>
                <p class="text-3xl font-bold text-[#4c2b1b]"><?php echo $total_products; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2 text-[#f7be43]"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                    <h3 class="text-xl font-semibold">Total Customers</h3>
                </div>
                <p class="text-3xl font-bold text-[#f7be43]"><?php echo $total_customers; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2 text-[#f3b93d]"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm0-6a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12 6a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm0-6a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                    <h3 class="text-xl font-semibold">Total Orders</h3>
                </div>
                <p class="text-3xl font-bold text-[#f3b93d]"><?php echo $total_orders; ?></p>
            </div>
        </div>

        <!-- Recent Activity or something -->
        <div class="bg-white p-6 rounded-lg shadow-xl animate__animated animate__fadeInUp">
            <h3 class="text-xl font-semibold mb-4">Quick Actions</h3>
            <a href="products.php?action=add" class="bg-[#4c2b1b] text-white px-4 py-2 rounded hover:bg-[#3a1f14] transition duration-300 inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add New Product
            </a>
            <a href="products.php" class="bg-[#f7be43] text-white px-4 py-2 rounded hover:bg-[#e6c28a] transition duration-300 ml-4 inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                View All Products
            </a>
        </div>
    </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>

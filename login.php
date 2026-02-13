<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$message = '';

if (isset($_GET['registered'])) {
    $message = 'Registration successful. Please login.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            if ($_SESSION['role'] == 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: products.php');
            }
            exit();
        } else {
            $message = 'Invalid email or password.';
        }
    } catch (PDOException $e) {
        $message = 'Login failed: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
            <?php if ($message): ?>
                <p class="text-<?php echo isset($_GET['registered']) ? 'green' : 'red'; ?>-500 mb-4"><?php echo $message; ?></p>
            <?php endif; ?>
            <form method="post">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Login</button>
            </form>
            <p class="mt-4 text-center">Don't have an account? <a href="register.php" class="text-indigo-600 hover:text-indigo-500">Register</a></p>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

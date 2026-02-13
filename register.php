<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $fullname = sanitize($_POST['fullname']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'customer';

    $errors = [];

    if (empty(trim($fullname))) {
        $errors[] = 'Full name is required.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        try {
            // Check if email or username exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            if ($stmt->fetch()) {
                $errors[] = 'Email or username already exists.';
            } else {
                // Insert new user
                $hashed = hashPassword($password);
                $stmt = $pdo->prepare("INSERT INTO users (username, fullname, email, password, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $fullname, $email, $hashed, $role]);
                header('Location: login.php?registered=1');
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = 'Registration failed: ' . $e->getMessage();
        }
    }

    if ($errors) {
        $message = implode(' ', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
            <?php if ($message): ?>
                <p class="text-red-500 mb-4"><?php echo $message; ?></p>
            <?php endif; ?>
            <form method="post">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" id="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="fullname" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="fullname" id="fullname" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-6">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Register</button>
            </form>
            <p class="mt-4 text-center">Already have an account? <a href="login.php" class="text-indigo-600 hover:text-indigo-500">Login</a></p>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

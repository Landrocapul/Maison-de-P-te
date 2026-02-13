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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            background-image: url('images/bg/bakeryspace.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
    </style>
</head>
<body">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="animate__animated animate__fadeInDown">
                <img src="images/logos/maisondepate_logo_high.png" alt="Maison de Pâte" class="mx-auto h-28 w-auto">
                <h2 class="mt-6 text-center text-3xl font-extrabold text-white">Create your account</h2>
            </div>
            <div class="bg-white bg-opacity-90 backdrop-blur-sm py-8 px-6 shadow-2xl rounded-lg animate__animated animate__fadeInUp">
                <?php if ($message): ?>
                    <p class="text-red-500 mb-4 text-center"><?php echo $message; ?></p>
                <?php endif; ?>
                <form method="post" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b] transition duration-300">
                    </div>
                    <div>
                        <label for="fullname" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="fullname" id="fullname" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b] transition duration-300">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b] transition duration-300">
                    </div>
                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" required class="mt-1 block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b] transition duration-300">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="open-eye-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="closed-eye-password" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <line x1="4" y1="4" x2="20" y2="20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="relative">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" required class="mt-1 block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b] transition duration-300">
                        <button type="button" onclick="togglePassword('confirm_password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="open-eye-confirm_password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="closed-eye-confirm_password" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <line x1="4" y1="4" x2="20" y2="20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-[#4c2b1b] text-white py-2 px-4 rounded-md hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b] transition duration-300 animate__animated animate__pulse">Create account</button>
                    </div>
                </form>
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">Already have an account? <a href="login.php" class="font-medium text-[#4c2b1b] hover:text-[#3a1f14] transition duration-300">Sign in here</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <script>
        function togglePassword(id) {
            var x = document.getElementById(id);
            var openEye = document.getElementById('open-eye-' + id);
            var closedEye = document.getElementById('closed-eye-' + id);
            if (x.type === "password") {
                x.type = "text";
                openEye.classList.add('hidden');
                closedEye.classList.remove('hidden');
            } else {
                x.type = "password";
                closedEye.classList.add('hidden');
                openEye.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>

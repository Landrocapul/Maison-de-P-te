<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-center mb-8">Contact Us</h1>
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
            <p class="text-lg text-gray-600 mb-6">We'd love to hear from you! Reach out to us for any questions, orders, or feedback.</p>
            <div class="space-y-4">
                <div>
                    <h3 class="text-xl font-semibold">Address</h3>
                    <p>123 Bakery Street<br>Paris, France 75001</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold">Phone</h3>
                    <p>+33 1 23 45 67 89</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold">Email</h3>
                    <p>info@maisondepate.com</p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold">Hours</h3>
                    <p>Monday - Friday: 7:00 AM - 7:00 PM<br>Saturday - Sunday: 8:00 AM - 5:00 PM</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

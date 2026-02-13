<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-center mb-8">About Maison de Pâte</h1>
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
            <p class="text-lg text-gray-600 mb-6">Maison de Pâte is a family-owned bakery dedicated to crafting the finest breads and pastries using traditional French techniques passed down through generations. Our passion for baking shines through in every loaf, croissant, and éclair we create.</p>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-2xl font-semibold mb-4">Our Story</h3>
                    <p class="text-gray-600">Founded in 1920, Maison de Pâte has been serving the community with artisanal baked goods for over a century. What started as a small family bakery has grown into a beloved institution, known for our commitment to quality and tradition.</p>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold mb-4">Our Philosophy</h3>
                    <p class="text-gray-600">We believe that great baking starts with the finest ingredients. We source our flour, butter, and other components from trusted suppliers, ensuring that every product meets our high standards of excellence.</p>
                </div>
            </div>
            <div class="mt-8">
                <h3 class="text-2xl font-semibold mb-4">Visit Us</h3>
                <p class="text-gray-600">Come experience the warmth and aroma of our bakery. Whether you're picking up a fresh baguette for dinner or indulging in one of our decadent pastries, we welcome you to Maison de Pâte.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

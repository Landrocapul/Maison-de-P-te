<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maison de Pâte - House of Dough</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-[#4c2b1b] to-[#f7be43] text-white py-20 bg-cover bg-center" style="background-image: url('images/bg/warmbakery.jpg');">
        <div class="container mx-auto px-4 text-center bg-black bg-opacity-50 py-20 rounded-lg">
            <h2 class="text-5xl font-bold mb-4">Welcome to Maison de Pâte</h2>
            <p class="text-xl mb-8">The finest breads and pastries, crafted with love</p>
            <a href="products.php" class="bg-[#4c2b1b] text-white px-8 py-3 rounded-full font-semibold hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b]">Browse Our Products</a>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h3 class="text-4xl font-bold text-gray-800 mb-4">About Us</h3>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">At Maison de Pâte, we believe in the art of baking. Our master bakers use traditional techniques and the finest ingredients to create breads and pastries that delight the senses. From crusty baguettes to decadent éclairs, every item is made with passion.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <img src="images/products/freshbaked.jpeg" alt="Fresh Baked" class="w-16 h-16 mx-auto mb-4 rounded-full object-cover">
                    <h4 class="text-xl font-semibold mb-2">Fresh Daily</h4>
                    <p class="text-gray-600">Baked fresh every morning with organic ingredients.</p>
                </div>
                <div class="text-center">
                    <img src="images/products/croissants.jpg" alt="Artisanal Quality" class="w-16 h-16 mx-auto mb-4 rounded-full object-cover">
                    <h4 class="text-xl font-semibold mb-2">Artisanal Quality</h4>
                    <p class="text-gray-600">Handcrafted with traditional methods and love.</p>
                </div>
                <div class="text-center">
                    <img src="images/bg/bakeryspace.jpg" alt="Family Owned" class="w-16 h-16 mx-auto mb-4 rounded-full object-cover">
                    <h4 class="text-xl font-semibold mb-2">Family Owned</h4>
                    <p class="text-gray-600">A family tradition passed down through generations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <h3 class="text-4xl font-bold text-center text-gray-800 mb-12">Featured Products</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                <div class="text-center">
                    <img src="images/products/baguettes.jpg" alt="Baguettes" class="w-full h-32 object-cover rounded-lg mb-4">
                    <h4 class="text-lg font-semibold">Baguettes</h4>
                </div>
                <div class="text-center">
                    <img src="images/products/Macarons.jpeg" alt="Macarons" class="w-full h-32 object-cover rounded-lg mb-4">
                    <h4 class="text-lg font-semibold">Macarons</h4>
                </div>
                <div class="text-center">
                    <img src="images/products/Mille-Feuille.jpg" alt="Mille-Feuille" class="w-full h-32 object-cover rounded-lg mb-4">
                    <h4 class="text-lg font-semibold">Mille-Feuille</h4>
                </div>
                <div class="text-center">
                    <img src="images/products/Pain au Chocolat.jpg" alt="Pain au Chocolat" class="w-full h-32 object-cover rounded-lg mb-4">
                    <h4 class="text-lg font-semibold">Pain au Chocolat</h4>
                </div>
                <div class="text-center">
                    <img src="images/products/Tarte Tatin.jpg" alt="Tarte Tatin" class="w-full h-32 object-cover rounded-lg mb-4">
                    <h4 class="text-lg font-semibold">Tarte Tatin</h4>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .hero-text {
            animation: fadeInUp 1s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .featured-item:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
        .about-card:hover {
            transform: translateY(-10px);
            transition: transform 0.3s ease;
        }
        .indicator.active {
            opacity: 1;
        }
        .animate-zoom {
            animation: zoomOut 2s ease-out;
        }
        @keyframes zoomOut {
            from {
                background-size: 120%;
                background-position: center;
            }
            to {
                background-size: 100%;
                background-position: center;
            }
        }
        .hero-content {
            opacity: 0;
            transform: translateY(18px);
        }
        .hero-content.is-active {
            animation: heroIn 900ms cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        .hero-content.is-active .hero-title {
            animation: heroTitleIn 900ms cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        .hero-content.is-active .hero-subtitle {
            animation: heroSubtitleIn 1000ms cubic-bezier(0.2, 0.8, 0.2, 1) 90ms forwards;
        }
        .hero-content.is-active .hero-cta {
            animation: heroCtaIn 1050ms cubic-bezier(0.2, 0.8, 0.2, 1) 170ms forwards;
        }
        @keyframes heroIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes heroTitleIn {
            from {
                opacity: 0;
                transform: translateY(14px);
                filter: blur(2px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
                filter: blur(0);
            }
        }
        @keyframes heroSubtitleIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes heroCtaIn {
            from {
                opacity: 0;
                transform: translateY(8px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .glass-panel {
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
        .category-card {
            transform: translateY(10px);
            opacity: 0;
        }
        .category-card.is-visible {
            animation: cardIn 800ms cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        .category-card:hover {
            transform: translateY(-6px) scale(1.01);
            transition: transform 220ms ease;
        }
        @keyframes cardIn {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="carousel-container flex transition-transform duration-500 ease-in-out" id="carousel">
            <!-- Slide 1 -->
            <div class="carousel-slide flex-shrink-0 w-full text-white py-20 bg-cover bg-center" style="background-image: url('images/bg/warmbakery.jpg');">
                <div class="container mx-auto px-4 text-center glass-panel py-20 rounded-lg hero-content">
                    <img src="images/logos/maisondepate_logo_high.png" alt="Maison de Pâte" class="mx-auto mb-4 w-80">
                    <h2 class="text-5xl font-bold mb-4 hero-title">Welcome to Maison de Pâte</h2>
                    <p class="text-xl mb-8 hero-subtitle">The finest breads and pastries, crafted with love</p>
                    <a href="products.php" class="bg-[#4c2b1b] text-white px-8 py-3 rounded-full font-semibold hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b] hero-cta">Browse Our Products</a>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="carousel-slide flex-shrink-0 w-full text-white py-20 bg-cover bg-center" style="background-image: url('images/products/freshbaked.jpeg');">
                <div class="container mx-auto px-4 text-center glass-panel py-20 rounded-lg hero-content">
                    <h2 class="text-5xl font-bold mb-4 hero-title">Breads</h2>
                    <p class="text-xl mb-8 hero-subtitle">loaves, buns, bagels</p>
                    <a href="products.php?categories[]=Bread" class="bg-[#4c2b1b] text-white px-8 py-3 rounded-full font-semibold hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b] hero-cta">View Breads</a>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="carousel-slide flex-shrink-0 w-full text-white py-20 bg-cover bg-center" style="background-image: url('images/products/croissants.jpg');">
                <div class="container mx-auto px-4 text-center glass-panel py-20 rounded-lg hero-content">
                    <h2 class="text-5xl font-bold mb-4 hero-title">Pastries</h2>
                    <p class="text-xl mb-8 hero-subtitle">croissants, danish</p>
                    <a href="products.php?categories[]=Pastry" class="bg-[#4c2b1b] text-white px-8 py-3 rounded-full font-semibold hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b] hero-cta">View Pastries</a>
                </div>
            </div>
            <!-- Slide 4 -->
            <div class="carousel-slide flex-shrink-0 w-full text-white py-20 bg-cover bg-center" style="background-image: url('images/products/Mille-Feuille.jpg');">
                <div class="container mx-auto px-4 text-center glass-panel py-20 rounded-lg hero-content">
                    <h2 class="text-5xl font-bold mb-4 hero-title">Cakes & Cupcakes</h2>
                    <p class="text-xl mb-8 hero-subtitle">Delicious cakes and cupcakes for every occasion</p>
                    <a href="products.php" class="bg-[#4c2b1b] text-white px-8 py-3 rounded-full font-semibold hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b] hero-cta">View Cakes</a>
                </div>
            </div>
            <!-- Slide 5 -->
            <div class="carousel-slide flex-shrink-0 w-full text-white py-20 bg-cover bg-center" style="background-image: url('images/products/Macarons.jpeg');">
                <div class="container mx-auto px-4 text-center glass-panel py-20 rounded-lg hero-content">
                    <h2 class="text-5xl font-bold mb-4 hero-title">Cookies & Bars</h2>
                    <p class="text-xl mb-8 hero-subtitle">Crunchy cookies and chewy bars</p>
                    <a href="products.php" class="bg-[#4c2b1b] text-white px-8 py-3 rounded-full font-semibold hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b] hero-cta">View Cookies</a>
                </div>
            </div>
            <!-- Slide 6 -->
            <div class="carousel-slide flex-shrink-0 w-full text-white py-20 bg-cover bg-center" style="background-image: url('images/products/Tarte Tatin.jpg');">
                <div class="container mx-auto px-4 text-center glass-panel py-20 rounded-lg hero-content">
                    <h2 class="text-5xl font-bold mb-4 hero-title">Pies/Tarts</h2>
                    <p class="text-xl mb-8 hero-subtitle">Sweet and savory pies and tarts</p>
                    <a href="products.php" class="bg-[#4c2b1b] text-white px-8 py-3 rounded-full font-semibold hover:bg-[#3a1f14] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#4c2b1b] hero-cta">View Pies</a>
                </div>
            </div>
        </div>
        <!-- Indicators -->
        <div class="absolute bottom-5 left-1/2 transform -translate-x-1/2 flex space-x-2" id="indicators">
            <button class="indicator w-3 h-3 rounded-full bg-white opacity-50 active" data-slide="0"></button>
            <button class="indicator w-3 h-3 rounded-full bg-white opacity-50" data-slide="1"></button>
            <button class="indicator w-3 h-3 rounded-full bg-white opacity-50" data-slide="2"></button>
            <button class="indicator w-3 h-3 rounded-full bg-white opacity-50" data-slide="3"></button>
            <button class="indicator w-3 h-3 rounded-full bg-white opacity-50" data-slide="4"></button>
            <button class="indicator w-3 h-3 rounded-full bg-white opacity-50" data-slide="5"></button>
        </div>
        <!-- Prev/Next -->
        <button class="prev absolute left-5 top-1/2 transform -translate-y-1/2 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75" id="prev">&lt;</button>
        <button class="next absolute right-5 top-1/2 transform -translate-y-1/2 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75" id="next">&gt;</button>
    </section>

    <script>
        const carousel = document.getElementById('carousel');
        const indicators = document.getElementById('indicators');
        const prevBtn = document.getElementById('prev');
        const nextBtn = document.getElementById('next');
        let currentSlide = 0;
        const totalSlides = 6;
        let autoPlayInterval;

        function showSlide(index) {
            carousel.style.transform = `translateX(-${index * 100}%)`;
            updateIndicators(index);
            document.querySelectorAll('.carousel-slide').forEach((slide, i) => {
                const content = slide.querySelector('.hero-content');
                if (i === index) {
                    slide.classList.add('animate-zoom');
                    if (content) content.classList.add('is-active');
                } else {
                    slide.classList.remove('animate-zoom');
                    if (content) content.classList.remove('is-active');
                }
            });
        }

        function updateIndicators(index) {
            indicators.querySelectorAll('.indicator').forEach((btn, i) => {
                btn.classList.toggle('active', i === index);
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            showSlide(currentSlide);
        }

        function goToSlide(index) {
            currentSlide = index;
            showSlide(currentSlide);
        }

        // Event listeners
        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);
        indicators.querySelectorAll('.indicator').forEach((btn, i) => {
            btn.addEventListener('click', () => goToSlide(i));
        });

        // Auto-play
        function startAutoPlay() {
            autoPlayInterval = setInterval(nextSlide, 4000);
        }

        function stopAutoPlay() {
            clearInterval(autoPlayInterval);
        }

        // Swipe support
        let startX;
        carousel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            stopAutoPlay();
        });

        carousel.addEventListener('touchend', (e) => {
            if (!startX) return;
            let endX = e.changedTouches[0].clientX;
            let diffX = startX - endX;
            if (Math.abs(diffX) > 50) {
                if (diffX > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
            startX = null;
            startAutoPlay();
        });

        // Pause auto-play on hover
        carousel.addEventListener('mouseenter', stopAutoPlay);
        carousel.addEventListener('mouseleave', startAutoPlay);

        // Start auto-play on load
        showSlide(currentSlide);
        startAutoPlay();
    </script>

    <!-- Category Highlights -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10">
                <h3 class="text-4xl font-bold text-gray-800 mb-3">Explore Our Categories</h3>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Discover artisan breads, flaky pastries, and sweet classics made fresh.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <a href="products.php?categories[]=Bread" class="category-card block bg-white rounded-xl shadow-lg overflow-hidden" data-reveal>
                    <div class="h-44 bg-cover bg-center" style="background-image: url('images/products/baguettes.jpg');"></div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-1 text-[#4c2b1b]">Breads</h4>
                        <p class="text-gray-600">Loaves, buns, bagels</p>
                    </div>
                </a>
                <a href="products.php?categories[]=Pastry" class="category-card block bg-white rounded-xl shadow-lg overflow-hidden" data-reveal>
                    <div class="h-44 bg-cover bg-center" style="background-image: url('images/products/croissants.jpg');"></div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-1 text-[#4c2b1b]">Pastries</h4>
                        <p class="text-gray-600">Croissants, danish</p>
                    </div>
                </a>
                <a href="products.php" class="category-card block bg-white rounded-xl shadow-lg overflow-hidden" data-reveal>
                    <div class="h-44 bg-cover bg-center" style="background-image: url('images/products/Mille-Feuille.jpg');"></div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-1 text-[#4c2b1b]">Cakes & Cupcakes</h4>
                        <p class="text-gray-600">Celebration-ready sweets</p>
                    </div>
                </a>
                <a href="products.php" class="category-card block bg-white rounded-xl shadow-lg overflow-hidden" data-reveal>
                    <div class="h-44 bg-cover bg-center" style="background-image: url('images/products/Macarons.jpeg');"></div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-1 text-[#4c2b1b]">Cookies & Bars</h4>
                        <p class="text-gray-600">Crunchy, chewy, buttery</p>
                    </div>
                </a>
                <a href="products.php" class="category-card block bg-white rounded-xl shadow-lg overflow-hidden" data-reveal>
                    <div class="h-44 bg-cover bg-center" style="background-image: url('images/products/Tarte Tatin.jpg');"></div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-1 text-[#4c2b1b]">Pies & Tarts</h4>
                        <p class="text-gray-600">Sweet and savory favorites</p>
                    </div>
                </a>
                <a href="products.php" class="category-card block bg-white rounded-xl shadow-lg overflow-hidden" data-reveal>
                    <div class="h-44 bg-cover bg-center" style="background-image: url('images/products/Pain au Chocolat.jpg');"></div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-1 text-[#4c2b1b]">Chef’s Picks</h4>
                        <p class="text-gray-600">Seasonal highlights</p>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <script>
        const revealEls = document.querySelectorAll('[data-reveal]');
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        revealEls.forEach((el) => revealObserver.observe(el));
    </script>

    <!-- About Section -->
    <section class="py-16 animate__animated animate__fadeInUp">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h3 class="text-4xl font-bold text-gray-800 mb-4 animate__animated animate__fadeInDown">About Us</h3>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto animate__animated animate__fadeIn animate__delay-1s">At Maison de Pâte, we believe in the art of baking. Our master bakers use traditional techniques and the finest ingredients to create breads and pastries that delight the senses. From crusty baguettes to decadent éclairs, every item is made with passion.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center about-card animate__animated animate__flipInY">
                    <img src="images/products/freshbaked.jpeg" alt="Fresh Baked" class="w-16 h-16 mx-auto mb-4 rounded-full object-cover">
                    <h4 class="text-xl font-semibold mb-2">Fresh Daily</h4>
                    <p class="text-gray-600">Baked fresh every morning with organic ingredients.</p>
                </div>
                <div class="text-center about-card animate__animated animate__flipInY animate__delay-1s">
                    <img src="images/products/croissants.jpg" alt="Artisanal Quality" class="w-16 h-16 mx-auto mb-4 rounded-full object-cover">
                    <h4 class="text-xl font-semibold mb-2">Artisanal Quality</h4>
                    <p class="text-gray-600">Handcrafted with traditional methods and love.</p>
                </div>
                <div class="text-center about-card animate__animated animate__flipInY animate__delay-2s">
                    <img src="images/bg/bakeryspace.jpg" alt="Family Owned" class="w-16 h-16 mx-auto mb-4 rounded-full object-cover">
                    <h4 class="text-xl font-semibold mb-2">Family Owned</h4>
                    <p class="text-gray-600">A family tradition passed down through generations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-[#f3b93d] animate__animated animate__fadeInUp">
        <div class="container mx-auto px-4">
            <h3 class="text-4xl font-bold text-center text-gray-800 mb-12 animate__animated animate__bounceIn">Featured Products</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                <div class="text-center featured-item animate__animated animate__zoomIn">
                    <img src="images/products/baguettes.jpg" alt="Baguettes" class="w-full h-32 object-cover rounded-lg mb-4 shadow-lg">
                    <h4 class="text-lg font-semibold">Baguettes</h4>
                </div>
                <div class="text-center featured-item animate__animated animate__zoomIn animate__delay-1s">
                    <img src="images/products/Macarons.jpeg" alt="Macarons" class="w-full h-32 object-cover rounded-lg mb-4 shadow-lg">
                    <h4 class="text-lg font-semibold">Macarons</h4>
                </div>
                <div class="text-center featured-item animate__animated animate__zoomIn animate__delay-2s">
                    <img src="images/products/Mille-Feuille.jpg" alt="Mille-Feuille" class="w-full h-32 object-cover rounded-lg mb-4 shadow-lg">
                    <h4 class="text-lg font-semibold">Mille-Feuille</h4>
                </div>
                <div class="text-center featured-item animate__animated animate__zoomIn animate__delay-3s">
                    <img src="images/products/Pain au Chocolat.jpg" alt="Pain au Chocolat" class="w-full h-32 object-cover rounded-lg mb-4 shadow-lg">
                    <h4 class="text-lg font-semibold">Pain au Chocolat</h4>
                </div>
                <div class="text-center featured-item animate__animated animate__zoomIn animate__delay-4s">
                    <img src="images/products/Tarte Tatin.jpg" alt="Tarte Tatin" class="w-full h-32 object-cover rounded-lg mb-4 shadow-lg">
                    <h4 class="text-lg font-semibold">Tarte Tatin</h4>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

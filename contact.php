<?php
require_once 'includes/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $message_text = sanitize($_POST['message']);

    // Send email (note: mail() may not work without proper server config)
    $to = 'info@maisondepate.com';
    $subject = 'Contact Form Submission from ' . $name;
    $body = "Name: $name\nEmail: $email\n\n$message_text";
    $headers = "From: $email";

    if (mail($to, $subject, $body, $headers)) {
        $message = 'Thank you for your message! We will get back to you soon.';
    } else {
        $message = 'Failed to send message. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Maison de Pâte</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-center mb-8 animate__animated animate__fadeInDown">Contact Us</h1>
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md animate__animated animate__fadeInUp">
            <p class="text-lg text-gray-600 mb-6">We'd love to hear from you! Reach out to us for any questions, orders, or feedback.</p>
            <div class="space-y-6">
                <div class="flex items-start space-x-4 animate__animated animate__fadeInLeft">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#4c2b1b] mt-1"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.458-7.5 11.458S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                    <div>
                        <h3 class="text-xl font-semibold">Address</h3>
                        <p>123 Bakery Street<br>Paris, France 75001</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 animate__animated animate__fadeInLeft animate__delay-1s">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#4c2b1b] mt-1"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                    <div>
                        <h3 class="text-xl font-semibold">Phone</h3>
                        <p>+33 1 23 45 67 89</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 animate__animated animate__fadeInLeft animate__delay-2s">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#4c2b1b] mt-1"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                    <div>
                        <h3 class="text-xl font-semibold">Email</h3>
                        <p>info@maisondepate.com</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 animate__animated animate__fadeInLeft animate__delay-3s">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#4c2b1b] mt-1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div>
                        <h3 class="text-xl font-semibold">Hours</h3>
                        <p>Monday - Friday: 7:00 AM - 7:00 PM<br>Saturday - Sunday: 8:00 AM - 5:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md animate__animated animate__fadeInUp animate__delay-1s mt-8">
            <h2 class="text-2xl font-bold mb-4">Send us a Message</h2>
            <?php if ($message): ?>
                <p class="text-<?php echo strpos($message, 'Thank you') !== false ? 'green' : 'red'; ?>-500 mb-4"><?php echo $message; ?></p>
            <?php endif; ?>
            <form method="post" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        <span class="ml-2">Name</span>
                    </label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <span class="ml-2">Email</span>
                    </label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]">
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        <span class="ml-2">Message</span>
                    </label>
                    <textarea name="message" id="message" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#4c2b1b] focus:border-[#4c2b1b]"></textarea>
                </div>
                <button type="submit" class="bg-[#4c2b1b] text-white px-4 py-2 rounded hover:bg-[#3a1f14] transition duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                    Send Message
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FusionBuild Technologies Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mustardOrange: '#FFB400',
                        darkBlue: '#002147',
                        black: '#121212',
                        white: '#FFFFFF',
                    }
                }
            }
        };
    </script>
    <style>
        @media (max-width: 640px) {
            .hero-section {
                padding: 10px;
                text-align: center;
            }
            .features-section {
                padding: 10px;
            }
            .feature-box {
                padding: 12px;
                width: 100%;
            }
            .footer-text {
                font-size: 14px;
            }
        }
    </style>
</head>
<body class="bg-white flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-black shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <img src="{{ asset('images/fusion_build_logo_2.png') }}" alt="FusionBuild Logo" class="h-10">
            <button id="menu-btn" class="sm:hidden text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
            <div class="hidden sm:flex items-center space-x-4">
                <a href="/" class="text-white px-4">Home</a>
                <a href="#contact" class="text-white px-4">Contact</a>
                <a href="/login" class="bg-mustardOrange text-black px-4 py-2 rounded-lg font-bold">Login</a>
            </div>
        </div>
        <div id="mobile-menu" class="hidden sm:hidden bg-black text-white p-4 space-y-2">
            <a href="/" class="block px-4 py-2">Home</a>
            <a href="#contact" class="block px-4 py-2">Contact</a>
            <a href="/login" class="block bg-mustardOrange text-black px-4 py-2 rounded-lg font-bold">Login</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
    <header class="hero-section text-center py-24 sm:py-30 text-white px-6 bg-cover bg-center" style="background-image: url('{{ asset('images/background4.png') }}');">
    <h2 class="text-3xl sm:text-4xl font-bold">FusionBuild Technologies Finance</h2>
    <p class="mt-4 text-lg sm:text-xl">Fusion of Strength and Smart Solutions</p>
    <a href="#features" class="mt-6 inline-block bg-mustardOrange text-black px-6 py-3 rounded-lg font-bold shadow-2xl shadow-gray-900">
    Learn More
</a>
</header>

        <section id="features" class="features-section py-16 px-4 container mx-auto text-center">
    <h3 class="text-2xl sm:text-3xl font-bold mb-6 text-darkBlue">Features</h3>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="feature-box bg-mustardOrange p-6 rounded-lg shadow-md">
            <h4 class="text-lg sm:text-xl font-bold text-black">Fast Performance</h4>
            <p class="mt-2 text-black">Optimized for speed and efficiency.</p>
        </div>
        <div class="feature-box bg-mustardOrange p-6 rounded-lg shadow-md">
            <h4 class="text-lg sm:text-xl font-bold text-black">Secure</h4>
            <p class="mt-2 text-black">Built with Laravel’s security features.</p>
        </div>
        <div class="feature-box bg-mustardOrange p-6 rounded-lg shadow-md">
            <h4 class="text-lg sm:text-xl font-bold text-black">Easy to Use</h4>
            <p class="mt-2 text-black">Intuitive design and simple navigation.</p>
        </div>
    </div>
</section>

    </main>

    <!-- Footer -->
    <footer class="bg-black text-white text-center py-6 mt-auto">
    <p class="footer-text text-white">
    © 2025 <span class="text-[#FFB400] font-bold">FusionBuild Technologies.</span> All rights reserved.
</p>
    </footer>

    <!-- JavaScript for Menu Toggle -->
    <script>
        document.getElementById('menu-btn').addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>

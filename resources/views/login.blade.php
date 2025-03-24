<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FusionBuild Technologies Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mustardOrange: '#FFB400',
                        darkBlue: '#002147',
                        black: '#000000',
                        white: '#FFFFFF',
                    },
                    screens: {
                        xs: '480px', // Custom breakpoint for extra small devices
                    },
                }
            }
        };
    </script>
</head>
<body class="bg-white flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-black shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <img src="{{ asset('images/fusion_build_logo_2.png') }}" alt="FusionBuild Logo" class="h-8 sm:h-10">

            <!-- Hamburger Menu Button -->
            <button id="menu-btn" class="sm:hidden text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>

            <!-- Desktop Menu -->
            <div class="hidden sm:flex items-center space-x-4">
                <a href="/" class="text-white px-4">Home</a>
                <a href="#" class="text-white px-4">Contact</a>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div id="mobile-menu" class="hidden sm:hidden bg-black text-white p-4 space-y-2">
            <a href="/" class="block px-4 py-2">Home</a>
            <a href="#" class="block px-4 py-2">Contact</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center p-4">
        <div class="bg-darkBlue text-white p-6 sm:p-12 rounded-lg shadow-lg w-full max-w-[28rem]">

            <!-- Logo -->
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/fusion_build_logo_3.png') }}" 
                     alt="FusionBuild Logo" 
                     class="w-24 sm:w-32 md:w-40 h-auto pb-2">
            </div>

            <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-center mb-4">Login</h2>

            @if (session('success'))
                <p class="text-green-500 text-center mb-4">{{ session('success') }}</p>
            @endif

            @if ($errors->has('login_error'))
                <p class="text-red-500 text-center">{{ $errors->first('login_error') }}</p>
            @endif

            <!-- Login Form -->
            <form action="{{ route('LoginVerification') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-white text-sm sm:text-base">Email</label>
                    <input type="email" id="email" name="email" class="w-full p-2 text-sm sm:text-base rounded-lg text-black" required>
                </div>
                <div>
                    <label for="password" class="block text-white text-sm sm:text-base">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-2 text-sm sm:text-base rounded-lg text-black" required>
                </div>
                
                <!-- Forgot Password -->
                <div class="text-right text-xs sm:text-sm">
                    <a href="{{ route('ForgotPassword') }}" class="text-mustardOrange">Forgot Password?</a>
                </div>

                <button type="submit" class="w-full bg-mustardOrange text-black font-bold py-2 rounded-lg text-sm sm:text-base">
                    Login
                </button>
            </form>

            <p class="text-center mt-4 text-xs sm:text-sm">
                <a href="/register" class="text-white">
                    Don't have an account? <span class="text-mustardOrange">Register</span>
                </a>
            </p>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black text-white text-center py-4 sm:py-6 text-xs sm:text-sm">
        <p>Contact us at 
            <a href="mailto:support@mylaravelapp.com" class="text-mustardOrange">contact@fusionbuild.tech</a>
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - FusionBuild Technologies Finance</title>
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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white flex flex-col min-h-screen">
    <nav class="bg-black shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <img src="{{ asset('images/fusion_build_logo_2.png') }}" alt="FusionBuild Logo" class="h-10">
            <div class="flex items-center space-x-4">
                <a href="/" class="text-white px-4">Home</a>
                <a href="#" class="text-white px-4">Contact</a>
            </div>
        </div>
    </nav>
    
    <main class="flex-grow flex items-center justify-center">
        <div class="bg-darkBlue text-white p-12 rounded-lg shadow-lg w-full max-w-[30rem]">

            <h2 class="text-2xl font-bold text-center mb-4">Forgot Password</h2>
            <p class="text-center text-gray-300 mb-4">Enter your email to receive a password reset code.</p>

            <!-- Success Message -->
            @if (session('success'))
                <p class="text-green-500 text-center mb-4">{{ session('success') }}</p>
            @endif

            <!-- Error Message -->
            @if ($errors->any())
                <div class="text-red-500 text-center mb-4">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <p class="text-green-500 text-center mb-4">{{ session('status') }}</p>
            @endif

            <form action="{{ route('ForgotPasswordEmail') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-white">Email</label>
                    <input type="email" id="email" name="email" class="w-full p-2 rounded-lg text-black" required>
                </div>
                <button type="submit" class="w-full bg-mustardOrange text-black font-bold py-2 rounded-lg">Send Reset Code</button>
            </form>

            <p class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-mustardOrange">Back to Login</a>
            </p>
        </div>
    </main>
    
    <footer class="bg-black text-white text-center py-6 mt-auto">
        <p>Contact us at <a href="mailto:support@mylaravelapp.com" class="text-mustardOrange">support@mylaravelapp.com</a></p>
    </footer>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | MyLaravelApp</title>
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
<body class="bg-white flex items-center justify-center min-h-screen px-4">

    <div class="bg-darkBlue text-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-6">Register</h2>

        <form action="{{ route('registration') }}" method="POST">
            @csrf

            <!-- First Name & Last Name in a Row (Mobile Responsive) -->
            <div class="flex flex-col sm:flex-row sm:gap-4 mb-4">
                <div class="w-full sm:w-1/2">
                    <label class="block text-mustardOrange font-semibold">First Name</label>
                    <input type="text" name="firstname" value="{{ old('firstname') }}" required class="w-full p-3 rounded-lg text-black mt-1 capitalize">
                    @error('firstname')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="w-full sm:w-1/2 mt-3 sm:mt-0">
                    <label class="block text-mustardOrange font-semibold">Last Name</label>
                    <input type="text" name="lastname" value="{{ old('lastname') }}" required class="w-full p-3 rounded-lg text-black mt-1 capitalize">
                    @error('lastname')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-mustardOrange font-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full p-3 rounded-lg text-black mt-1">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-mustardOrange font-semibold">Password</label>
                <input type="password" name="password" required class="w-full p-3 rounded-lg text-black mt-1">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-mustardOrange font-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full p-3 rounded-lg text-black mt-1">
                @error('password_confirmation')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Smaller Register Button (Centered) -->
            <div class="flex justify-center mt-6">
                <button type="submit" class="w-2/3 bg-mustardOrange text-black font-bold py-2 px-6 rounded-lg hover:bg-yellow-500 transition">
                    Register
                </button>
            </div>
        </form>

        @if ($errors->any())
            <div class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="mt-4 text-center">Already have an account? 
            <a href="{{ route('login') }}" class="text-mustardOrange font-semibold">Login</a>
        </p>
    </div>

</body>
</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        function capitalizeInput(input) {
            input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
        }

        document.querySelectorAll("input[name='firstname'], input[name='lastname']").forEach(input => {
            input.addEventListener("input", function () {
                capitalizeInput(this);
            });
        });
    });
</script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification | MyLaravelApp</title>
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
<body class="bg-white flex items-center justify-center min-h-screen">

    <div class="bg-darkBlue text-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-6">Verify Your Email</h2>

        @if (session('error'))
            <p class="text-red-500 text-center">{{ session('error') }}</p>
        @endif
        @if (session('success'))
    <p class="text-green-500 text-center">{{ session('success') }}</p>
@endif


        @if ($errors->any())
            <div class="mb-4">
                @foreach ($errors->all() as $error)
                    <p class="text-red-500 text-center">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('RegistrationVerification') }}" method="POST">
            @csrf
            <div class="mb-4">
                <div class="mb-4"><label class="block text-mustardOrange font-semibold">Enter OTP</label></div>
                <input type="text" name="code" value="{{ old('otp') }}" required class="w-full p-2 rounded-lg text-black text-center" placeholder="Enter the OTP sent to your email.">
                @error('otp')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-mustardOrange text-black font-bold py-2 rounded-lg hover:bg-yellow-500">
                Verify OTP
            </button>
        </form>

        <p class="mt-4 text-center">Didn't receive the OTP?
            <a href="{{ route('ResendVerification') }}" class="text-mustardOrange font-semibold">Resend OTP</a>
        </p>
    </div>

</body>
</html>

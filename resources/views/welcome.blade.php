<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bank Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex flex-col justify-center items-center">
        <h1 class="text-5xl font-bold text-blue-700 mb-6">
            Bank Management System
        </h1>

        <p class="text-gray-600 mb-8">
            Secure • Reliable • Modern Banking
        </p>

        <div class="space-x-4">
            <a href="{{ route('login') }}"
               class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
               Login
            </a>

            <a href="{{ route('register') }}"
               class="px-6 py-3 bg-gray-800 text-white rounded-lg shadow hover:bg-gray-900">
               Register
            </a>
        </div>
    </div>

</body>
</html>

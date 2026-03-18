<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('login') }} - Pharma SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-primary-600">Pharma SaaS</h1>
                <p class="text-gray-500 mt-2">Intelligent Pharmacy Management</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('email') }}</label>
                    <input type="email" name="email" required value="{{ old('email') }}" 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                
                <div class="mb-6 flex items-center">
                    <input type="checkbox" name="remember" id="remember" 
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                </div>
                
                <button type="submit" class="w-full bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    {{ __('login') }}
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Demo: admin@pharmasaas.com / password
                </p>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <div class="flex justify-center space-x-2">
                <a href="{{ route('language.switch', 'fr') }}" class="px-3 py-1 text-sm rounded {{ app()->getLocale() === 'fr' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700' }}">FR</a>
                <a href="{{ route('language.switch', 'en') }}" class="px-3 py-1 text-sm rounded {{ app()->getLocale() === 'en' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700' }}">EN</a>
            </div>
        </div>
    </div>
</body>
</html>

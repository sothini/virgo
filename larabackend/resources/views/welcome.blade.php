<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body>
        <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4"
      >
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl">
          <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">Virgosoft API</h2>
            <p class="mt-2 text-sm text-gray-600">API IS UP</p>
          </div>
          <div>
            <a href="/api/documentation" 
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition">API Documentation</a>
          </div>
          <div class="text-center text-sm text-gray-600">
            Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
          </div>
         
    
         
        </div>
      </div>
    </body>
</html>

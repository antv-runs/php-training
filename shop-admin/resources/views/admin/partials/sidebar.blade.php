<aside class="w-64 bg-white border-r">
    <div class="p-4 border-b">
        <a href="{{ url('/admin') }}" class="text-lg font-bold">{{ config('app.name', 'Admin') }}</a>
    </div>

    <nav class="p-4">
        <ul class="space-y-2">
            <li><a href="{{ url('/admin') }}" class="block px-3 py-2 rounded hover:bg-gray-100">Dashboard</a></li>
            <li><a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">Products</a></li>
            <li><a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">Orders</a></li>
        </ul>
    </nav>
</aside>

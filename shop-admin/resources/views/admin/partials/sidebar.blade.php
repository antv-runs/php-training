<aside class="w-64 bg-white border-r min-h-screen">
    <div class="p-4 border-b">
        <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold">{{ config('app.name', 'Admin') }}</a>
    </div>

    <nav class="p-4">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 font-semibold' : '' }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.products.*') ? 'bg-gray-100 font-semibold' : '' }}">Products Management</a>
            </li>
            <li>
                <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.categories.*') ? 'bg-gray-100 font-semibold' : '' }}">Categories Management</a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 font-semibold' : '' }}">Users Management</a>
            </li>
        </ul>
    </nav>
</aside>

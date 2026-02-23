<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg font-semibold">Xin chào, {{ auth()->user() ? auth()->user()->name : 'User' }}!</h2>
                    <p class="mt-2 text-gray-600">Bạn vừa đăng nhập. Đây là trang dành cho user (không có quyền truy cập /admin).</p>

                    <div class="mt-4">
                        <h3 class="font-medium">Hành động nhanh</h3>
                        <ul class="list-disc list-inside mt-2 text-gray-700">
                            <li>Xem danh sách sản phẩm (nếu có)</li>
                            <li>Chỉnh sửa thông tin cá nhân</li>
                            <li>Xem lịch sử đơn hàng</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold">Admin Dashboard</h2>
            <p class="mt-2 text-gray-600">Chào mừng, {{ auth()->user() ? auth()->user()->name : 'Admin' }}.</p>

            <!-- Tabs -->
            <div class="mt-6">
                <div class="border-b">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button id="tab-content" class="py-2 px-3 border-b-2 border-indigo-500 text-sm font-medium text-indigo-600">Nội dung học</button>
                        <button id="tab-exercises" class="py-2 px-3 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700">Bài tập</button>
                        <button id="tab-other" class="py-2 px-3 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700">Khác</button>
                    </nav>
                </div>

                <div id="panel-content" class="mt-4">
                    <h3 class="text-lg font-semibold">Nội dung học</h3>
                    <ul class="list-disc list-inside mt-2 text-gray-700">
                        <li>CRUD Category</li>
                        <li>CRUD Product</li>
                        <li>Upload image</li>
                        <li>Validation</li>
                    </ul>
                </div>

                <div id="panel-exercises" class="hidden mt-4">
                    <h3 class="text-lg font-semibold">Bài tập</h3>
                    <ul class="list-disc list-inside mt-2 text-gray-700">
                        <li>Hoàn thiện admin: Category</li>
                        <li>Hoàn thiện admin: Product</li>
                        <li>Hoàn thiện admin: User</li>
                    </ul>
                </div>

                <div id="panel-other" class="hidden mt-4">
                    <h3 class="text-lg font-semibold">Khác</h3>
                    <p class="mt-2 text-gray-700">Các công cụ và thông tin bổ sung sẽ được cập nhật ở đây.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple tab switching
        const tabContent = document.getElementById('tab-content');
        const tabExercises = document.getElementById('tab-exercises');
        const tabOther = document.getElementById('tab-other');

        const panelContent = document.getElementById('panel-content');
        const panelExercises = document.getElementById('panel-exercises');
        const panelOther = document.getElementById('panel-other');

        function clearActive() {
            [tabContent, tabExercises, tabOther].forEach(t => {
                t.classList.remove('border-indigo-500','text-indigo-600');
                t.classList.add('border-transparent','text-gray-500');
            });
            [panelContent, panelExercises, panelOther].forEach(p => p.classList.add('hidden'));
        }

        tabContent.addEventListener('click', () => {
            clearActive();
            tabContent.classList.add('border-indigo-500','text-indigo-600');
            panelContent.classList.remove('hidden');
        });

        tabExercises.addEventListener('click', () => {
            clearActive();
            tabExercises.classList.add('border-indigo-500','text-indigo-600');
            panelExercises.classList.remove('hidden');
        });

        tabOther.addEventListener('click', () => {
            clearActive();
            tabOther.classList.add('border-indigo-500','text-indigo-600');
            panelOther.classList.remove('hidden');
        });
    </script>
@endsection

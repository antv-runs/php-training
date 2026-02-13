@extends('admin')

@section('title', "Category List")

@section('content')
<a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-2">Add Category</a>
<table class="table">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Action</th>
    </tr>
    @foreach ($categories as $category)
    <tr>
        <td>{{ $category->id }}</td>
        <td>{{ $category->name }}</td>
        <td>
            <a href="{{ route('admin.categories.destroy', $category) }}" class="btn btn-warning btn-sm">Edit</a>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                @csrf @method("DELETE")
                <button class="btn btn-danger btn-sm">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection

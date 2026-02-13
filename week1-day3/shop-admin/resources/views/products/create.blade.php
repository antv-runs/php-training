<!DOCTYPE html>
<html>
<head>
    <title>Thêm sản phẩm</title>
</head>
<body>

<h1>Thêm sản phẩm</h1>

<form action="{{ route('products.store') }}" method="POST">
    @csrf

    <div>
        <label>Tên:</label>
        <input type="text" name="name" value="{{ old('name') }}">
        @error('name') <p style="color:red">{{ $message }}</p> @enderror
    </div>

    <div>
        <label>Giá:</label>
        <input type="text" name="price" value="{{ old('price') }}">
        @error('price') <p style="color:red">{{ $message }}</p> @enderror
    </div>

    <div>
        <label>Mô tả:</label>
        <textarea name="description">{{ old('description') }}</textarea>
    </div>

    <button type="submit">Lưu</button>
</form>

<a href="{{ route('products.index') }}">Quay lại</a>

</body>
</html>

@extends('dashboard.layouts.admin-layout')
@section('title', 'Add Product Variant')

@section('content')
<div class="container">
    <h2>Add Product Variant</h2>

    <form action="{{ route('variants.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Product</label>
            <select name="product_id" class="form-control" required>
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Color (optional)</label>
            <select name="color_id" class="form-control">
                <option value="">-- Select Color --</option>
                @foreach($colors as $color)
                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Size (optional)</label>
            <select name="size_id" class="form-control">
                <option value="">-- Select Size --</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Add Variant</button>
    </form>
</div>
@endsection

@extends('dashboard.layouts.admin-layout')
@section('title', 'Product Variants')

@section('content')
<div class="container">
    <h2>Product Variants</h2>
    <a href="{{ route('variants.create') }}" class="btn btn-primary mb-3">Add Variant</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Color</th>
                <th>Size</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $variant)
                <tr>
                    <td>{{ $variant->product->name }}</td>
                    <td>{{ $variant->color->name ?? '-' }}</td>
                    <td>{{ $variant->size->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('variants.edit', $variant) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('variants.destroy', $variant) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete this variant?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $variants->links() }}
</div>
@endsection

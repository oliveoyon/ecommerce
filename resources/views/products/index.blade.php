@extends('dashboard.layouts.admin-layout')

@section('title', 'Product Management')

@section('content')
<section>
    <div class="container-fluid">
        <h2 class="mb-3">Products</h2>

        <button class="btn btn-primary mb-3" id="addProductBtn">Add Product</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Unit</th>
                    <th>Purchase Price</th>
                    <th>Sale Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                @foreach ($products as $product)
                <tr id="productRow-{{ $product->id }}">
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->brand->name ?? '' }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->subcategory->name }}</td>
                    <td>{{ $product->unit->name ?? '' }}</td>
                    <td>{{ $product->default_purchase_price }}</td>
                    <td>{{ $product->default_sale_price }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm editProductBtn" data-id="{{ $product->id }}">Edit</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <form id="productForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalTitle">Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <input type="hidden" id="product_id" name="product_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" class="form-control">
                                    <option value="">-- Select Brand --</option>
                                    @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subcategory</label>
                                <select name="subcategory_id" class="form-control" required>
                                    <option value="">-- Select Subcategory --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Unit</label>
                                <select name="unit_id" class="form-control">
                                    <option value="">-- Select Unit --</option>
                                    @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Purchase Price</label>
                                <input type="number" name="default_purchase_price" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sale Price</label>
                                <input type="number" name="default_sale_price" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="has_variants" id="has_variants" value="1">
                                    <label class="form-check-label" for="has_variants">Has Variants</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="has_expiry" id="has_expiry" value="1">
                                    <label class="form-check-label" for="has_expiry">Has Expiry</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control summernote" rows="3"></textarea>
                            </div>
                            <div id="variantSection" style="display: none;">
                                <hr>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Select Colors</label>
                                        <select name="variant_colors[]" class="form-select" multiple>
                                            @foreach ($colors as $color)
                                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Select Sizes</label>
                                        <select name="variant_sizes[]" class="form-select" multiple>
                                            @foreach ($sizes as $size)
                                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <button type="button" class="btn btn-secondary" id="generateVariants">Generate Variant Combinations</button>
                                    </div>
                                    <div class="col-md-12 mt-3" id="variantCombinationsContainer"></div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Product Images</label>
                                <input type="file" name="images[]" id="imageUpload" class="form-control" multiple>
                                <div class="mt-2 d-flex flex-wrap" id="imagePreviewContainer"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="saveProductBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    #imagePreviewContainer {
        background: #f8f9fa;
        padding: 8px;
        max-height: 180px;
        overflow: auto;
        border: 1px solid #dee2e6;
        border-radius: 6px;
    }

    .modal-fullscreen .modal-content {
        display: flex;
        flex-direction: column;
        height: 100vh;
    }

    .modal-fullscreen .modal-body {
        overflow-y: auto;
        flex-grow: 1;
        padding: 1.5rem;
        background-color: #fff;
    }

    /* Optional: add styling for image preview clarity */
    #imagePreviewContainer {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 10px;
        border-radius: 5px;
    }
</style>

@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
$(function () {
    var productModal = new bootstrap.Modal(document.getElementById('productModal'));

    $('#addProductBtn').click(function () {
        $('#productForm')[0].reset();
        $('#formMethod').val('POST');
        $('#product_id').val('');
        $('#productModalTitle').text('Add Product');
        $('#imagePreviewContainer').empty();
        $('select[name="subcategory_id"]').html('<option value="">-- Select Subcategory --</option>');
        productModal.show();
    });

    $('select[name="category_id"]').change(function () {
        var categoryId = $(this).val();
        if (!categoryId) {
            $('select[name="subcategory_id"]').html('<option value="">-- Select Subcategory --</option>');
            return;
        }
        $.get('/admin/subcategories-by-category/' + categoryId, function (data) {
            let options = '<option value="">-- Select Subcategory --</option>';
            data.forEach(function (subcat) {
                options += `<option value="${subcat.id}">${subcat.name}</option>`;
            });
            $('select[name="subcategory_id"]').html(options);
        });
    });

    $(document).on('click', '.deleteTempImage', function () {
        $(this).closest('.position-relative').remove();
    });

    $(document).on('click', '.deleteExistingImageBtn', function () {
        const imageId = $(this).data('id');
        const container = $(this).closest('.position-relative');

        if (confirm('Are you sure you want to delete this image?')) {
            $.ajax({
                url: `/admin/products/images/${imageId}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                    container.remove();
                },
                error: function () {
                    alert('Failed to delete image');
                }
            });
        }
    });

    $('#imageUpload').on('change', function () {
        const container = $('#imagePreviewContainer');
        container.empty();
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                container.append(`
                    <div class="position-relative me-2 mb-2">
                        <img src="${e.target.result}" class="img-thumbnail" style="height:100px;width:100px;object-fit:cover">
                        <span class="position-absolute top-0 end-0 btn btn-sm btn-danger deleteTempImage">&times;</span>
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        });
    });

    $('.editProductBtn').click(function () {
        var id = $(this).data('id');
        $.get('/admin/products/' + id + '/edit', function (data) {
            $('#product_id').val(data.id);
            $('#formMethod').val('PUT');
            $('#productModalTitle').text('Edit Product');

            $('[name="name"]').val(data.name);
            $('[name="brand_id"]').val(data.brand_id);
            $('[name="category_id"]').val(data.category_id).trigger('change');
            setTimeout(() => {
                $('[name="subcategory_id"]').val(data.subcategory_id);
            }, 300);
            $('[name="unit_id"]').val(data.unit_id);
            $('[name="default_purchase_price"]').val(data.default_purchase_price);
            $('[name="default_sale_price"]').val(data.default_sale_price);
            $('[name="has_variants"]').prop('checked', data.has_variants);
            $('[name="has_expiry"]').prop('checked', data.has_expiry);
            $('[name="description"]').val(data.description);

            $('#imagePreviewContainer').empty();
            data.images?.forEach(image => {
                $('#imagePreviewContainer').append(`
                    <div class="position-relative me-2 mb-2" data-id="${image.id}">
                        <img src="/storage/${image.image_path}" class="img-thumbnail" style="height:100px;width:100px;object-fit:cover">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 deleteExistingImageBtn" data-id="${image.id}">&times;</button>
                    </div>
                `);
            });

            productModal.show();
        });
    });

    $('#productModal').on('shown.bs.modal', function () {
        $('.summernote').summernote({
            height: 160,
            placeholder: 'Write product description here…',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });
    }).on('hidden.bs.modal', function () {
        $('.summernote').summernote('destroy');
    });

    $('#productForm').submit(function (e) {
        e.preventDefault();
        $('#saveProductBtn').prop('disabled', true);

        let id = $('#product_id').val();
        let method = $('#formMethod').val();
        let url = method === 'POST' ? '/admin/products' : `/admin/products/${id}`;

        let formData = new FormData(this);
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                location.reload();
            },
            error: function () {
                alert('Failed to save product. Please check your input.');
            },
            complete: function () {
                $('#saveProductBtn').prop('disabled', false);
            }
        });
    });

    function toggleVariantSection() {
    $('#variantSection').toggle($('#has_variants').is(':checked'));
    }
    $('#has_variants').on('change', toggleVariantSection);
    toggleVariantSection(); // initial load

    $('#generateVariants').on('click', function () {
        const colors = $('select[name="variant_colors[]"]').val();
        const sizes = $('select[name="variant_sizes[]"]').val();
        const container = $('#variantCombinationsContainer');
        container.empty();

        if (!colors?.length && !sizes?.length) {
            container.html('<div class="alert alert-warning">Please select at least color or size.</div>');
            return;
        }

        let html = '<table class="table table-bordered">';
        html += '<thead><tr><th>Color</th><th>Size</th><th>Price</th><th>Stock</th></tr></thead><tbody>';

        (colors?.length ? colors : [null]).forEach(color => {
            (sizes?.length ? sizes : [null]).forEach(size => {
                html += `
                    <tr>
                        <td>
                            <input type="hidden" name="variants[][color_id]" value="${color ?? ''}">
                            ${color ? $('select[name="variant_colors[]"] option[value="' + color + '"]').text() : '-'}
                        </td>
                        <td>
                            <input type="hidden" name="variants[][size_id]" value="${size ?? ''}">
                            ${size ? $('select[name="variant_sizes[]"] option[value="' + size + '"]').text() : '-'}
                        </td>
                        <td><input type="number" name="variants[][variant_price]" class="form-control" step="0.01"></td>
                        <td><input type="number" name="variants[][quantity_in_stock]" class="form-control" min="0"></td>
                    </tr>
                `;
            });
        });

        html += '</tbody></table>';
        container.html(html);
    });

});
</script>
@endpush

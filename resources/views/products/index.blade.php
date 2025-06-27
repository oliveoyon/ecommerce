@extends('dashboard.layouts.admin-layout')
@section('title', 'Product Management')

@section('content')
<section>
  <div class="container-fluid">
    <h2 class="mb-3">Products</h2>
    <button class="btn btn-primary mb-3" id="addProductBtn">Add Product</button>
    <table class="table table-bordered">
      <thead>
        <tr><th>Name</th><th>Brand</th><th>Category</th>
            <th>Subcategory</th><th>Unit</th><th>Purchase Price</th>
            <th>Sale Price</th><th>Actions</th></tr>
      </thead>
      <tbody>
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
          <input type="hidden" name="_method" id="formMethod" value="POST">
          <input type="hidden" id="product_id" name="product_id">

          <div class="modal-header">
            <h5 class="modal-title" id="productModalTitle">Add Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">
              {{-- Core fields --}}
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

              {{-- Variants/expiry switches --}}
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

              {{-- Description --}}
              <div class="col-md-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control summernote" rows="3"></textarea>
              </div>

              {{-- Variant section --}}
              <div class="col-12 mt-3" id="variantSection" style="display:none;">
                <h5>Variants</h5>
                <div class="row g-2 align-items-end">
                  <div class="col-md-4">
                    <label for="variantColor" class="form-label">Color</label>
                    <select id="variantColor" class="form-control">
                      <option value="">-- Select Color --</option>
                      @foreach ($colors as $color)
                      <option value="{{ $color->id }}">{{ $color->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label for="variantSize" class="form-label">Size</label>
                    <select id="variantSize" class="form-control">
                      <option value="">-- Select Size --</option>
                      @foreach ($sizes as $size)
                      <option value="{{ $size->id }}">{{ $size->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-4">
                    <button type="button" id="addVariantBtn" class="btn btn-primary">Add Variant</button>
                  </div>
                </div>

                <table class="table table-bordered mt-3" id="variantsTable">
                  <thead><tr><th>Color</th><th>Size</th><th>Action</th></tr></thead>
                  <tbody><!-- variants appended here --></tbody>
                </table>

                <input type="hidden" name="variants" id="variantsInput" />
              </div>

              {{-- Images --}}
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
    padding: 10px;
    max-height: 200px;
    overflow: auto;
    border: 1px solid #dee2e6;
    border-radius: 5px;
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
$(function () {
  const modal = new bootstrap.Modal('#productModal');
  let variants = [];

  function toggleVariantSection() {
    if ($('#has_variants').is(':checked')) {
      $('#variantSection').show();
    } else {
      $('#variantSection').hide();
      clearVariants();
    }
  }

  function renderVariants() {
    const tbody = $('#variantsTable tbody').empty();
    variants.forEach((v, idx) => {
      const c = $("#variantColor option[value='" + v.color_id + "']").text() || '';
      const s = $("#variantSize option[value='" + v.size_id + "']").text() || '';
      tbody.append(`<tr data-idx="${idx}">
                      <td>${c}</td><td>${s}</td>
                      <td><button type="button" class="btn btn-danger btn-sm removeVariantBtn">Remove</button></td>
                    </tr>`);
    });
    $('#variantsInput').val(JSON.stringify(variants));
  }

  function clearVariants() {
    variants = [];
    renderVariants();
  }

  $('#addProductBtn').click(function () {
    $('#productForm')[0].reset();
    $('#formMethod').val('POST');
    $('#product_id').val('');
    $('#productModalTitle').text('Add Product');
    $('#imagePreviewContainer').empty();
    clearVariants();
    toggleVariantSection();
    modal.show();
  });

  $('#has_variants').change(toggleVariantSection);

  $('#addVariantBtn').click(function () {
    const color_id = $('#variantColor').val(),
          size_id = $('#variantSize').val();
    if (!color_id && !size_id) {
      return alert('Select color or size');
    }
    if (variants.some(x => x.color_id == color_id && x.size_id == size_id)) {
      return alert('Variant already added');
    }
    variants.push({color_id, size_id});
    renderVariants();
    $('#variantColor, #variantSize').val('');
  });

  $(document).on('click', '.removeVariantBtn', function () {
    const idx = $(this).closest('tr').data('idx');
    variants.splice(idx, 1);
    renderVariants();
  });

  $('#productModal').on('shown.bs.modal', function () {
    $('.summernote').summernote({
      height: 160,
      placeholder: 'Write product description…',
      toolbar: [
        ['style', ['bold','italic','underline','clear']],
        ['para', ['ul','ol','paragraph']],
        ['insert', ['link']],
        ['view', ['codeview']]
      ]
    });
  }).on('hidden.bs.modal', function () {
    $('.summernote').summernote('destroy');
  });

  $('#imageUpload').on('change', function () {
    const cont = $('#imagePreviewContainer').empty();
    Array.from(this.files).forEach(f => {
      const fr = new FileReader();
      fr.onload = e => cont.append(`
        <div class="position-relative me-2 mb-2">
          <img src="${e.target.result}" class="img-thumbnail" style="height:100px;width:100px;object-fit:cover">
          <span class="position-absolute top-0 end-0 btn btn-sm btn-danger deleteTempImage">&times;</span>
        </div>`);
      fr.readAsDataURL(f);
    });
  });

  $(document).on('click', '.deleteTempImage', function () {
    $(this).closest('.position-relative').remove();
  });

  $('.editProductBtn').click(function () {
    const id = $(this).data('id');
    $.get(`/admin/products/${id}/edit`, function (data) {
      $('#product_id').val(data.id);
      $('#formMethod').val('PUT');
      $('#productModalTitle').text('Edit Product');
      $('[name="name"]').val(data.name);
      $('[name="brand_id"]').val(data.brand_id);
      $('[name="category_id"]').val(data.category_id).trigger('change');
      setTimeout(() => $('[name="subcategory_id"]').val(data.subcategory_id), 300);
      $('[name="unit_id"]').val(data.unit_id);
      $('[name="default_purchase_price"]').val(data.default_purchase_price);
      $('[name="default_sale_price"]').val(data.default_sale_price);
      $('#has_variants').prop('checked', data.has_variants);
      $('#has_expiry').prop('checked', data.has_expiry);
      clearVariants();
      if (data.variants) variants = data.variants.map(v => ({color_id: v.color_id || '', size_id: v.size_id || ''}));
      renderVariants();
      toggleVariantSection();
      $('.summernote').summernote('code', data.description || '');
      $('#imagePreviewContainer').empty();
      (data.images || []).forEach(img => {
        $('#imagePreviewContainer').append(`
          <div class="position-relative me-2 mb-2" data-id="${img.id}">
            <img src="/storage/${img.image_path}" class="img-thumbnail" style="height:100px;width:100px;object-fit:cover">
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 deleteExistingImageBtn" data-id="${img.id}">&times;</button>
          </div>`);
      });
      modal.show();
    });
  });

  $(document).on('click', '.deleteExistingImageBtn', function () {
    const imgId = $(this).data('id'),
          ctr = $(this).closest('.position-relative');
    if (confirm('Delete this image?')) {
      $.ajax({
        url: `/admin/products/images/${imgId}`,
        type: 'DELETE',
        data: {_token: '{{ csrf_token() }}'},
        success: () => ctr.remove(),
        error: () => alert('Failed delete')
      });
    }
  });

  $('#productForm').submit(function (e) {
    e.preventDefault();
    $('#saveProductBtn').prop('disabled', true);
    const id = $('#product_id').val(),
          method = $('#formMethod').val(),
          url = method === 'POST' ? '/admin/products' : `/admin/products/${id}`;
    const fd = new FormData(this);
    if (method === 'PUT') fd.append('_method', 'PUT');
    $.ajax({
      url, method: 'POST', data: fd,
      contentType: false, processData: false,
      success: () => location.reload(),
      error: () => alert('Save failed'),
      complete: () => $('#saveProductBtn').prop('disabled', false)
    });
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
});
</script>
@endpush

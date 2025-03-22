@extends('dashboard.layouts.admin-layout')

@section('title', 'Category Management')


@section('content')
    <section>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col">
                    <button class="btn btn-success btn-sm" id="createDistrictBtn"><i class="fas fa-plus-square mr-1"></i> Add
                        New Category</button>

                </div>
            </div>

            <!-- Categories Table -->
            <table class="table table-striped" id="districtsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- This is where you will loop through your categories -->
                    @foreach ($categories as $category)
                        <tr id="category-{{ $category->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->name }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm editDistrictBtn" data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}" data-status="{{ $category->status }}" data-img="{{ $category->category_img }}">Edit</button>
                                <button class="btn btn-danger btn-sm deleteDistrictBtn"
                                    data-id="{{ $category->id }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Fullscreen Modal for Create/Edit Category -->
        <div class="modal fade" id="districtModal" tabindex="-1" aria-labelledby="districtModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="districtModalLabel">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="districtForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="districtName" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="districtName" name="name">
                            </div>
                            <div class="mb-3">
                                <label for="category_img" class="form-label">Image</label>
                                <input type="file" class="form-control" id="category_img" name="category_img">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="mb-3" id="imageContainer">
                                <!-- The image will be dynamically inserted here -->
                            </div>
                            <div class="mb-3 text-end custombtn">
                                <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.getElementById('createDistrictBtn').addEventListener('click', function() {
            document.getElementById('districtForm').reset();
            document.getElementById('districtForm').setAttribute('action', '{{ route('categories.add') }}');
            document.getElementById('districtForm').setAttribute('method', 'POST');
            document.getElementById('districtModalLabel').textContent = 'Add New Category';
            var districtModal = new bootstrap.Modal(document.getElementById('districtModal'));
            districtModal.show();
        });

        document.querySelectorAll('.editDistrictBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var districtId = this.getAttribute('data-id');
                var districtName = this.getAttribute('data-name');
                var categoryStatus = this.getAttribute('data-status');
                var categoryImage = this.getAttribute('data-img'); // Get the image URL

                // Set the form inputs
                document.getElementById('districtName').value = districtName;
                document.getElementById('status').value = categoryStatus;
                
                // Set the image under the file input
                var imageContainer = document.getElementById('imageContainer'); // Where the image will be displayed
                if (categoryImage) {
                    // If there's an image URL, show it
                    imageContainer.innerHTML = `<img src="/storage/${categoryImage}" alt="Category Image" class="img-thumbnail" style="max-width: 150px;">`;
                } else {
                    imageContainer.innerHTML = ''; // Clear the image if not available
                }

                // Set the modal and form attributes
                document.getElementById('districtModalLabel').textContent = 'Edit Category';
                document.getElementById('districtForm').setAttribute('action',
                    '{{ route('categories.update', ':districtId') }}'.replace(':districtId', districtId));

                document.getElementById('districtForm').setAttribute('method', 'POST');

                // Add PUT method hidden input
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'PUT';
                document.getElementById('districtForm').appendChild(input);

                // Show the modal
                var districtModal = new bootstrap.Modal(document.getElementById('districtModal'));
                districtModal.show();
            });
        });


        document.querySelectorAll('.deleteDistrictBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var districtId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this category!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('categories.delete', ':districtId') }}'.replace(
                                ':districtId', districtId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('category-' + districtId).remove();

                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The category has been deleted.',
                                        icon: 'success',
                                        position: 'top-end',
                                        toast: true,
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true,
                                    });
                                } else {
                                    Swal.fire('Error!',
                                        'There was an error deleting the category.', 'error'
                                    );
                                }
                            });
                    }
                });
            });
        });

        document.getElementById('districtForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var submitButton = document.querySelector('#submitBtn');
            submitButton.disabled = true;

            var action = this.getAttribute('action');
            var method = this.getAttribute('method');
            var formData = new FormData(this);
            var districtModalElement = document.getElementById('districtModal');
            var districtModal = bootstrap.Modal.getInstance(districtModalElement);

            fetch(action, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (districtModal) {
                            districtModal.hide(); // Hide modal first
                        }

                        // Show Swal message for at least 2 seconds
                        let swalInstance = Swal.fire({
                            title: 'Success!',
                            text: method === 'POST' ? 'Category added successfully.' :
                                'Category updated successfully.',
                            icon: 'success',
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });

                        // Update UI instantly
                        setTimeout(() => {
                            if (method === 'POST') {
                                location.reload(); // Reload page after Swal message finishes
                            } else {
                                let districtRow = document.getElementById('category-' + data.id);
                                if (districtRow) {
                                    districtRow.querySelector('.category-name').textContent = formData
                                        .get('district_name');
                                }
                            }
                        }, 500); // Delay UI update slightly for smoothness

                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'There was an error processing your request.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Something went wrong!', 'error');
                })
                .finally(() => {
                    submitButton.disabled = false;
                });
        });


        // Assuming you have an image container to display the current image
const imageContainer = document.getElementById('imageContainer');

// File input element
const fileInput = document.getElementById('category_img');

// Event listener for file input change
fileInput.addEventListener('change', function(event) {
    const file = event.target.files[0];
    
    // Check if a file is selected
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Update the image source with the new file
            imageContainer.innerHTML = `<img src="${e.target.result}" alt="Category Image" class="img-thumbnail" style="max-width: 150px;">`;
        };
        
        // Read the selected file as a DataURL
        reader.readAsDataURL(file);
    }
});

    </script>
@endpush

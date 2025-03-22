@extends('dashboard.layouts.admin-layout')

@section('title', 'Subcategory Management')


@section('content')
    <section>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col">
                    <button class="btn btn-success btn-sm" id="createDistrictBtn"><i class="fas fa-plus-square mr-1"></i> Add
                        New Subcategory</button>

                </div>
            </div>

            <!-- Subcategories Table -->
            <table class="table table-striped" id="districtsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- This is where you will loop through your subcategories -->
                    @foreach ($subcategories as $subcategory)
                        <tr id="subcategory-{{ $subcategory->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $subcategory->name }}</td>
                            <td>{{ $subcategory->category->name }}</td>

                            <td>
                                <button class="btn btn-warning btn-sm editDistrictBtn" data-id="{{ $subcategory->id }}"
                                    data-name="{{ $subcategory->name }}" data-status="{{ $subcategory->status }}"  data-catid="{{ $subcategory->category_id }}">Edit</button>
                                <button class="btn btn-danger btn-sm deleteDistrictBtn"
                                    data-id="{{ $subcategory->id }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Fullscreen Modal for Create/Edit Subcategory -->
        <div class="modal fade" id="districtModal" tabindex="-1" aria-labelledby="districtModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="districtModalLabel">Add New Subcategory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="districtForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="districtName" class="form-label">Subcategory Name</label>
                                <input type="text" class="form-control" id="districtName" name="name">
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Cagegory Name</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
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
            document.getElementById('districtForm').setAttribute('action', '{{ route('subcategories.add') }}');
            document.getElementById('districtForm').setAttribute('method', 'POST');
            document.getElementById('districtModalLabel').textContent = 'Add New Subcategory';
            var districtModal = new bootstrap.Modal(document.getElementById('districtModal'));
            districtModal.show();
        });

        document.querySelectorAll('.editDistrictBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var districtId = this.getAttribute('data-id');
                var districtName = this.getAttribute('data-name');
                var categoryId = this.getAttribute('data-catid');
                var subcategoryStatus = this.getAttribute('data-status');

                document.getElementById('districtName').value = districtName;
                document.getElementById('category_id').value = categoryId;
                document.getElementById('status').value = subcategoryStatus;
                document.getElementById('districtModalLabel').textContent = 'Edit Subcategory';
                document.getElementById('districtForm').setAttribute('action',
                    '{{ route('subcategories.update', ':districtId') }}'.replace(':districtId', districtId));

                document.getElementById('districtForm').setAttribute('method', 'POST');

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'PUT';
                document.getElementById('districtForm').appendChild(input);

                var districtModal = new bootstrap.Modal(document.getElementById('districtModal'));
                districtModal.show();
            });
        });

        document.querySelectorAll('.deleteDistrictBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var districtId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this subcategory!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('subcategories.delete', ':districtId') }}'.replace(
                                ':districtId', districtId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('subcategory-' + districtId).remove();

                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The subcategory has been deleted.',
                                        icon: 'success',
                                        position: 'top-end',
                                        toast: true,
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true,
                                    });
                                } else {
                                    Swal.fire('Error!',
                                        'There was an error deleting the subcategory.', 'error'
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
                            text: method === 'POST' ? 'Subcategory added successfully.' :
                                'Subcategory updated successfully.',
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
                                let districtRow = document.getElementById('subcategory-' + data.id);
                                if (districtRow) {
                                    districtRow.querySelector('.subcategory-name').textContent = formData
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
    </script>
@endpush

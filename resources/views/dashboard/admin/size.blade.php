@extends('dashboard.layouts.admin-layout')

@section('title', 'Size Management')


@section('content')
    <section>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col">
                    <button class="btn btn-success btn-sm" id="createSizeBtn"><i class="fas fa-plus-square mr-1"></i> Add
                        New Size</button>

                </div>
            </div>

            <!-- Sizes Table -->
            <table class="table table-striped" id="sizesTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- This is where you will loop through your sizes -->
                    @foreach ($sizes as $size)
                        <tr id="size-{{ $size->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $size->name }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm editSizeBtn" data-id="{{ $size->id }}"
                                    data-name="{{ $size->name }}">Edit</button>
                                <button class="btn btn-danger btn-sm deleteSizeBtn"
                                    data-id="{{ $size->id }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Fullscreen Modal for Create/Edit Size -->
        <div class="modal fade" id="sizeModal" tabindex="-1" aria-labelledby="sizeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sizeModalLabel">Add New Size</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="sizeForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="sizeName" class="form-label">Size Name</label>
                                <input type="text" class="form-control" id="sizeName" name="name">
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
        document.getElementById('createSizeBtn').addEventListener('click', function() {
            document.getElementById('sizeForm').reset();
            document.getElementById('sizeForm').setAttribute('action', '{{ route('sizes.add') }}');
            document.getElementById('sizeForm').setAttribute('method', 'POST');
            document.getElementById('sizeModalLabel').textContent = 'Add New Size';
            var sizeModal = new bootstrap.Modal(document.getElementById('sizeModal'));
            sizeModal.show();
        });

        document.querySelectorAll('.editSizeBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var sizeId = this.getAttribute('data-id');
                var sizeName = this.getAttribute('data-name');

                document.getElementById('sizeName').value = sizeName;
                document.getElementById('sizeModalLabel').textContent = 'Edit Size';
                document.getElementById('sizeForm').setAttribute('action',
                    '{{ route('sizes.update', ':sizeId') }}'.replace(':sizeId', sizeId));

                document.getElementById('sizeForm').setAttribute('method', 'POST');

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'PUT';
                document.getElementById('sizeForm').appendChild(input);

                var sizeModal = new bootstrap.Modal(document.getElementById('sizeModal'));
                sizeModal.show();
            });
        });

        document.querySelectorAll('.deleteSizeBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var sizeId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this size!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('sizes.delete', ':sizeId') }}'.replace(
                                ':sizeId', sizeId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('size-' + sizeId).remove();

                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The size has been deleted.',
                                        icon: 'success',
                                        position: 'top-end',
                                        toast: true,
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true,
                                    });
                                } else {
                                    Swal.fire('Error!',
                                        'There was an error deleting the size.', 'error'
                                    );
                                }
                            });
                    }
                });
            });
        });

        document.getElementById('sizeForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var submitButton = document.querySelector('#submitBtn');
            submitButton.disabled = true;

            var action = this.getAttribute('action');
            var method = this.getAttribute('method');
            var formData = new FormData(this);
            var sizeModalElement = document.getElementById('sizeModal');
            var sizeModal = bootstrap.Modal.getInstance(sizeModalElement);

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
                        if (sizeModal) {
                            sizeModal.hide(); // Hide modal first
                        }

                        // Show Swal message for at least 2 seconds
                        let swalInstance = Swal.fire({
                            title: 'Success!',
                            text: method === 'POST' ? 'Size added successfully.' :
                                'Size updated successfully.',
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
                                let sizeRow = document.getElementById('size-' + data.id);
                                if (sizeRow) {
                                    sizeRow.querySelector('.size-name').textContent = formData
                                        .get('size_name');
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

@extends('dashboard.layouts.admin-layout')

@section('title', 'Color Management')


@section('content')
    <section>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col">
                    <button class="btn btn-success btn-sm" id="createColorBtn"><i class="fas fa-plus-square mr-1"></i> Add
                        New Color</button>

                </div>
            </div>

            <!-- Colors Table -->
            <table class="table table-striped" id="colorsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- This is where you will loop through your colors -->
                    @foreach ($colors as $color)
                        <tr id="color-{{ $color->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $color->name }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm editColorBtn" data-id="{{ $color->id }}"
                                    data-name="{{ $color->name }}">Edit</button>
                                <button class="btn btn-danger btn-sm deleteColorBtn"
                                    data-id="{{ $color->id }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Fullscreen Modal for Create/Edit Color -->
        <div class="modal fade" id="colorModal" tabindex="-1" aria-labelledby="colorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="colorModalLabel">Add New Color</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="colorForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="colorName" class="form-label">Color Name</label>
                                <input type="text" class="form-control" id="colorName" name="name">
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
        document.getElementById('createColorBtn').addEventListener('click', function() {
            document.getElementById('colorForm').reset();
            document.getElementById('colorForm').setAttribute('action', '{{ route('colors.add') }}');
            document.getElementById('colorForm').setAttribute('method', 'POST');
            document.getElementById('colorModalLabel').textContent = 'Add New Color';
            var colorModal = new bootstrap.Modal(document.getElementById('colorModal'));
            colorModal.show();
        });

        document.querySelectorAll('.editColorBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var colorId = this.getAttribute('data-id');
                var colorName = this.getAttribute('data-name');

                document.getElementById('colorName').value = colorName;
                document.getElementById('colorModalLabel').textContent = 'Edit Color';
                document.getElementById('colorForm').setAttribute('action',
                    '{{ route('colors.update', ':colorId') }}'.replace(':colorId', colorId));

                document.getElementById('colorForm').setAttribute('method', 'POST');

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'PUT';
                document.getElementById('colorForm').appendChild(input);

                var colorModal = new bootstrap.Modal(document.getElementById('colorModal'));
                colorModal.show();
            });
        });

        document.querySelectorAll('.deleteColorBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var colorId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this color!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('colors.delete', ':colorId') }}'.replace(
                                ':colorId', colorId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('color-' + colorId).remove();

                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The color has been deleted.',
                                        icon: 'success',
                                        position: 'top-end',
                                        toast: true,
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true,
                                    });
                                } else {
                                    Swal.fire('Error!',
                                        'There was an error deleting the color.', 'error'
                                    );
                                }
                            });
                    }
                });
            });
        });

        document.getElementById('colorForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var submitButton = document.querySelector('#submitBtn');
            submitButton.disabled = true;

            var action = this.getAttribute('action');
            var method = this.getAttribute('method');
            var formData = new FormData(this);
            var colorModalElement = document.getElementById('colorModal');
            var colorModal = bootstrap.Modal.getInstance(colorModalElement);

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
                        if (colorModal) {
                            colorModal.hide(); // Hide modal first
                        }

                        // Show Swal message for at least 2 seconds
                        let swalInstance = Swal.fire({
                            title: 'Success!',
                            text: method === 'POST' ? 'Color added successfully.' :
                                'Color updated successfully.',
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
                                let colorRow = document.getElementById('color-' + data.id);
                                if (colorRow) {
                                    colorRow.querySelector('.color-name').textContent = formData
                                        .get('color_name');
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

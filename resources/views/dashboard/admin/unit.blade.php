@extends('dashboard.layouts.admin-layout')

@section('title', 'Unit Management')


@section('content')
    <section>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col">
                    <button class="btn btn-success btn-sm" id="createUnitBtn"><i class="fas fa-plus-square mr-1"></i> Add
                        New Unit</button>

                </div>
            </div>

            <!-- Units Table -->
            <table class="table table-striped" id="unitsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- This is where you will loop through your units -->
                    @foreach ($units as $unit)
                        <tr id="unit-{{ $unit->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $unit->name }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm editUnitBtn" data-id="{{ $unit->id }}"
                                    data-name="{{ $unit->name }}">Edit</button>
                                <button class="btn btn-danger btn-sm deleteUnitBtn"
                                    data-id="{{ $unit->id }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Fullscreen Modal for Create/Edit Unit -->
        <div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="unitModalLabel">Add New Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="unitForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="unitName" class="form-label">Unit Name</label>
                                <input type="text" class="form-control" id="unitName" name="name">
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
        document.getElementById('createUnitBtn').addEventListener('click', function() {
            document.getElementById('unitForm').reset();
            document.getElementById('unitForm').setAttribute('action', '{{ route('units.add') }}');
            document.getElementById('unitForm').setAttribute('method', 'POST');
            document.getElementById('unitModalLabel').textContent = 'Add New Unit';
            var unitModal = new bootstrap.Modal(document.getElementById('unitModal'));
            unitModal.show();
        });

        document.querySelectorAll('.editUnitBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var unitId = this.getAttribute('data-id');
                var unitName = this.getAttribute('data-name');

                document.getElementById('unitName').value = unitName;
                document.getElementById('unitModalLabel').textContent = 'Edit Unit';
                document.getElementById('unitForm').setAttribute('action',
                    '{{ route('units.update', ':unitId') }}'.replace(':unitId', unitId));

                document.getElementById('unitForm').setAttribute('method', 'POST');

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'PUT';
                document.getElementById('unitForm').appendChild(input);

                var unitModal = new bootstrap.Modal(document.getElementById('unitModal'));
                unitModal.show();
            });
        });

        document.querySelectorAll('.deleteUnitBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var unitId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this unit!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('units.delete', ':unitId') }}'.replace(
                                ':unitId', unitId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('unit-' + unitId).remove();

                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The unit has been deleted.',
                                        icon: 'success',
                                        position: 'top-end',
                                        toast: true,
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true,
                                    });
                                } else {
                                    Swal.fire('Error!',
                                        'There was an error deleting the unit.', 'error'
                                    );
                                }
                            });
                    }
                });
            });
        });

        document.getElementById('unitForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var submitButton = document.querySelector('#submitBtn');
            submitButton.disabled = true;

            var action = this.getAttribute('action');
            var method = this.getAttribute('method');
            var formData = new FormData(this);
            var unitModalElement = document.getElementById('unitModal');
            var unitModal = bootstrap.Modal.getInstance(unitModalElement);

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
                        if (unitModal) {
                            unitModal.hide(); // Hide modal first
                        }

                        // Show Swal message for at least 2 seconds
                        let swalInstance = Swal.fire({
                            title: 'Success!',
                            text: method === 'POST' ? 'Unit added successfully.' :
                                'Unit updated successfully.',
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
                                let unitRow = document.getElementById('unit-' + data.id);
                                if (unitRow) {
                                    unitRow.querySelector('.unit-name').textContent = formData
                                        .get('unit_name');
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

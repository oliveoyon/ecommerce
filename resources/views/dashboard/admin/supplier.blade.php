@extends('dashboard.layouts.admin-layout')

@section('title', 'Supplier Management')


@section('content')
    <section>
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col">
                    <button class="btn btn-success btn-sm" id="createSupplierBtn"><i class="fas fa-plus-square mr-1"></i> Add
                        New Supplier</button>

                </div>
            </div>

            <!-- Suppliers Table -->
            <table class="table table-striped" id="suppliersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- This is where you will loop through your suppliers -->
                    @foreach ($suppliers as $supplier)
                        <tr id="supplier-{{ $supplier->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact_person }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->address }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm editSupplierBtn" 
                                    data-id="{{ $supplier->id }}" 
                                    data-name="{{ $supplier->name }}" 
                                    data-contact_person="{{ $supplier->contact_person }}" 
                                    data-phone="{{ $supplier->phone }}" 
                                    data-email="{{ $supplier->email }}" 
                                    data-address="{{ $supplier->address }}">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm deleteSupplierBtn"
                                    data-id="{{ $supplier->id }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Fullscreen Modal for Create/Edit Supplier -->
        <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="supplierModalLabel">Add New Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="supplierForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="supplierName" class="form-label">Supplier Name</label>
                                <input type="text" class="form-control" id="supplierName" name="name">
                            </div>
                            <div class="mb-3">
                                <label for="contactPerson" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contactPerson" name="contact_person">
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address"></textarea>
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
        document.getElementById('createSupplierBtn').addEventListener('click', function() {
            document.getElementById('supplierForm').reset();
            document.getElementById('supplierForm').setAttribute('action', '{{ route('suppliers.add') }}');
            document.getElementById('supplierForm').setAttribute('method', 'POST');
            document.getElementById('supplierModalLabel').textContent = 'Add New Supplier';
            var supplierModal = new bootstrap.Modal(document.getElementById('supplierModal'));
            supplierModal.show();
        });

        document.querySelectorAll('.editSupplierBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var supplierId = this.getAttribute('data-id');
                var supplierName = this.getAttribute('data-name');
                var contactPerson = this.getAttribute('data-contact_person');
                var phone = this.getAttribute('data-phone');
                var email = this.getAttribute('data-email');
                var address = this.getAttribute('data-address');

                document.getElementById('supplierName').value = supplierName;
                document.getElementById('contactPerson').value = contactPerson;
                document.getElementById('phone').value = phone;
                document.getElementById('email').value = email;
                document.getElementById('address').value = address;
                document.getElementById('supplierModalLabel').textContent = 'Edit Supplier';
                document.getElementById('supplierForm').setAttribute('action',
                    '{{ route('suppliers.update', ':supplierId') }}'.replace(':supplierId', supplierId));

                document.getElementById('supplierForm').setAttribute('method', 'POST');

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'PUT';
                document.getElementById('supplierForm').appendChild(input);

                var supplierModal = new bootstrap.Modal(document.getElementById('supplierModal'));
                supplierModal.show();
            });
        });

        document.querySelectorAll('.deleteSupplierBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var supplierId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this supplier!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonSupplier: '#3085d6',
                    cancelButtonSupplier: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ route('suppliers.delete', ':supplierId') }}'.replace(
                                ':supplierId', supplierId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('supplier-' + supplierId).remove();

                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The supplier has been deleted.',
                                        icon: 'success',
                                        position: 'top-end',
                                        toast: true,
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true,
                                    });
                                } else {
                                    Swal.fire('Error!',
                                        'There was an error deleting the supplier.', 'error'
                                    );
                                }
                            });
                    }
                });
            });
        });

        document.getElementById('supplierForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var submitButton = document.querySelector('#submitBtn');
            submitButton.disabled = true;

            var action = this.getAttribute('action');
            var method = this.getAttribute('method');
            var formData = new FormData(this);
            var supplierModalElement = document.getElementById('supplierModal');
            var supplierModal = bootstrap.Modal.getInstance(supplierModalElement);

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
                        if (supplierModal) {
                            supplierModal.hide(); // Hide modal first
                        }

                        // Show Swal message for at least 2 seconds
                        let swalInstance = Swal.fire({
                            title: 'Success!',
                            text: method === 'POST' ? 'Supplier added successfully.' :
                                'Supplier updated successfully.',
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
                                let supplierRow = document.getElementById('supplier-' + data.id);
                                if (supplierRow) {
                                    supplierRow.querySelector('.supplier-name').textContent = formData
                                        .get('supplier_name');
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

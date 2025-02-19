<div class="work-position-manager">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Manage Work Positions</h2>
        <button
            onclick="openAddModal()"
            class="btn btn-info btn-sm">
            Add New Position
        </button>
    </div>
    <!-- List of Work Positions -->
    @if($workPositions->isEmpty())
        <div class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-3 rounded relative" role="alert">
            <p class="font-bold">No applicants found</p>
            <p class="text-sm">There are currently no registered applicants in the system.</p>
        </div>
        <ul class="overflow-x-auto">
            <div class="overflow-x-auto">
                @else
                    <table class="table table-xs min-w-full bg-white">
                        <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Position Name
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Position Description
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody id="positionsTableBody">
                        @foreach($workPositions as $position)
                            <tr data-id="{{ $position->id }}">
                                <td class="py-2 px-4 border-b border-gray-200">{{ $position->name }}</td>
                                <td class="py-2 px-4 border-b border-gray-200">{{ $position->description }}</td>
                                <td class="py-2 px-4 border-b border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <button
                                            onclick="openEditModal({{ $position->id }}, '{{ $position->name }}', '{{ $position->description }}')"
                                            class="text-blue-500 hover:underline edit-btn">Edit
                                        </button>

                                        <form method="POST" action="{{ route('work-position.destroy', $position->id) }}"
                                              class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-500 hover:underline delete-btn"
                                                    data-id="{{ $position->id }}">Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
                {{ $registrations->links() }}
            </div>
        </ul>

        <!-- Add Position Modal -->
        <dialog id="addModal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Add New Position</h3>
                <form id="addForm" method="POST" action="{{ route('work-position.store') }}">
                    @csrf
                    <div class="form-control">
                        <label class="label" for="addName">
                            <span class="label-text">Position Name</span>
                        </label>
                        <input type="text" id="addName" name="name" class="input input-bordered" required>
                    </div>
                    <div class="form-control">
                        <label class="label" for="addDescription">
                            <span class="label-text">Description</span>
                        </label>
                        <textarea id="addDescription" name="description" class="textarea textarea-bordered"
                                  required></textarea>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-primary">Add Position</button>
                        <button type="button" onclick="closeAddModal()" class="btn">Cancel</button>
                    </div>
                </form>
            </div>
        </dialog>

        <!-- Daisy UI Confirmation Modal -->
        <dialog id="deleteModal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Confirm Deletion</h3>
                <p class="py-4">Are you sure you want to delete this position? This action cannot be undone.</p>
                <div class="modal-action">
                    <form method="dialog">
                        <button id="confirmDelete" class="btn btn-error">Delete</button>
                        <button id="cancelDelete" class="btn">Cancel</button>
                    </form>
                </div>
            </div>
        </dialog>

        <!-- Edit Modal -->
        <dialog id="editModal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Edit Position</h3>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-control">
                        <label class="label" for="editName">
                            <span class="label-text">Position Name</span>
                        </label>
                        <input type="text" id="editName" name="name" class="input input-bordered" required>
                    </div>
                    <div class="form-control">
                        <label class="label" for="editDescription">
                            <span class="label-text">Description</span>
                        </label>
                        <textarea id="editDescription" name="description" class="textarea textarea-bordered"
                                  required></textarea>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button id="cancelEdit" class="btn">Cancel</button>
                    </div>
                </form>
            </div>
        </dialog>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const deleteButtons = document.querySelectorAll('.delete-btn');
                const modal = document.getElementById('deleteModal');
                const confirmDeleteBtn = document.getElementById('confirmDelete');
                const cancelDeleteBtn = document.getElementById('cancelDelete');
                let currentDeleteId;

                // Check for session messages and show toasts
                @if(session('success'))
                showToast('success', "{{ session('success') }}");
                @endif

                @if(session('error'))
                showToast('error', "{{ session('error') }}");
                @endif

                deleteButtons.forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        currentDeleteId = this.dataset.id;
                        modal.showModal();
                    });
                });

                confirmDeleteBtn.addEventListener('click', function () {
                    if (currentDeleteId) {
                        deletePosition(currentDeleteId);
                    }
                    modal.close();
                });

                cancelDeleteBtn.addEventListener('click', function () {
                    modal.close();
                });

                function deletePosition(id) {
                    fetch(`{{ route('work-position.destroy', '') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                removeTableRow(id);
                                showToast('success', data.message || 'Position deleted successfully');
                            } else {
                                showToast('error', data.message || 'An error occurred while deleting the position');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('error', 'An error occurred. Please try again.');
                        });
                }

                function removeTableRow(id) {
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.remove();
                    }
                }

                // Edit modal functionality
                const modalEdit = document.getElementById('editModal');
                const form = document.getElementById('editForm');
                const nameInput = document.getElementById('editName');
                const descriptionInput = document.getElementById('editDescription');
                const cancelEditBtn = document.getElementById('cancelEdit');

                function openEditModal(id, name, description) {
                    form.action = `{{ route('work-position.update', '') }}/${id}`;
                    nameInput.value = name;
                    descriptionInput.value = description;

                    modalEdit.showModal();
                }

                function closeEditModal() {
                    modalEdit.close();
                }

                cancelEditBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    closeEditModal();
                });

                // Form submission
                document.getElementById('editForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                console.log('Position updated successfully:', data.position);
                                showToast('success', data.message || 'Position updated successfully');
                                // Update the table row with new data
                                updateTableRow(data.position);
                                closeEditModal();
                            } else {
                                showToast('error', data.message || 'An error occurred. Please try again.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('error', 'An error occurred. Please try again.');
                        })
                        .finally(() => {
                            closeEditModal();
                        });
                });

                function updateTableRow(position) {
                    const row = document.querySelector(`tr[data-id="${position.id}"]`);
                    if (row) {
                        row.querySelector('td:nth-child(1)').textContent = position.name;
                        row.querySelector('td:nth-child(2)').textContent = position.description;
                    }
                }

                // Add Position modal functionality
                const addModal = document.getElementById('addModal');
                const addForm = document.getElementById('addForm');

                window.openAddModal = function () {
                    addModal.showModal();
                }

                window.closeAddModal = function () {
                    addModal.close();
                }

// Form submission for adding a new position
                addForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                showToast('success', data.message || 'Position added successfully');
                                // Add the new position to the table
                                addTableRow(data.position);
                                closeAddModal();
                                // Reset the form
                                addForm.reset();
                            } else {
                                showToast('error', data.message || 'An error occurred. Please try again.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('error', 'An error occurred. Please try again.');
                        });
                });

                function addTableRow(position) {
                    const tbody = document.getElementById('positionsTableBody');
                    const newRow = document.createElement('tr');
                    newRow.setAttribute('data-id', position.id);
                    newRow.innerHTML = `
        <td class="py-2 px-4 border-b border-gray-200">${position.name}</td>
        <td class="py-2 px-4 border-b border-gray-200">${position.description}</td>
        <td class="py-2 px-4 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <button
                    onclick="openEditModal(${position.id}, '${position.name}', '${position.description}')"
                    class="text-blue-500 hover:underline edit-btn">Edit
                </button>
                <form method="POST" action="{{ route('work-position.destroy', '') }}/${position.id}" class="inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="text-red-500 hover:underline delete-btn" data-id="${position.id}">Delete</button>
                </form>
            </div>
        </td>
    `;
                    if (tbody.firstChild) {
                        tbody.insertBefore(newRow, tbody.firstChild);
                    } else {
                        tbody.appendChild(newRow);
                    }

                }

                function showToast(type, message) {
                    Toastify({
                        text: message,
                        duration: 2500,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: type === 'success'
                                ? 'linear-gradient(135deg, #4CAF50, #8BC34A, #2E7D32)'
                                : 'linear-gradient(135deg, #F44336, #FF9800, #B71C1C)',
                        },
                        stopOnFocus: true,
                    }).showToast();
                }

                // Make openEditModal globally accessible
                window.openEditModal = openEditModal;
            });
        </script>

</div>

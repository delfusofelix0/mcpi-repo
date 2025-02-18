<div class="applicant-list-manager">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Applicant List</h2>
        <input type="text" id="general-search" placeholder="Search..." class="input input-bordered w-full max-w-xs">
    </div>

    @if($registrations->isEmpty())
        <div class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-3 rounded relative" role="alert">
            <p class="font-bold">No applicants found</p>
            <p class="text-sm">There are currently no registered applicants in the system.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table id="applicant-table" class="table table-xs min-w-full bg-white">
                <thead>
                <tr>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <div class="flex items-center cursor-pointer" id="name-sort">
                            Name
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <input type="text" id="name-search" placeholder="Search name..." class="input input-bordered w-full max-w-xs mt-2 hidden">
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <select id="position-filter" class="select select-bordered w-full max-w-xs">
                            <option value="">All Positions</option>
                            @foreach($workPositions as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <select id="education-filter" class="select select-bordered w-full max-w-xs">
                            <option value="">All Education</option>
                            <option value="High School">High School</option>
                            <option value="Associate">Associate</option>
                            <option value="Bachelor">Bachelor</option>
                            <option value="Master">Master</option>
                            <option value="Doctorate">Doctorate</option>
                        </select>
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Applied At
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <select id="status-filter" class="select select-bordered w-full max-w-xs">
                            <option value="">All Status</option>
                            <option value="Viewed">Viewed</option>
                            <option value="Hired">Hired</option>
                            <option value="Option">Option</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Action
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($registrations as $registration)
                    <tr>
                        <td class="py-2 px-4 border-b border-gray-200">{{ $registration->firstname }} {{ $registration->lastname }}</td>
                        <td class="py-2 px-4 border-b border-gray-200">{{ $registration->email }}</td>
                        <td class="py-2 px-4 border-b border-gray-200">{{ $registration->workPosition->name ?? 'N/A'}}</td>
                        <td class="py-2 px-4 border-b border-gray-200">{{ $registration->highest_education }}</td>
                        <td class="py-2 px-4 border-b border-gray-200">{{ $registration->created_at->format('Y-m-d H:i') }}</td>
                        <td id="status-cell-{{ $registration->id }}" class="py-2 px-4 border-b border-gray-200">{{ $registration->status }}</td>
                        <td class="py-2 px-4 border-b border-gray-200">
                            <div class="flex flex-col space-y-2">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('applicant.view', $registration->id) }}" class="text-green-500 hover:underline">
                                        View
                                    </a>
                                    @can('edit applicant', $registration)
                                        <label for="status-modal-{{ $registration->id }}" class="text-blue-500 hover:underline cursor-pointer">
                                            Edit
                                        </label>
                                    @endcan
                                    @can('delete applicant', $registration)
                                        <label for="delete-modal-{{ $registration->id }}" class="text-red-500 hover:underline cursor-pointer">
                                            Delete
                                        </label>
                                    @endcan
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @foreach($registrations as $registration)
                <!-- Modal -->
                <input type="checkbox" id="status-modal-{{ $registration->id }}" class="modal-toggle" />
                <div class="modal">
                    <div class="modal-box">
                        <h3 class="font-bold text-lg">Update {{ $registration->firstname }} {{ $registration->lastname }}</h3>
                        <form id="statusForm-{{ $registration->id }}" class="py-4 flex flex-col space-y-4">
                            @csrf
                            <div class="flex flex-col">
                                <label for="status-{{ $registration->id }}" class="text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status-{{ $registration->id }}" name="status" class="select select-bordered w-full max-w-xs">
                                    <option value="Viewed" {{ $registration->status == 'Viewed' ? 'selected' : '' }}>Viewed</option>
                                    <option value="Hired" {{ $registration->status == 'Hired' ? 'selected' : '' }}>Hired</option>
                                    <option value="Option" {{ $registration->status == 'Option' ? 'selected' : '' }}>Option</option>
                                    <option value="Rejected" {{ $registration->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="modal-action">
                                <button type="submit" class="btn btn-primary">Update Status</button>
                                <label for="status-modal-{{ $registration->id }}" class="btn">Close</label>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <input type="checkbox" id="delete-modal-{{ $registration->id }}" class="modal-toggle" />
                <div class="modal">
                    <div class="modal-box">
                        <h3 class="font-bold text-lg">Confirm Deletion</h3>
                        <p class="py-4">Are you sure you want to delete the application for {{ $registration->firstname }} {{ $registration->lastname }}? This action cannot be undone.</p>
                        <div class="modal-action">
                            <form action="{{ route('applicant.destroy', $registration->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-error">Delete</button>
                            </form>
                            <label for="delete-modal-{{ $registration->id }}" class="btn">Cancel</label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $registrations->links() }}
        </div>
    @endif
</div>

<script>
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

    function checkTableEmpty() {
        const table = document.getElementById('applicant-table');
        const tableBody = table.querySelector('tbody');
        const container = table.closest('.overflow-x-auto');

        if (tableBody.children.length === 0) {
            // Table is empty, show the "No applicants found" message
            container.innerHTML = `
                <div class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-3 rounded relative" role="alert">
                    <p class="font-bold">No applicants found</p>
                    <p class="text-sm">There are currently no registered applicants in the system.</p>
                </div>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const generalSearch = document.getElementById('general-search');
        const nameSort = document.getElementById('name-sort');
        const nameSearch = document.getElementById('name-search');
        const positionFilter = document.getElementById('position-filter');
        const educationFilter = document.getElementById('education-filter');
        const statusFilter = document.getElementById('status-filter');
        const table = document.getElementById('applicant-table');
        const tbody = table.querySelector('tbody');

        let sortDirection = 1; // 1 for ascending, -1 for descending

        function filterTable() {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const searchTerm = generalSearch.value.toLowerCase();
            const nameSearchTerm = nameSearch.value.toLowerCase();
            const positionValue = positionFilter.value;
            const educationValue = educationFilter.value;
            const statusValue = statusFilter.value;

            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const email = row.cells[1].textContent.toLowerCase();
                const position = row.cells[2].textContent;
                const education = row.cells[3].textContent;
                const status = row.cells[5].textContent;

                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchesName = name.includes(nameSearchTerm);
                const matchesPosition = positionValue === '' || position === positionValue;
                const matchesEducation = educationValue === '' || education === educationValue;
                const matchesStatus = statusValue === '' || status === statusValue;

                row.style.display = (matchesSearch && matchesName && matchesPosition && matchesEducation && matchesStatus) ? '' : 'none';
            });
        }

        function sortTable() {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort((a, b) => {
                const nameA = a.cells[0].textContent.toLowerCase();
                const nameB = b.cells[0].textContent.toLowerCase();
                return nameA.localeCompare(nameB) * sortDirection;
            });
            rows.forEach(row => tbody.appendChild(row));
            sortDirection *= -1;
        }

        generalSearch.addEventListener('input', filterTable);
        nameSearch.addEventListener('input', filterTable);
        positionFilter.addEventListener('change', filterTable);
        educationFilter.addEventListener('change', filterTable);
        statusFilter.addEventListener('change', filterTable);

        nameSort.addEventListener('click', () => {
            sortTable();
            nameSearch.classList.toggle('hidden');
        });

        @foreach($registrations as $registration)
        // Status update form submission
        document.getElementById('statusForm-{{ $registration->id }}').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            formData.append('registration_id', '{{ $registration->id }}');

            fetch('{{ route('registration.status.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message);
                        document.getElementById('status-cell-{{ $registration->id }}').textContent = data.status;
                        document.getElementById('status-modal-{{ $registration->id }}').checked = false;
                    } else {
                        showToast('error', 'Failed to update status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred');
                });
        });

        // Delete confirmation
        const deleteForm{{ $registration->id }} = document.querySelector('form[action="{{ route('applicant.destroy', $registration->id) }}"]');
        if (deleteForm{{ $registration->id }}) {
            deleteForm{{ $registration->id }}.addEventListener('submit', function(e) {
                e.preventDefault();
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('success', data.message);
                            document.querySelector(`tr:has(#status-cell-{{ $registration->id }})`).remove();
                            document.getElementById('delete-modal-{{ $registration->id }}').checked = false;
                            checkTableEmpty();  // Check if table is empty after deletion
                        } else {
                            showToast('error', 'Failed to delete applicant');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('error', 'An error occurred');
                    });
            });
        }
        @endforeach
    });
</script>

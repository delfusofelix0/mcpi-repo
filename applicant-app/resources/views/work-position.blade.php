<div class="work-position-manager">
    @if (session('success'))
        <div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div id="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    <div class="flex justify-between items-center mb-4" >
        <h2 class="text-xl font-bold">Manage Work Positions</h2>
        <button
            class="btn btn-info btn-sm mx-10">
            Add New Position
        </button>
    </div>
    <!-- List of Work Positions -->
    <ul class="overflow-x-auto">
                <div class="overflow-x-auto">
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
                        <tbody>
                        @foreach($workPositions as $position)
                        <tr data-id="{{ $position->id }}">
                            <td class="py-2 px-4 border-b border-gray-200">{{ $position->name }}</td>
                            <td class="py-2 px-4 border-b border-gray-200">{{ $position->description }}</td>
                            <td class="py-2 px-4 border-b border-gray-200">
                                <div>
                                    <a href="{{ route('work-position.edit', $position->id) }}" class="text-blue-500 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('work-position.destroy', $position->id) }}" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-500 hover:underline delete-btn" data-id="{{ $position->id }}">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
    </ul>

    <!-- Confirmation Modal -->
    <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Confirm Deletion
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete this position? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDelete" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button type="button" id="cancelDelete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const modal = document.getElementById('deleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDelete');
            const cancelDeleteBtn = document.getElementById('cancelDelete');
            let currentForm;

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentForm = this.closest('form');
                    modal.classList.remove('hidden');
                });
            });

            confirmDeleteBtn.addEventListener('click', function() {
                if (currentForm) {
                    currentForm.submit();
                }
                modal.classList.add('hidden');
            });

            cancelDeleteBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
            });

            // New code for auto-hiding messages
            function hideMessage(elementId) {
                const element = document.getElementById(elementId);
                if (element) {
                    setTimeout(() => {
                        element.style.transition = 'opacity 1s';
                        element.style.opacity = '0';
                        setTimeout(() => {
                            element.style.display = 'none';
                        }, 1000);
                    }, 3000);
                }
            }

            hideMessage('successMessage');
            hideMessage('errorMessage');
        });
    </script>

    </div>

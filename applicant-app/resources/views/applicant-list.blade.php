<div class="mt-8">
    <h2 class="text-xl font-bold mb-4">Applicant List</h2>
    @if($registrations->isEmpty())
        <div class="    bg-gray-100 border border-gray-300 text-gray-700 px-4 py-3 rounded relative" role="alert">
            <p class="font-bold">No applicants found</p>
            <p class="text-sm">There are currently no registered applicants in the system.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="table table-xs min-w-full bg-white">
                <thead>
                <tr>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Name
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Position
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Education
                    </th>
                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Applied At
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
                        <td class="py-2 px-4 border-b border-gray-200 flex space-x-4">
                            <a href="{{ route('applicant.view', $registration->id) }}" class="text-blue-500 hover:underline">
                                View
                            </a>
                            @can('delete applicant', $registration)
                                <form action="{{ route('applicant.destroy', $registration->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

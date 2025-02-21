<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Applicant Form</title>
    <!-- ... other head elements ... -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @turnstileScripts()
</head>
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
<body class="bg-gray-300">
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 10-1.414 1.414L8.586 10l-2.934 2.934a1 1 0 101.414 1.414L10 11.414l2.934 2.934a1 1 0 001.414-1.414L11.414 10l2.934-2.934a1 1 0 000-1.414z"/></svg>
        </span>
    </div>
@endif
<div id="app" class="container mx-auto w-[896px] p-4">
    <form method="POST" action="{{ route('applicant.store') }}" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" enctype="multipart/form-data">
        @csrf
        <div class="bg-blue-600 text-white p-4 mb-6 -mx-8 -mt-6">
            <h1 class="text-2xl font-bold">Applicant Form</h1>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="position">Positions</label>
            <select name="position" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="position">
                <option value="" data-description="" {{ old('position') ? '' : 'selected' }}>Please choose..</option>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}" data-description="{{ $position->description }}" {{ old('position') == $position->id ? 'selected' : '' }}>
                        {{ $position->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('position')" class="mt-2" />
        </div>

        <div id="position-description" class="mt-2 text-gray-700">
            <!-- Description will be displayed here -->
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const positionSelect = document.getElementById('position');
                const descriptionDiv = document.getElementById('position-description');

                // Set the description on page load if a position is already selected
                const selectedOption = positionSelect.options[positionSelect.selectedIndex];
                const initialDescription = selectedOption.getAttribute('data-description');
                descriptionDiv.textContent = initialDescription || 'No description available.';

                positionSelect.addEventListener('change', function () {
                    const selectedOption = positionSelect.options[positionSelect.selectedIndex];
                    const description = selectedOption.getAttribute('data-description');
                    descriptionDiv.textContent = description || 'No description available.';
                });
            });
        </script>

        <hr class="mb-6 border-t-2 border-gray-300">
        <div class="my-4">
            <div class="grid grid-cols-2 items-center space-x-4">
                <div class="space-y-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="photo">Photo (2x2)</label>
                    <input type="file" id="photo" name="photo" accept="image/*"
                           class="file-input file-input-bordered file-input-sm w-full max-w-xs" onchange="previewImage(event)">
                    <button type="button" class="btn btn-primary btn-sm font-bold" onclick="window.my_modal_5.showModal()">Photo Instructions</button>
                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                </div>
                <div id="photo-preview" class="hidden">
                    <img id="uploaded-photo" src="" alt="Uploaded photo" class="w-24 h-24 object-cover">
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="firstname">Firstname</label>
                <input name="firstname" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="firstname" type="text" placeholder="Firstname">
                <x-input-error :messages="$errors->get('firstname')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="mi">Middle Initial</label>
                <input name="mi" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="mi" type="text" placeholder="Middle Initial">
                <x-input-error :messages="$errors->get('mi')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">Lastname</label>
                <input name="lastname" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="lastname" type="text" placeholder="Lastname">
                <x-input-error :messages="$errors->get('lastname')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="suffix">Suffix</label>
                <input name="suffix" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="suffix" type="text" placeholder="Suffix">
                <x-input-error :messages="$errors->get('suffix')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input name="email" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="email" type="email" placeholder="Email">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">Phone Number</label>
                <input name="phone" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="phone" type="tel" placeholder="Phone number">
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="religion">Religion</label>
                <input name="religion" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="religion" type="text" placeholder="Religion">
                <x-input-error :messages="$errors->get('religion')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="sogie">Sexual Orientation Gender Identity and Expression (SOGIE)</label>
                <select name="sogie" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="sogie">
                    <option value="">Please choose..</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="non-binary">Non-Binary</option>
                    <option value="others">Prefer not to say</option>
                </select>
                <x-input-error :messages="$errors->get('sogie')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="birthdate">Birth Date</label>
                <input name="birthdate" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="birthdate" type="date">
                <x-input-error :messages="$errors->get('birthdate')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="address">Address</label>
            <input name="address" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="address" type="text" placeholder="Prk./Brgy./City/Municipality">
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="highest_education">Highest Educational Attainment</label>
            <select name="highest_education" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="highest_education">
                <option value="">Please choose..</option>
                <option value="Elementary Level">Elementary Level</option>
                <option value="Elementary Graduate">Elementary Graduate</option>
                <option value="High School level">High School level</option>
                <option value="High School Graduate">High School Graduate</option>
                <option value="Vocational">Vocational</option>
                <option value="College Level">College Level</option>
                <option value="College Graduate">College Graduate</option>
                <option value="Post Graduate">Post Graduate</option>
            </select>
            <x-input-error :messages="$errors->get('highest_education')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="latest_company">Latest Company/Agency</label>
            <input name="latest_company" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="latest_company" type="text" placeholder="Latest Company/Agency">
            <x-input-error :messages="$errors->get('latest_company')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="present_position">Present/Latest Position</label>
                <input name="present_position" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="present_position" type="text" placeholder="Latest position">
                <x-input-error :messages="$errors->get('present_position')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status_employment">Status of Employment</label>
                <input name="status_employment" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="status_employment" type="text" placeholder="Status of Employment">
                <x-input-error :messages="$errors->get('status_employment')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="last_employment_date">Last Date of Employment</label>
                <input name="still_employed" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="last_employment_date" type="date">
                <x-input-error :messages="$errors->get('last_employment_date')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="eligibility">Eligibility</label>
                <input name="eligibility" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="eligibility" type="text" placeholder="Eligibility">
                <x-input-error :messages="$errors->get('eligibility')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-lg text-cyan-500 font-bold mb-2">Instruction: Kindly check if you belong to any of the following:</h3>
            <div>
                <div class="flex flex-col mt-2 gap-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="person_with_disability" value="1" id="disability-checkbox">
                        <span class="ml-2">Person with Disability - if yes, specify:</span>
                    </label>
                    <input id="disability-details" class="shadow appearance-none border border-gray-400 rounded w-full mt-2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="disability_details" placeholder="Please specify.." style="display: none;">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="pregnant" value="1">
                        <span class="ml-2">Pregnant</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="indigenous_community" value="1" id="indigenous-checkbox">
                        <span class="ml-2">Indigenous Community - if yes, specify:</span>
                    </label>
                    <input id="indigenous-details" class="shadow appearance-none border border-gray-400 rounded w-full mt-2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="indigenous_details" placeholder="Please specify.." style="display: none;">
                </div>
            </div>
        </div>

        <h3 class="text-lg text-cyan-500 font-bold mt-2">INSTRUCTION: UPLOAD FILE IN PDF FORMAT. IF THE DOCUMENTS HAVE MULTIPLE PAGES IT SHOULD BE IN ONE (1) PDF FILE.</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application Letter</label>
                <input class="file-input file-input-bordered file-input-sm w-full max-w-xs" id="document1" type="file" name="documents[application_letter]" accept=".pdf">
                <x-input-error :messages="$errors->get('documents.application_letter')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document2">Fully accomplished Personal Data Sheet (PDS) with recent passport-sized picture.*Required</label>
                <input class="file-input file-input-bordered file-input-sm w-full max-w-xs" id="document2" type="file" name="documents[personal_data_sheet]" accept=".pdf">
                <x-input-error :messages="$errors->get('documents.personal_data_sheet')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document3">Performance rating in the present position for one(1) year (if applicable).</label>
                <input class="file-input file-input-bordered file-input-sm w-full max-w-xs" id="document3" type="file" name="documents[performance_rating]" accept=".pdf">
                <x-input-error :messages="$errors->get('documents.performance_rating')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document4">Certificate of Eligibility/Rating or Professional License as proof of eligibility.*Required</label>
                <input class="file-input file-input-bordered file-input-sm w-full max-w-xs" id="document4" type="file" name="documents[eligibility_proof]" accept=".pdf">
                <x-input-error :messages="$errors->get('documents.eligibility_proof')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document5">Transcript of Records, including Diploma as proof of highest education attained.*Required</label>
                <input class="file-input file-input-bordered file-input-sm w-full max-w-xs" id="document5" type="file" name="documents[transcript]" accept=".pdf">
                <x-input-error :messages="$errors->get('documents.transcript')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document6">Certificate of Employment/Service Contract/Work Experience Sheet as proof of experience..*Required</label>
                <input class="file-input file-input-bordered file-input-sm w-full max-w-xs" id="document6" type="file" name="documents[employment_proof]" accept=".pdf">
                <x-input-error :messages="$errors->get('documents.employment_proof')" class="mt-2" />
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document7">Certificate/s of Training/Seminar/Conferences as proof.*Required</label>
                <input class="file-input file-input-bordered file-input-sm w-full max-w-xs" id="document7" type="file" name="documents[training_certificates]" accept=".pdf">
                <x-input-error :messages="$errors->get('documents.training_certificates')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <x-turnstile />
            <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
        </div>

        <div class="mt-6">
            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Submit
            </button>

        </div>
    </form>

    <dialog id="my_modal_5" class="modal overflow-y-auto py-10 modal-bottom sm:modal-middle">
        <form method="dialog" class="modal-box max-h-max m-auto">
            <div class="">
                <div class="">
                    <h2 class="mb-4 text-cyan-500 font-bold text-2xl">PLEASE READ!</h2>
                    <h4 class="uppercase font-bold">To avoid application disapproval, Please follow the photo requirements below:</h4>
                    <hr class="my-6 border-t-2 border-gray-300">

                    <h2 class="mb-4 uppercase font-bold">Below are sample of acceptable photo,</h2>
                    <div class="flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-4">
                        <div class="flex justify-center">
                            <img src="{{ asset('images/female-2x2.jpg') }}" alt="Photo Sample 1" class="w-64 h-[320px] object-cover">
                        </div>
                        <div class="h-[320px] border-l-2 border-dashed border-gray-300 hidden md:block"></div>
                        <div class="flex justify-center">
                            <img src="{{ asset('images/male-2x2.jpeg') }}" alt="Photo Sample 2" class="w-64 h-[320px] object-cover">
                        </div>
                    </div>

                    <hr class="my-6 border-t-2 border-gray-300">

                    <h4 class="font-bold">NOTE: Application will NOT be processed if</h4>
                    <ol class="list-decimal list-inside">
                        <li>Photo does not resemble applicant.</li>
                        <li>Applicant wears eyeglasses.</li>
                        <li>Background is not plain white.</li>
                        <li>Photo has shadows.</li>
                        <li>Ears are covered.</li>
                    </ol>
                </div>

                <div class="mt-6">
                    <div class="modal-action">
                        <button class="btn" onclick="closePhotoModal()">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </dialog>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('uploaded-photo').src = e.target.result;
                    document.getElementById('photo-preview').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function closePhotoModal() {
            window.my_modal_5.close();
        }

        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('disability-checkbox');
            const detailsInput = document.getElementById('disability-details');

            checkbox.addEventListener('change', function () {
                if (checkbox.checked) {
                    detailsInput.style.display = 'block';
                } else {
                    detailsInput.style.display = 'none';
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const indigenousCheckbox = document.getElementById('indigenous-checkbox');
            const indigenousDetailsInput = document.getElementById('indigenous-details');

            indigenousCheckbox.addEventListener('change', function () {
                if (indigenousCheckbox.checked) {
                    indigenousDetailsInput.style.display = 'block';
                } else {
                    indigenousDetailsInput.style.display = 'none';
                }
            });
        });
    </script>
</div>

</body>
</html>

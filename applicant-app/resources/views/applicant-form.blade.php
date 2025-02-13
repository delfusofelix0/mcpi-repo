<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
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
<div id="app" class="container mx-auto w-[896px] p-4">
    <form method="POST" action="{{ route('applicant.store') }}" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" enctype="multipart/form-data">
        @csrf
        <div class="bg-green-600 text-white p-4 mb-6 -mx-8 -mt-6">
            <h1 class="text-2xl font-bold">Application Form</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="firstname">Firstname</label>
                <input name="firstname" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="firstname" type="text" placeholder="Firstname">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="mi">Middle Initial</label>
                <input name="mi" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="mi" type="text" placeholder="Middle Initial">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">Lastname</label>
                <input name="lastname" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="lastname" type="text" placeholder="Lastname">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="suffix">Suffix</label>
                <input name="suffix" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="suffix" type="text" placeholder="Suffix">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input name="email" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="email" type="email" placeholder="Email">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">Phone Number</label>
                <input name="phone" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="phone" type="tel" placeholder="Phone number">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="religion">Religion</label>
                <input name="religion" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="religion" type="text" placeholder="Religion">
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
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="birthdate">Birth Date</label>
                <input name="birthdate" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="birthdate" type="date">
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="address">Address</label>
            <input name="address" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="address" type="text" placeholder="Prk./Brgy./City/Municipality">
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
        </div>

        <div class="mt-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="latest_company">Latest Company/Agency</label>
            <input name="latest_company" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="latest_company" type="text" placeholder="Latest Company/Agency">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="present_position">Present/Latest Position</label>
                <input name="present_position" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="present_position" type="text" placeholder="Latest position">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status_employment">Status of Employment</label>
                <input name="status_employment" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="status_employment" type="text" placeholder="Status of Employment">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="still_employed">Last Date of Employment</label>
                <input name="still_employed" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="still_employed" type="date">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="eligibility">Eligibility</label>
                <input name="eligibility" class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="eligibility" type="text" placeholder="Eligibility">
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-lg text-cyan-500 font-bold mb-2">Instruction: Kindly check if you belong to any of the following:</h3>
            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="person_with_disability" value="1">
                    <span class="ml-2">Person with Disability - if yes, specify:</span>
                </label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full mt-2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="disability_details" placeholder="Please specify..">
                <div class="flex flex-col mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="pregnant" value="1">
                        <span class="ml-2">Pregnant</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="indigenous_community" value="1">
                        <span class="ml-2">Indigenous Community - if yes, specify:</span>
                    </label>
                </div>
                <input class="shadow appearance-none border border-gray-400 rounded w-full mt-2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="indigenous_details" placeholder="Please specify..">
            </div>
        </div>

        <h3 class="text-lg text-cyan-500 font-bold mb-2">INSTRUCTION: UPLOAD FILE IN PDF FORMAT. IF THE DOCUMENTS HAVE MULTIPLE PAGES IT SHOULD BE IN ONE (1) PDF FILE.</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application Letter</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document1" type="file" name="documents[application_letter]" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document2">Fully accomplished Personal Data Sheet (PDS) with recent passport-sized picture.*Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document2" type="file" name="documents[personal_data_sheet]" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document3">Performance rating in the present position for one(1) year (if applicable).</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document3" type="file" name="documents[performance_rating]" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document4">Certificate of Eligibility/Rating or Professional License as proof of eligibility.*Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document4" type="file" name="documents[eligibility_proof]" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document5">Transcript of Records, including Diploma as proof of highest education attained.*Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document5" type="file" name="documents[transcript]" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document6">Certificate of Employment/Service Contract/Work Experience Sheet as proof of experience..*Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document6" type="file" name="documents[employment_proof]" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document7">Certificate/s of Training/Seminar/Conferences as proof.*Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document7" type="file" name="documents[training_certificates]" accept=".pdf">
            </div>
        </div>

        <div class="mt-6">
            <x-turnstile />
        </div>

        <div class="mt-6">
            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Submit
            </button>
        </div>
    </form>

    <!-- Modal -->
    <div
        x-cloak
        x-data="modal"
        x-show="show"
        class="fixed inset-0 overflow-y-auto flex items-center justify-center z-50"
    >
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline" x-text="isError ? 'Error' : 'Success'"></h3>
                        <div class="mt-2">
                            <template x-if="!isError">
                                <p class="text-sm text-gray-500" x-text="message"></p>
                            </template>
                            <template x-if="isError">
                                <ul class="list-disc pl-5">
                                    <template x-for="error in errorMessages" :key="error">
                                        <li x-text="error" class="text-sm text-gray-500"></li>
                                    </template>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="show = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('modal', () => ({
                show: false,
                message: '',
                isError: false,
                errorMessages: [],
                init() {
                    @if(session('success'))
                        this.show = true;
                        this.message = @json(session('success'));
                        this.isError = false;
                    @elseif($errors->any())
                        this.show = true;
                        this.isError = true;
                        this.errorMessages = @json($errors->all());
                    @endif
                }
            }))
        })
    </script>

</div>

</body>
</html>

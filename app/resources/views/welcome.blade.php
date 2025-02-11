<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- ... other head elements ... -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-300">
<div id="app" class="container mx-auto p-4">
    <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="bg-green-600 text-white p-4 mb-6 -mx-8 -mt-6">
            <h1 class="text-2xl font-bold">Registration Form</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="firstname">Firstname</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="firstname" type="text" placeholder="Firstname">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="mi">M.I</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="mi" type="text" placeholder="M.I">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="lastname">Lastname</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="lastname" type="text" placeholder="Lastname">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="suffix">Suffix</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="suffix" type="text" placeholder="Suffix">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="email" type="email" placeholder="Email">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">Phone Number</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="phone" type="tel" placeholder="Phone number">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="religion">Religion</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="religion" type="text" placeholder="Religion">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="sogie">Sexual Orientation Gender Identity and Expression (SOGIE)</label>
                <select class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="sogie">
                    <option value="">Please choose..</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="non-binary">Non-Binary</option>
                    <option value="others">Prefer not to say</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="birthdate">Birth Date</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="birthdate" type="date">
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="address">Address</label>
            <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="address" type="text" placeholder="Prk./Brgy./City/Municipality">
        </div>

        <div class="mt-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="highest_education">Highest Educational Attainment</label>
            <select class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="highest_education">
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
            <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="latest_company" type="text" placeholder="Latest Company/Agency">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="present_position">Present/Latest Position</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="present_position" type="text" placeholder="Latest position">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status_employment">Status of Employment</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="status_employment" type="text" placeholder="Status of Employment">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="still_employed">Last Date of Employment</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="still_employed" type="date">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="eligibility">Eligibility</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="eligibility" type="text" placeholder="Eligibility">
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-lg text-cyan-500 font-bold mb-2">Instruction: Kindly checked if you belong to any of the following:</h3>
            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox" name="person_with_disability">
                    <span class="ml-2">Person with Disability - if yes, specify:</span>
                </label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full mt-2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" placeholder="Please specify..">
                <div class="flex flex-col mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="pregnant">
                        <span class="ml-2">Pregnant</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox" name="indigenous_community">
                        <span class="ml-2">Person with Disability - if yes, specify:</span>
                    </label>
                </div>
                <input class="shadow appearance-none border border-gray-400 rounded w-full mt-2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" placeholder="Please specify..">
            </div>
        </div>

        <h3 class="text-lg text-cyan-500 font-bold mb-2">INSTRUCTION: UPLOAD FILE IN PDF FORMAT. IF THE DOCUMENTS HAVE MULTIPLE PAGES IT SHOULD BE IN ONE (1) PDF FILE.</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application Letter *Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document1" type="File" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application Letter *Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document1" type="File" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application Letter *Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document1" type="File" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application Letter *Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document1" type="File" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application Letter *Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document1" type="File" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application Letter *Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document1" type="File" accept=".pdf">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application Letter *Required</label>
                <input class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" id="document1" type="File" accept=".pdf">
            </div>
        </div>

        <div class="mt-6">
            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Submit
            </button>
        </div>
    </form>
</div>
</body>
</html>

<script setup>
import {onMounted, ref} from 'vue';
import {Head, useForm} from '@inertiajs/vue3';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';

const form = useForm({
    position: null,
    first_name: '',
    middle_initial: '',
    last_name: '',
    suffix: '',
    email: '',
    phone: '',
    religion: '',
    sogie: null,
    birth_date: null,
    address: '',
    highest_education: null,
    latest_company: '',
    present_position: '',
    status_employment: '',
    last_employment_date: null,
    eligibility: '',
    person_with_disability: false,
    disability_details: '',
    pregnant: false,
    indigenous_community: false,
    indigenous_details: '',
    application_letter: null,
    personal_data_sheet: null,
    performance_rating: null,
    eligibility_proof: null,
    transcript: null,
    employment_proof: null,
    training_certificates: null,
});

// Issue: sogie and education should return a string to satisfy form validation

const sogieOptions = ref([
    {label: 'Male', code: 'male'},
    {label: 'Female', code: 'female'},
    {label: 'Non-Binary', code: 'non-binary'},
    {label: 'Prefer not to say', code: 'others'},
]);

const educationOptions = ref([
    {label: 'Elementary Level', code: 'Elementary Level'},
    {label: 'Elementary Graduate', code: 'Elementary Graduate'},
    {label: 'High School level', code: 'High School level'},
    {label: 'High School Graduate', code: 'High School Graduate'},
    {label: 'Vocational', code: 'Vocational'},
    {label: 'College Level', code: 'College Level'},
    {label: 'College Graduate', code: 'College Graduate'},
    {label: 'Post Graduate', code: 'Post Graduate'},
]);

const submit = () => {
    form.post(route('applicant-form.store'), {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            console.log('Form submitted successfully');
            form.reset();
        },
        onError: (error) => {
            console.table(error);
        },
    });
};
</script>

<template>
    <Head title="Applicant Form"/>

    <div class="flex justify-center items-center min-h-screen bg-gray-100 p-4">
        <div class="w-full max-w-5xl bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-cyan-600 p-4">
                <h2 class="text-2xl font-bold text-white">Applicant Form</h2>
            </div>

            <div class="p-8">

                <form @submit.prevent="submit" class="p-fluid">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="firstname" class="block mb-2">First name</label>
                                <InputText id="firstname" class="w-full md:w-80" v-model="form.first_name"
                                           placeholder="Firstname"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="mi" class="block mb-2">Middle Initial</label>
                                <InputText id="mi" class="w-full md:w-80" v-model="form.middle_initial"
                                           placeholder="Middle Initial"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="lastname" class="block mb-2">Last name</label>
                                <InputText id="lastname" class="w-full md:w-80" v-model="form.last_name"
                                           placeholder="Lastname"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="suffix" class="block mb-2">Suffix</label>
                                <InputText id="suffix" class="w-full md:w-80" v-model="form.suffix"
                                           placeholder="Suffix"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="email" class="block mb-2">Email</label>
                                <InputText id="email" class="w-full md:w-80" v-model="form.email" type="email"
                                           placeholder="Email"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="phone" class="block mb-2">Phone Number</label>
                                <InputMask id="phone" class="w-full md:w-80" v-model="form.phone" mask="(999) 999-9999"
                                           placeholder="Phone number"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="religion" class="block mb-2">Religion</label>
                                <InputText id="religion" class="w-full md:w-80" v-model="form.religion"
                                           placeholder="Religion"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="sogie" class="block mb-2">Sexual Orientation Gender Identity and Expression
                                    (SOGIE)</label>
                                <Select id="sogie" class="w-full md:w-80" v-model="sogie" :options="sogieOptions"
                                        optionLabel="label"
                                        placeholder="Please choose.."/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="birthdate" class="block mb-2">Birth Date</label>
                                <DatePicker id="birthdate" class="w-full md:w-80" v-model="form.birth_date"
                                            showIcon fluid :showOnFocus="false"
                                            dateFormat="yy-mm-dd" placeholder="YYYY-MM-DD"/>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-field">
                                <label for="address" class="block mb-2">Address</label>
                                <InputText id="address" class="w-full md:w-80" v-model="form.address"
                                           placeholder="Prk./Brgy./City/Municipality"/>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-field">
                                <label for="highest_education" class="block mb-2">Highest Educational Attainment</label>
                                <Select id="highest_education" class="w-full md:w-80" v-model="form.highest_education"
                                        :options="educationOptions"
                                        optionLabel="label" placeholder="Please choose.."/>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-field">
                                <label for="latest_company" class="block mb-2">Latest Company/Agency</label>
                                <InputText id="latest_company" class="w-full md:w-80" v-model="form.latest_company"
                                           placeholder="Latest Company/Agency"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="present_position" class="block mb-2">Present/Latest Position</label>
                                <InputText id="present_position" class="w-full md:w-80" v-model="form.present_position"
                                           placeholder="Latest position"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="status_employment" class="block mb-2">Status of Employment</label>
                                <InputText id="status_employment" class="w-full md:w-80"
                                           v-model="form.status_employment"
                                           placeholder="Status of Employment"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="still_employed" class="block mb-2">Last Date of Employment</label>
                                <DatePicker id="still_employed" class="w-full md:w-80" v-model="form.last_employment_date"
                                            showIcon fluid :showOnFocus="false"
                                            dateFormat="yy-mm-dd" placeholder="YYYY-MM-DD"/>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="eligibility" class="block mb-2">Eligibility</label>
                                <InputText id="eligibility" class="w-full md:max-w-80" v-model="form.eligibility"
                                           placeholder="Eligibility"/>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg text-cyan-600 font-bold mb-4">Instruction: Kindly check if you belong to any
                            of the following:</h3>
                        <div class="space-y-4">
                            <div class="p-field-checkbox flex items-center">
                                <Checkbox id="person_with_disability" v-model="form.person_with_disability"
                                          :binary="true" class="mr-2"/>
                                <label for="person_with_disability">Person with Disability</label>
                            </div>
                            <div v-if="form.person_with_disability" class="p-field mt-2">
                                <InputText v-model="form.disability_details" placeholder="Please specify.."
                                           class="w-full md:w-80"/>
                            </div>
                            <div class="p-field-checkbox flex items-center">
                                <Checkbox id="pregnant" v-model="form.pregnant" :binary="true" class="mr-2"/>
                                <label for="pregnant">Pregnant</label>
                            </div>
                            <div class="p-field-checkbox flex items-center">
                                <Checkbox id="indigenous_community" v-model="form.indigenous_community" :binary="true"
                                          class="mr-2"/>
                                <label for="indigenous_community">Indigenous Community</label>
                            </div>
                            <div v-if="form.indigenous_community" class="p-field mt-2">
                                <InputText v-model="form.indigenous_details" placeholder="Please specify.."
                                           class="w-full md:w-80"/>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg text-cyan-600 font-bold mb-4 mt-4">INSTRUCTION: UPLOAD FILE IN PDF FORMAT. IF THE
                        DOCUMENTS HAVE MULTIPLE PAGES IT SHOULD BE IN ONE (1) PDF FILE.</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application
                                Letter</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document1" type="file" accept=".pdf"
                                @input="form.application_letter = $event.target.files[0]">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document2">Fully accomplished
                                Personal Data Sheet (PDS) with recent passport-sized picture.*Required</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document2" type="file" accept=".pdf"
                                @input="form.personal_data_sheet = $event.target.files[0]">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document3">Performance rating
                                in the present position for one(1) year (if applicable).</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document3" type="file" accept=".pdf"
                                @input="form.performance_rating = $event.target.files[0]">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document4">Certificate of
                                Eligibility/Rating or Professional License as proof of eligibility.*Required</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document4" type="file" accept=".pdf"
                                @input="form.eligibility_proof = $event.target.files[0]">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document5">Transcript of
                                Records, including Diploma as proof of highest education attained.*Required</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document5" type="file" accept=".pdf"
                                @input="form.transcript = $event.target.files[0]">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document6">Certificate of
                                Employment/Service Contract/Work Experience Sheet as proof of
                                experience.*Required</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document6" type="file" accept=".pdf"
                                @input="form.employment_proof = $event.target.files[0]">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document7">Certificate/s of
                                Training/Seminar/Conferences as proof.*Required</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document7" type="file" accept=".pdf"
                                @input="form.training_certificates = $event.target.files[0]">
                        </div>
                    </div>

                    <div class="mt-6">
                        <Button type="submit" label="Submit" class="mt-8 w-full"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.p-field {
    margin-bottom: 1rem;
}
</style>

<script setup>
import {ref} from 'vue';
import {Head, useForm} from '@inertiajs/vue3';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Message from 'primevue/message';
import InputMask from 'primevue/inputmask';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import VueTurnstile from 'vue-turnstile';
import {useToast} from 'primevue/usetoast';

const props = defineProps(['positions']);
// I like to move it, move it.
const visible = ref(false);
const photoPreview = ref(null);
const resetKey = ref(0);
const selectedPositionDescription = ref('');
const successDialogVisible = ref(false);
const turnstileRef = ref(null);
const turnstileSiteKey = import.meta.env.VITE_TURNSTILE_SITE_KEY;

const updatePositionDescription = (event) => {
    const selectedPosition = props.positions.find(pos => pos.id === event.value);
    selectedPositionDescription.value = selectedPosition ? selectedPosition.description : '';
};

const form = useForm({
    position: null,
    photo: null,
    first_name: '',
    middle_initial: null,
    last_name: '',
    suffix: null,
    email: '',
    phone: '',
    religion: '',
    sogie: '',
    birth_date: null,
    address: '',
    highest_education: '',
    course_major: '',
    has_previous_company: false,
    latest_company: '',
    present_position: '',
    years_of_service: '',
    last_employment_date: null,
    eligibility: '',
    person_with_disability: false,
    disability_details: '',
    pregnant: false,
    indigenous_community: false,
    indigenous_details: '',
    application_letter: null,
    personal_data_sheet: null,
    eligibility_proof: null,
    transcript: null,
    training_certificates: null,
    performance_rating: null,
    employment_proof: null,
    skip_performance_rating: false,
    skip_employment_proof: false,
    skip_eligibility_proof: false,
    'cf-turnstile-response': '',
});

const sogieOptions = ref([
    {label: 'Male', code: 'Male'},
    {label: 'Female', code: 'Female'},
]);

const educationOptions = ref([
    // add empty option
    {label: 'Please choose..', code: ''},
    {label: 'Vocational', code: 'Vocational'},
    {label: 'College Level', code: 'College Level'},
    {label: 'Masters Degree', code: 'Masters Degree'},
    {label: 'Doctors Degree', code: 'Doctors Degree'},
]);

const toast = useToast();

const closeSuccessDialog = () => {
    successDialogVisible.value = false;
};

const handlePhotoUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.photo = file;
        form.clearErrors('photo');
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        form.photo = null;
        photoPreview.value = null;
    }
};

// Function to scroll to the first error
const scrollToFirstError = () => {
    // Small delay to ensure DOM is updated
    setTimeout(() => {
        // Find the first element with an error (check both PrimeVue and custom error classes)
        const firstErrorField = document.querySelector('.p-invalid, .border-red-500');

        if (firstErrorField) {
            // Scroll to the error with some offset for better visibility
            firstErrorField.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }, 100);
};

const submit = (e) => {
    e.preventDefault()

    form.phone = form.phone.replace(/\D/g, '');
    form.post(route('applicant-form.store'), {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            form.photo = null;
            photoPreview.value = null;
            resetKey.value++;
            form.reset();
            successDialogVisible.value = true;
        },
        onError: (error) => {
            scrollToFirstError();
            if (turnstileRef.value) {
                turnstileRef.value.reset();
            }
            toast.add({severity: 'error', summary: 'Error', detail: 'Form submission failed', life: 5000});
        },
    });
};
</script>

<template>
    <Head title="Applicant Form"/>

    <Toast/>

    <div class="form-background">
        <div class="flex justify-center items-center min-h-screen  p-4">
            <div class="w-full max-w-5xl bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="bg-cyan-600 p-4">
                    <h2 class="text-2xl font-bold text-white">Applicant Form</h2>
                </div>

                <div class="p-8">

                    <form @submit.prevent="submit" class="p-fluid">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="col-12">
                                <div class="p-field">
                                    <label for="position" class="block mb-2">Position</label>
                                    <Select id="position"
                                            v-model="form.position"
                                            :options="props.positions"
                                            optionLabel="name"
                                            optionValue="id"
                                            placeholder="Please choose.."
                                            class="w-full"
                                            :class="{ 'p-invalid': form.errors.position }"
                                            @change="updatePositionDescription"
                                            @blur="form.clearErrors('position')"
                                    >
                                        <template #option="slotProps">
                                            <div>{{ slotProps.option.name }}</div>
                                        </template>
                                    </Select>
                                    <Message v-if="form.errors.position" severity="error" variant="simple" size="small">
                                        {{ form.errors.position }}
                                    </Message>
                                </div>
                                <div v-if="selectedPositionDescription" class="mt-2 p-2 bg-gray-100 rounded-md">
                                    <p class="text-sm text-gray-700">{{ selectedPositionDescription }}</p>
                                </div>
                                <div v-else class="mt-2 p-2 bg-gray-100 rounded-md">
                                    <p>No Description.</p>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4"/>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-20">
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="photo2x2" class="block mb-2">Upload Photo (2x2)</label>
                                    <div class="flex items-center gap-2">
                                        <Button label="Instruction" size="small" @click="visible = true"/>
                                        <label for="photo2x2" class="file-input-button">
                                            <span class="p-button-label">Choose File</span>
                                            <input
                                                class="hidden"
                                                id="photo2x2"
                                                type="file"
                                                accept="image/*"
                                                @input="handlePhotoUpload"
                                                @focus="form.clearErrors('photo')"
                                            />
                                        </label>
                                    </div>

                                    <Dialog v-model:visible="visible" modal header="PLEASE READ!"
                                            :style="{ width: '90%', maxWidth: '800px' }" class="overflow-y-auto">
                                        <template #header>
                                            <h2 class="text-cyan-500 font-bold text-3xl">PLEASE READ!</h2>
                                        </template>
                                        <div class="modal-box max-h-max m-auto">
                                            <h4 class="text-xl uppercase font-bold">To avoid application disapproval,
                                                Please
                                                follow the photo
                                                requirements below:</h4>
                                            <hr class="my-6 border-t-2 border-gray-300">

                                            <h2 class="mb-4 uppercase font-bold">Below are sample of acceptable
                                                photo,</h2>
                                            <div
                                                class="flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-4">
                                                <div class="flex justify-center">
                                                    <img src="/images/female.jpg" alt="Photo Sample 1"
                                                         class="w-64 h-[320px] object-cover">
                                                </div>
                                                <div
                                                    class="h-[320px] border-l-2 border-dashed border-gray-300 hidden md:block"></div>
                                                <div class="flex justify-center">
                                                    <img src="/images/male.jpg" alt="Photo Sample 2"
                                                         class="w-64 h-[320px] object-cover">
                                                </div>
                                            </div>

                                            <hr class="my-6 border-t-2 border-gray-300">

                                            <h4 class="font-bold text-red-500">NOTE: Application will NOT be processed
                                                if</h4>
                                            <ol class="list-decimal list-inside">
                                                <li>Photo does not resemble applicant.</li>
                                                <li>Applicant wears eyeglasses.</li>
                                                <li>Background is not plain white.</li>
                                                <li>Photo has shadows.</li>
                                                <li>Ears are covered.</li>
                                            </ol>
                                        </div>
                                    </Dialog>
                                    <Message v-if="form.errors.application_letter" severity="error" variant="simple"
                                             size="small">{{ form.errors.photo }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label class="block mb-2">Photo Preview</label>
                                    <div v-if="photoPreview" class="mt-2">
                                        <img :src="photoPreview" alt="Photo preview"
                                             class="max-w-full h-auto max-h-32 rounded-lg shadow-md"/>
                                    </div>
                                    <div v-else
                                         class="mt-2 bg-gray-100 border border-gray-300 rounded-lg p-4 text-center text-gray-500">
                                        No photo uploaded
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="firstname" class="block mb-2">First name</label>
                                    <InputText id="firstname" class="w-full" v-model="form.first_name"
                                               :class="{ 'p-invalid': form.errors.first_name }"
                                               placeholder="Firstname" @input="form.clearErrors('first_name')"/>
                                    <Message v-if="form.errors.first_name" severity="error" variant="simple"
                                             size="small">{{ form.errors.first_name }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="mi" class="block mb-2">Middle Initial</label>
                                    <InputText id="mi" class="w-full" v-model="form.middle_initial"
                                               placeholder="Middle Initial"
                                               @input="form.clearErrors('middle_initial')"/>
                                    <Message v-if="form.errors.middle_initial" severity="error" variant="simple"
                                             size="small">{{ form.errors.middle_initial }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="lastname" class="block mb-2">Last name</label>
                                    <InputText id="lastname" class="w-full" v-model="form.last_name"
                                               :class="{ 'p-invalid': form.errors.last_name }"
                                               placeholder="Lastname" @input="form.clearErrors('last_name')"/>
                                    <Message v-if="form.errors.last_name" severity="error" variant="simple"
                                             size="small">{{ form.errors.last_name }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="suffix" class="block mb-2">Suffix</label>
                                    <InputText id="suffix" class="w-full" v-model="form.suffix"
                                               placeholder="Suffix" @input="form.clearErrors('suffix')"/>
                                    <Message v-if="form.errors.suffix" severity="error" variant="simple" size="small">
                                        {{ form.errors.suffix }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="email" class="block mb-2">Email</label>
                                    <InputText id="email" class="w-full" v-model="form.email" type="email"
                                               :class="{ 'p-invalid': form.errors.email }"
                                               placeholder="Email" @input="form.clearErrors('email')"/>
                                    <Message v-if="form.errors.email" severity="error" variant="simple" size="small">
                                        {{ form.errors.email }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="phone" class="block mb-2">Phone Number</label>
                                    <InputMask id="phone" class="w-full" v-model="form.phone"
                                               :class="{ 'p-invalid': form.errors.phone }" mask="+63 9999999999"
                                               placeholder="+63 9XXXXXXXXX"
                                               @update:modelValue="() => form.clearErrors('phone')"/>
                                    <Message v-if="form.errors.phone" severity="error" variant="simple" size="small">
                                        {{ form.errors.phone }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="religion" class="block mb-2">Religion</label>
                                    <InputText id="religion" class="w-full" v-model="form.religion"
                                               placeholder="Religion" @input="form.clearErrors('religion')"/>
                                    <Message v-if="form.errors.religion" severity="error" variant="simple" size="small">
                                        {{ form.errors.religion }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="sogie" class="block mb-2">Gender</label>
                                    <Select id="sogie"
                                            v-model="form.sogie"
                                            :options="sogieOptions"
                                            optionLabel="label"
                                            optionValue="code"
                                            placeholder="Please choose.."
                                            class="w-full"
                                            :class="{ 'p-invalid': form.errors.sogie }"
                                            @change="form.clearErrors('sogie')"/>
                                    <Message v-if="form.errors.sogie" severity="error" variant="simple" size="small">
                                        {{ form.errors.sogie }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="birthdate" class="block mb-2">Birth Date</label>
                                    <DatePicker id="birthdate" class="w-full" v-model="form.birth_date"
                                                :invalid="!!form.errors.birth_date"
                                                dateFormat="mm-dd-yy" placeholder="MM-DD-YYYY"
                                                @update:modelValue="() => form.clearErrors('birth_date')"/>
                                    <Message v-if="form.errors.birth_date" severity="error" variant="simple"
                                             size="small">{{ form.errors.birth_date }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-field">
                                    <label for="address" class="block mb-2">Present Address</label>
                                    <InputText id="address" class="w-full" v-model="form.address"
                                               :class="{ 'p-invalid': form.errors.address }"
                                               placeholder="Prk./Brgy./City/Municipality"
                                               @input="form.clearErrors('address')"/>
                                    <Message v-if="form.errors.address" severity="error" variant="simple" size="small">
                                        {{ form.errors.address }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-field">
                                    <label for="highest_education" class="block mb-2">Highest Educational
                                        Attainment</label>
                                    <Select id="highest_education"
                                            v-model="form.highest_education"
                                            :options="educationOptions"
                                            optionLabel="label"
                                            optionValue="code"
                                            placeholder="Please choose.."
                                            class="w-full"
                                            :class="{ 'p-invalid': form.errors.highest_education }"
                                            @change="form.clearErrors('highest_education')"/>
                                    <Message v-if="form.errors.highest_education" severity="error" variant="simple"
                                             size="small">{{ form.errors.highest_education }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="course_major" class="block mb-2">Please specify your course and
                                        major.</label>
                                    <div v-if="!form.highest_education" class="mt-2">
                                        <InputText id="course_major" class="w-full" placeholder="Disabled" disabled/>
                                    </div>
                                    <div v-else>
                                        <InputText v-model="form.course_major"
                                                   :class="{ 'p-invalid': form.errors.course_major }"
                                                   placeholder="e.g., Computer Science - Software Engineering"
                                                   class="w-full"/>
                                        <Message v-if="form.errors.course_major " severity="error" variant="simple"
                                                 size="small">{{ form.errors.course_major }}
                                        </Message>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Company.-->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-20">
                            <div class="col-12">
                                <div class="p-field-checkbox flex items-center mb-4">
                                    <Checkbox id="has_previous_company" v-model="form.has_previous_company"
                                              :binary="true" class="mr-2"/>
                                    <label for="has_previous_company">I have previous work experience</label>
                                </div>
                            </div>
                        </div>

                        <div v-if="form.has_previous_company" class="grid grid-cols-1 md:grid-cols-2 gap-x-20">
                            <div class="col-12">
                                <div class="p-field">
                                    <label for="latest_company" class="block mb-2">Last/Latest Company/Agency</label>
                                    <InputText id="latest_company" class="w-full" v-model="form.latest_company"
                                               :class="{ 'p-invalid': form.errors.latest_company }"
                                               placeholder="Latest Company/Agency"/>
                                    <Message v-if="form.errors.latest_company" severity="error" variant="simple"
                                             size="small">{{ form.errors.latest_company }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="present_position" class="block mb-2">Last/Latest Position</label>
                                    <InputText id="present_position" class="w-full" v-model="form.present_position"
                                               :class="{ 'p-invalid': form.errors.present_position }"
                                               placeholder="Last/Latest position"/>
                                    <Message v-if="form.errors.present_position" severity="error" variant="simple"
                                             size="small">{{ form.errors.present_position }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="years_of_service" class="block mb-2">Years of Service (Previous
                                        Company)</label>
                                    <InputText id="years_of_service" class="w-full"
                                               v-model="form.years_of_service"
                                               :class="{ 'p-invalid': form.errors.years_of_service }"
                                               placeholder="Years of Service"/>
                                    <Message v-if="form.errors.years_of_service" severity="error" variant="simple"
                                             size="small">{{ form.errors.years_of_service }}
                                    </Message>
                                </div>
                            </div>
                            <div class="col-12 md:col-6">
                                <div class="p-field">
                                    <label for="still_employed" class="block mb-2">Last Date of Employment</label>
                                    <DatePicker id="still_employed" class="w-full" v-model="form.last_employment_date"
                                                :invalid="!!form.errors.last_employment_date"
                                                dateFormat="mm-dd-yy" placeholder="MM-DD-YYYY"/>
                                    <Message v-if="form.errors.last_employment_date" severity="error" variant="simple"
                                             size="small">{{ form.errors.last_employment_date }}
                                    </Message>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="eligibility" class="block mb-2">Eligibility</label>
                                <InputText id="eligibility" class="w-full" v-model="form.eligibility"
                                           placeholder="e.g., PRC, Civil Service, etc."/>
                                <Message v-if="form.errors.eligibility" severity="error" variant="simple"
                                         size="small">{{ form.errors.eligibility }}
                                </Message>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg text-cyan-600 font-bold mb-4">Instruction: Kindly check if you belong to
                                any
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
                                    <Checkbox id="indigenous_community" v-model="form.indigenous_community"
                                              :binary="true"
                                              class="mr-2"/>
                                    <label for="indigenous_community">Indigenous Community</label>
                                </div>
                                <div v-if="form.indigenous_community" class="p-field mt-2">
                                    <InputText v-model="form.indigenous_details" placeholder="Please specify.."
                                               class="w-full md:w-80"/>
                                </div>
                            </div>
                        </div>

                        <h3 class="text-lg text-cyan-600 font-bold mb-4 mt-4">INSTRUCTION: UPLOAD FILE IN PDF FORMAT. IF
                            THE
                            DOCUMENTS HAVE MULTIPLE PAGES IT SHOULD BE IN ONE (1) PDF FILE.
                            <span class="text-red-500">(5MB FILE SIZE LIMIT)</span>
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="document1">Application
                                    Letter</label>
                                <input
                                    class="shadow appearance-none border border-gray-400 rounded w-full md:w-96 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                    id="document1" type="file" accept=".pdf" :key="resetKey"
                                    @input="form.application_letter = $event.target.files[0]"
                                    :class="{ 'border-red-500': form.errors.application_letter }"
                                    @focus="form.clearErrors('application_letter')"/>
                                <Message v-if="form.errors.application_letter" severity="error" variant="simple"
                                         size="small">{{ form.errors.application_letter }}
                                </Message>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="document2">Fully
                                    accomplished
                                    Personal Data Sheet (PDS) with recent passport-sized picture.*Required</label>
                                <input
                                    class="shadow appearance-none border border-gray-400 rounded w-full md:w-96 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                    id="document2" type="file" accept=".pdf" :key="resetKey"
                                    @input="form.personal_data_sheet = $event.target.files[0]"
                                    :class="{ 'border-red-500': form.errors.personal_data_sheet }"
                                    @focus="form.clearErrors('personal_data_sheet')"/>
                                <Message v-if="form.errors.personal_data_sheet" severity="error" variant="simple"
                                         size="small">{{ form.errors.personal_data_sheet }}
                                </Message>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="document3">Performance
                                    rating
                                    in the present position for one(1) year (if applicable).</label>
                                <div class="flex items-center mb-2">
                                    <Checkbox v-model="form.skip_performance_rating" binary
                                              inputId="skip_performance_rating"/>
                                    <label for="skip_performance_rating" class="ml-2 text-sm text-gray-700">Skip this
                                        document</label>
                                </div>
                                <input
                                    class="shadow appearance-none border border-gray-400 rounded w-full md:w-96 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                    id="document3" type="file" accept=".pdf" :key="resetKey"
                                    @input="form.performance_rating = $event.target.files[0]"
                                    :class="{ 'border-red-500': form.errors.performance_rating }"
                                    @focus="form.clearErrors('performance_rating')"
                                    :disabled="form.skip_performance_rating"
                                />
                                <Message v-if="form.errors.performance_rating" severity="error" variant="simple"
                                         size="small">{{ form.errors.performance_rating }}
                                </Message>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="document4">Certificate of
                                    Eligibility/Rating or Professional License as proof of eligibility.*Required</label>
                                <div class="flex items-center mb-2">
                                    <Checkbox v-model="form.skip_eligibility_proof" binary
                                              inputId="skip_eligibility_proof"/>
                                    <label for="skip_eligibility_proof" class="ml-2 text-sm text-gray-700">Skip this
                                        document</label>
                                </div>
                                <input
                                    class="shadow appearance-none border border-gray-400 rounded w-full md:w-96 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                    id="document4" type="file" accept=".pdf" :key="resetKey"
                                    @input="form.eligibility_proof = $event.target.files[0]"
                                    :class="{ 'border-red-500': form.errors.eligibility_proof }"
                                    @focus="form.clearErrors('eligibility_proof')"
                                    :disabled="form.skip_eligibility_proof" />
                                <Message v-if="form.errors.eligibility_proof" severity="error" variant="simple"
                                         size="small">{{ form.errors.eligibility_proof }}
                                </Message>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="document5">Transcript of
                                    Records, including Diploma as proof of highest education attained.*Required</label>
                                <input
                                    class="shadow appearance-none border border-gray-400 rounded w-full md:w-96 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                    id="document5" type="file" accept=".pdf" :key="resetKey"
                                    @input="form.transcript = $event.target.files[0]"
                                    :class="{ 'border-red-500': form.errors.transcript }"
                                    @focus="form.clearErrors('transcript')"/>
                                <Message v-if="form.errors.transcript" severity="error" variant="simple" size="small">
                                    {{ form.errors.transcript }}
                                </Message>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="document4">Proof of
                                    Employment</label>
                                <div class="flex items-center mb-2">
                                    <Checkbox v-model="form.skip_employment_proof" binary
                                              inputId="skip_employment_proof"/>
                                    <label for="skip_employment_proof" class="ml-2 text-sm text-gray-700">Skip this
                                        document</label>
                                </div>
                                <input
                                    class="shadow appearance-none border border-gray-400 rounded w-full md:w-96 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                    id="document4" type="file" accept=".pdf" :key="resetKey"
                                    @input="form.employment_proof = $event.target.files[0]"
                                    :class="{ 'border-red-500': form.errors.employment_proof }"
                                    @focus="form.clearErrors('employment_proof')"
                                    :disabled="form.skip_employment_proof"
                                />
                                <Message v-if="form.errors.employment_proof" severity="error" variant="simple"
                                         size="small">{{ form.errors.employment_proof }}
                                </Message>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="document7">Certificate/s
                                    of
                                    Training/Seminar/Conferences as proof.*Required</label>
                                <input
                                    class="shadow appearance-none border border-gray-400 rounded w-full md:w-96 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                    id="document7" type="file" accept=".pdf" :key="resetKey"
                                    @input="form.training_certificates = $event.target.files[0]"
                                    :class="{ 'border-red-500': form.errors.training_certificates }"
                                    @focus="form.clearErrors('training_certificates')"/>
                                <Message v-if="form.errors.training_certificates" severity="error" variant="simple"
                                         size="small">{{ form.errors.training_certificates }}
                                </Message>
                            </div>
                        </div>

                        <div class="mt-6">
                            <VueTurnstile
                                ref="turnstileRef"
                                :siteKey="turnstileSiteKey"
                                v-model="form['cf-turnstile-response']"
                            />
                            <Message v-if="form.errors['cf-turnstile-response']" severity="error" variant="simple"
                                     size="small">{{ form.errors['cf-turnstile-response'] }}
                            </Message>
                            <Button type="submit" :disabled="form.processing" label="Submit" class="mt-8 w-full"/>
                        </div>
                    </form>
                    <!-- Success modal -->
                    <Dialog v-model:visible="successDialogVisible" modal :closable="false"
                            class="p-fluid w-[36rem]">
                        <template #header>
                            <div class="flex items-center justify-center w-full">
                                <i class="pi pi-check-circle text-green-500 text-4xl mr-3"></i>
                                <span class="font-bold text-2xl">Success!</span>
                            </div>
                        </template>
                        <div class="text-center">
                            <p class="text-lg mb-4">Your application has been successfully submitted.</p>
                            <p class="text-sm text-gray-600 mb-2">Thank you for applying. We will review your
                                application and get
                                back to you soon.</p>
                            <p class="text-sm text-blue-600 font-semibold mt-4">Just a friendly reminder: There's no
                                need to submit
                                multiple applications. We've got your information and will give it our full
                                attention!</p>
                        </div>
                        <template #footer>
                            <div class="flex justify-content-center">
                                <Button label="Close" icon="pi pi-times" @click="closeSuccessDialog"
                                        class="p-button-text"/>
                            </div>
                        </template>
                    </Dialog>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.form-background {
    background-image: url('/images/bg.webp'); /* Light blue background */
    background-size: cover;
    background-position: center;
    background-attachment: fixed; /* This makes the background fixed while scrolling */
    min-height: 100vh;
}

.p-field {
    margin-bottom: 1rem;
}

input[type='file'][accept^="image/"] {
    color: rgba(0, 0, 0, 0)
}

.file-input-button {
    @apply inline-flex cursor-pointer select-none items-center justify-center overflow-hidden relative
    bg-surface-0 hover:bg-surface-50 text-surface-700
    border border-surface-300 hover:border-surface-400
    focus-visible:outline-none focus-visible:outline-offset-0 focus-visible:ring-2 focus-visible:ring-primary-500
    transition-all duration-200 rounded-md
    text-sm px-[0.625rem] py-[0.375rem] font-semibold;
}

.file-input-button:active {
    @apply bg-surface-100 border-surface-500 transform scale-95;
    box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
}

/* Ensure the label has the same height as the button */
.file-input-button {
    height: 2.2rem;
}

:deep(.p-overflow-hidden) {
    overflow: auto;
    overflow-x: hidden;
}

/* Add these new styles for the checkbox */
:deep(.p-checkbox) {
    @apply relative inline-flex select-none w-5 h-5;
}

:deep(.p-checkbox-input) {
    @apply cursor-pointer disabled:cursor-default appearance-none absolute left-0 top-0 w-full h-full m-0 p-0 opacity-0 z-10 border border-transparent rounded-sm;
}

:deep(.p-checkbox-box) {
    @apply flex justify-center items-center rounded-sm w-5 h-5 border-2 border-cyan-300 bg-white dark:bg-gray-900 transition-colors duration-200 shadow-sm;
}

:deep(.p-checkbox-checked .p-checkbox-box) {
    @apply bg-cyan-600 border-cyan-700;
}

:deep(.p-checkbox-icon) {
    @apply text-white text-sm w-3.5 h-3.5 transition-colors duration-200;
}

:deep(.p-checkbox:not(.p-disabled):hover .p-checkbox-box) {
    @apply border-cyan-400;
}

:deep(.p-checkbox:not(.p-disabled) .p-checkbox-input:focus-visible + .p-checkbox-box) {
    @apply outline outline-2 outline-offset-2 outline-cyan-500;
}

:deep(.p-checkbox.p-disabled .p-checkbox-box) {
    @apply opacity-60 cursor-not-allowed;
}
</style>

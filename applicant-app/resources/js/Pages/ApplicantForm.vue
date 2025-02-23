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
import { useToast } from 'primevue/usetoast';

const props = defineProps(['positions']);

const visible = ref(false);
const photoPreview = ref(null);
const resetKey = ref(0);

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

const sogieOptions = ref([
    {label: 'Male', code: 'Male'},
    {label: 'Female', code: 'Female'},
    {label: 'Non-Binary', code: 'Non-Binary'},
    {label: 'Prefer not to say', code: 'Others'},
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

const toast = useToast();

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

const submit = () => {
    form.phone = form.phone.replace(/\D/g, '');
    form.post(route('applicant-form.store'), {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            console.log('Form submitted successfully');
            toast.add({severity: 'success', summary: 'Success', detail: 'Form submitted successfully', life: 5000});
            form.photo = null;
            photoPreview.value = null;
            resetKey.value++;
            form.reset();
        },
        onError: (error) => {
            console.error('Form submission failed');
            toast.add({severity: 'error', summary: 'Error', detail: 'Form submission failed', life: 5000});
            console.table(error);
        },
    });
};
</script>

<template>
    <Head title="Applicant Form"/>

    <Toast />

    <div class="flex justify-center items-center min-h-screen bg-gray-100 p-4">
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
                                        @change="form.clearErrors('position')"
                                >
                                    <template #option="slotProps">
                                        <div>
                                            <div>{{ slotProps.option.name }}</div>
                                            <div>{{ slotProps.option.description }}</div>
                                        </div>
                                    </template>
                                </Select>
                                <Message v-if="form.errors.position" severity="error" variant="simple" size="small">{{ form.errors.position }}</Message>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4"/>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-20">
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="photo2x2" class="block mb-2">Upload Photo (2x2)</label>
                                <div class="flex items-center gap-2">
                                    <Button label="Show" size="small" @click="visible = true" />
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

                                <Dialog v-model:visible="visible" modal header="Edit Profile" :style="{ width: '25rem' }">
                                    <span class="text-surface-500 dark:text-surface-400 block mb-8">Update your information.</span>
                                    <div class="flex items-center gap-4 mb-4">
                                        <label for="username" class="font-semibold w-24">Username</label>
                                        <InputText id="username" class="flex-auto" autocomplete="off" />
                                    </div>
                                    <div class="flex items-center gap-4 mb-8">
                                        <label for="email" class="font-semibold w-24">Email</label>
                                        <InputText id="email" class="flex-auto" autocomplete="off" />
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <Button type="button" label="Cancel" severity="secondary" @click="visible = false"></Button>
                                        <Button type="button" label="Save" @click="visible = false"></Button>
                                    </div>
                                </Dialog>
                                <Message v-if="form.errors.application_letter" severity="error" variant="simple" size="small">{{ form.errors.photo }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label class="block mb-2">Photo Preview</label>
                                <div v-if="photoPreview" class="mt-2">
                                    <img :src="photoPreview" alt="Photo preview" class="max-w-full h-auto max-h-32 rounded-lg shadow-md"/>
                                </div>
                                <div v-else class="mt-2 bg-gray-100 border border-gray-300 rounded-lg p-4 text-center text-gray-500">
                                    No photo uploaded
                                </div>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="firstname" class="block mb-2">First name</label>
                                <InputText id="firstname" class="w-full" v-model="form.first_name" :class="{ 'p-invalid': form.errors.first_name }"
                                           placeholder="Firstname" @input="form.clearErrors('first_name')"/>
                                <Message v-if="form.errors.first_name" severity="error" variant="simple" size="small">{{ form.errors.first_name }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="mi" class="block mb-2">Middle Initial</label>
                                <InputText id="mi" class="w-full" v-model="form.middle_initial"
                                           placeholder="Middle Initial" @input="form.clearErrors('middle_initial')"/>
                                <Message v-if="form.errors.middle_initial" severity="error" variant="simple" size="small">{{ form.errors.middle_initial }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="lastname" class="block mb-2">Last name</label>
                                <InputText id="lastname" class="w-full" v-model="form.last_name" :class="{ 'p-invalid': form.errors.last_name }"
                                           placeholder="Lastname" @input="form.clearErrors('last_name')"/>
                                <Message v-if="form.errors.last_name" severity="error" variant="simple" size="small">{{ form.errors.last_name }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="suffix" class="block mb-2">Suffix</label>
                                <InputText id="suffix" class="w-full" v-model="form.suffix"
                                           placeholder="Suffix" @input="form.clearErrors('suffix')"/>
                                <Message v-if="form.errors.suffix" severity="error" variant="simple" size="small">{{ form.errors.suffix }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="email" class="block mb-2">Email</label>
                                <InputText id="email" class="w-full" v-model="form.email" type="email" :class="{ 'p-invalid': form.errors.email }"
                                           placeholder="Email" @input="form.clearErrors('email')"/>
                                <Message v-if="form.errors.email" severity="error" variant="simple" size="small">{{ form.errors.email }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="phone" class="block mb-2">Phone Number</label>
                                <InputMask id="phone" class="w-full" v-model="form.phone" :class="{ 'p-invalid': form.errors.phone }" mask="+63 9999999999"
                                           placeholder="+63 9XXXXXXXXX" @update:modelValue="() => form.clearErrors('phone')"/>
                                <Message v-if="form.errors.phone" severity="error" variant="simple" size="small">{{ form.errors.phone }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="religion" class="block mb-2">Religion</label>
                                <InputText id="religion" class="w-full" v-model="form.religion"
                                           placeholder="Religion" @input="form.clearErrors('religion')"/>
                                <Message v-if="form.errors.religion" severity="error" variant="simple" size="small">{{ form.errors.religion }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="sogie" class="block mb-2">Sexual Orientation Gender Identity and Expression
                                    (SOGIE)</label>
                                <Select id="sogie"
                                        v-model="form.sogie"
                                        :options="sogieOptions"
                                        optionLabel="label"
                                        optionValue="code"
                                        placeholder="Please choose.."
                                        class="w-full"
                                        :class="{ 'p-invalid': form.errors.sogie }"
                                        @change="form.clearErrors('sogie')"/>
                                <Message v-if="form.errors.sogie" severity="error" variant="simple" size="small">{{ form.errors.sogie }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="birthdate" class="block mb-2">Birth Date</label>
                                <DatePicker id="birthdate" class="w-full" v-model="form.birth_date"
                                            dateFormat="mm-dd-yy" placeholder="MM-DD-YYYY" :invalid="form.errors.birth_date"
                                            @update:modelValue="() => form.clearErrors('birth_date')"/>
                                <Message v-if="form.errors.birth_date" severity="error" variant="simple" size="small">{{ form.errors.birth_date }}</Message>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-field">
                                <label for="address" class="block mb-2">Address</label>
                                <InputText id="address" class="w-full" v-model="form.address" :class="{ 'p-invalid': form.errors.address }"
                                           placeholder="Prk./Brgy./City/Municipality" @input="form.clearErrors('address')"/>
                                <Message v-if="form.errors.address" severity="error" variant="simple" size="small">{{ form.errors.address }}</Message>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-field">
                                <label for="highest_education" class="block mb-2">Highest Educational Attainment</label>
                                <Select id="highest_education"
                                        v-model="form.highest_education"
                                        :options="educationOptions"
                                        optionLabel="label"
                                        optionValue="code"
                                        placeholder="Please choose.."
                                        class="w-full"
                                        :class="{ 'p-invalid': form.errors.highest_education }"
                                        @change="form.clearErrors('highest_education')"/>
                                <Message v-if="form.errors.highest_education" severity="error" variant="simple" size="small">{{ form.errors.highest_education }}</Message>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-field">
                                <label for="latest_company" class="block mb-2">Latest Company/Agency</label>
                                <InputText id="latest_company" class="w-full" v-model="form.latest_company"
                                           placeholder="Latest Company/Agency"/>
                                <Message v-if="form.errors.latest_company" severity="error" variant="simple" size="small">{{ form.errors.latest_company }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="present_position" class="block mb-2">Present/Latest Position</label>
                                <InputText id="present_position" class="w-full" v-model="form.present_position"
                                           placeholder="Latest position"/>
                                <Message v-if="form.errors.present_position" severity="error" variant="simple" size="small">{{ form.errors.present_position }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="status_employment" class="block mb-2">Status of Employment</label>
                                <InputText id="status_employment" class="w-full"
                                           v-model="form.status_employment"
                                           placeholder="Status of Employment"/>
                                <Message v-if="form.errors.status_employment" severity="error" variant="simple" size="small">{{ form.errors.status_employment }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="still_employed" class="block mb-2">Last Date of Employment</label>
                                <DatePicker id="still_employed" class="w-full" v-model="form.last_employment_date"
                                            dateFormat="mm-dd-yy" placeholder="MM-DD-YYYY"/>
                                <Message v-if="form.errors.last_employment_date" severity="error" variant="simple" size="small">{{ form.errors.last_employment_date }}</Message>
                            </div>
                        </div>
                        <div class="col-12 md:col-6">
                            <div class="p-field">
                                <label for="eligibility" class="block mb-2">Eligibility</label>
                                <InputText id="eligibility" class="w-full" v-model="form.eligibility"
                                           placeholder="Eligibility"/>
                                <Message v-if="form.errors.eligibility" severity="error" variant="simple" size="small">{{ form.errors.eligibility }}</Message>
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
                                id="document1" type="file" accept=".pdf" :key="resetKey"
                                @input="form.application_letter = $event.target.files[0]" @focus="form.clearErrors('application_letter')"/>
                            <Message v-if="form.errors.application_letter" severity="error" variant="simple" size="small">{{ form.errors.application_letter }}</Message>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document2">Fully accomplished
                                Personal Data Sheet (PDS) with recent passport-sized picture.*Required</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document2" type="file" accept=".pdf" :key="resetKey"
                                @input="form.personal_data_sheet = $event.target.files[0]" @focus="form.clearErrors('personal_data_sheet')"/>
                            <Message v-if="form.errors.personal_data_sheet" severity="error" variant="simple" size="small">{{ form.errors.personal_data_sheet }}</Message>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document3">Performance rating
                                in the present position for one(1) year (if applicable).</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document3" type="file" accept=".pdf" :key="resetKey"
                                @input="form.performance_rating = $event.target.files[0]" @focus="form.clearErrors('performance_rating')"/>
                            <Message v-if="form.errors.performance_rating" severity="error" variant="simple" size="small">{{ form.errors.performance_rating }}</Message>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document4">Certificate of
                                Eligibility/Rating or Professional License as proof of eligibility.*Required</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document4" type="file" accept=".pdf" :key="resetKey"
                                @input="form.eligibility_proof = $event.target.files[0]" @focus="form.clearErrors('eligibility_proof')"/>
                            <Message v-if="form.errors.eligibility_proof" severity="error" variant="simple" size="small">{{ form.errors.eligibility_proof }}</Message>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document5">Transcript of
                                Records, including Diploma as proof of highest education attained.*Required</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document5" type="file" accept=".pdf" :key="resetKey"
                                @input="form.transcript = $event.target.files[0]" @focus="form.clearErrors('transcript')"/>
                            <Message v-if="form.errors.transcript" severity="error" variant="simple" size="small">{{ form.errors.transcript }}</Message>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document6">Certificate of
                                Employment/Service Contract/Work Experience Sheet as proof of
                                experience.*Required</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document6" type="file" accept=".pdf" :key="resetKey"
                                @input="form.employment_proof = $event.target.files[0]" @focus="form.clearErrors('employment_proof')"/>
                            <Message v-if="form.errors.employment_proof" severity="error" variant="simple" size="small">{{ form.errors.employment_proof }}</Message>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="document7">Certificate/s of
                                Training/Seminar/Conferences as proof.*Required</label>
                            <input
                                class="shadow appearance-none border border-gray-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                id="document7" type="file" accept=".pdf" :key="resetKey"
                                @input="form.training_certificates = $event.target.files[0]" @focus="form.clearErrors('training_certificates')"/>
                            <Message v-if="form.errors.training_certificates" severity="error" variant="simple" size="small">{{ form.errors.training_certificates }}</Message>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Button type="submit" :disabled="form.processing" label="Submit" class="mt-8 w-full"/>
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
input[type='file'][accept^="image/"]  {
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
</style>

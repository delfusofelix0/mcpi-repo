<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { ref } from "vue";
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';

defineProps({
    canLogin: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const loading = ref(false);

const goToApplicantForm = () => {
    loading.value = true;
    router.visit(route('applicant-form.index'), {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            loading.value = false;
        },
    });
};




</script>

<template>
    <Head title="Welcome" />
        <div
            class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header
                    class="grid grid-cols-1 items-center gap-2 py-10"
                >
                    <div class="flex justify-center">
                        <ApplicationLogo class="max-h-52"/>
                    </div>
<!--                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">-->
<!--                        <Link-->
<!--                            v-if="$page.props.auth.user"-->
<!--                            :href="route('dashboard')"-->
<!--                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"-->
<!--                        >-->
<!--                            Dashboard-->
<!--                        </Link>-->

<!--                        <template v-else>-->
<!--                            <Link-->
<!--                                :href="route('login')"-->
<!--                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"-->
<!--                            >-->
<!--                                Log in-->
<!--                            </Link>-->
<!--                        </template>-->
<!--                    </nav>-->
                </header>

                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-cyan-100 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h1 class="text-3xl font-bold mb-4 text-center">Welcome to MCPI Applicant Registration System</h1>
                                <p class="mb-4 text-center">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                <div class="mt-6 flex justify-center">
                                    <Button
                                        label="Go to Applicant Form"
                                        @click="goToApplicantForm"
                                        :loading="loading"
                                        class="text-white font-bold py-2 px-4 rounded"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <footer
                    class="py-16 text-center text-sm text-black"
                >
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </footer>
            </div>
        </div>
</template>

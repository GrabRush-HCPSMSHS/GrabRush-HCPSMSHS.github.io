<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Header from '@/Components/Welcome/Header.vue';
import Footer from '@/Components/Welcome/Footer.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <Head title="Two-factor Confirmation" />
    <div class="bg-violet-100 dark:bg-gray-900 text-gray-800 dark:text-white min-h-screen flex flex-col">
        <Header :can-login="canLogin" :can-register="canRegister" />

        <main class="flex-grow flex items-center justify-center isolate px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md p-8 space-y-6 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Two-Factor Challenge
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        <template v-if="!recovery">
                            Please confirm access to your account by entering the authentication code provided by your authenticator application.
                        </template>
                        <template v-else>
                            Please confirm access to your account by entering one of your emergency recovery codes.
                        </template>
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div v-if="!recovery">
                        <InputLabel for="code" value="Code" />
                        <TextInput
                            id="code"
                            ref="codeInput"
                            v-model="form.code"
                            type="text"
                            inputmode="numeric"
                            class="mt-1 block w-full"
                            autofocus
                            autocomplete="one-time-code"
                            placeholder="123456"
                        />
                        <InputError class="mt-2" :message="form.errors.code" />
                    </div>

                    <div v-else>
                        <InputLabel for="recovery_code" value="Recovery Code" />
                        <TextInput
                            id="recovery_code"
                            ref="recoveryCodeInput"
                            v-model="form.recovery_code"
                            type="text"
                            class="mt-1 block w-full"
                            autocomplete="one-time-code"
                            placeholder="your-recovery-code"
                        />
                        <InputError class="mt-2" :message="form.errors.recovery_code" />
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="button" class="text-sm font-medium text-violet-600 hover:text-violet-500 cursor-pointer" @click.prevent="toggleRecovery">
                            <template v-if="!recovery">
                                Use a recovery code
                            </template>
                            <template v-else>
                                Use an authentication code
                            </template>
                        </button>
                    </div>
                    
                    <div>
                        <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Log in
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </main>

        <Footer />
    </div>
</template>

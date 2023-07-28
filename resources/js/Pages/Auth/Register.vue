<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import useVuelidate from '@vuelidate/core'
import { required, email, helpers, minLength, sameAs } from '@vuelidate/validators'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});
const password = computed(() => form.password);

// Add validation
const rules = {
    name:{
        required: helpers.withMessage('Name is required', required),
    },
    email:{ 
        required: helpers.withMessage('Email is required', required),
        email: helpers.withMessage('Invalid email address', email),
    },
    password:{
        required: helpers.withMessage('Password is required', required),
        minLength: helpers.withMessage('Password must be 8 character long', minLength(8)),
    },
    password_confirmation: {
        required: helpers.withMessage('Please confirm your password', required),
        sameAsPassword: helpers.withMessage('Passwords do not match', sameAs(password)),    }
}
const v$ = useVuelidate(rules, form)

const submit = () => {
    v$.value.$touch()
    if (!v$.value.$error) {
        form.post(route('register'), {
            onFinish: function() {
                form.reset('password', 'password_confirmation')
                v$.value.$reset()
            }
        });
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />
        <h1 class="text-3xl mb-6 text-center">Sign Up</h1>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    autofocus
                    autocomplete="name"
                />
                <InputError v-if="v$.name.$error" class="mt-2" :message="v$.name.$errors[0].$message" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    autocomplete="username"
                />
                <InputError v-if="v$.email.$error" class="mt-2" :message="v$.email.$errors[0].$message" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    autocomplete="new-password"
                />
                <InputError v-if="v$.password.$error" class="mt-2" :message="v$.password.$errors[0].$message" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                />
                <InputError v-if="v$.password_confirmation.$error" class="mt-2" :message="v$.password_confirmation.$errors[0].$message" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Already registered?
                </Link>

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

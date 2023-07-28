<script setup>

import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import useVuelidate from '@vuelidate/core'
import { required, email, helpers } from '@vuelidate/validators'

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Add validation
const rules = {
    email:{ 
        required: helpers.withMessage('Email is required', required),
        email: helpers.withMessage('Invalid email address', email)
    },
    password:{
        required: helpers.withMessage('Password is required', required)
    }
}
const v$ = useVuelidate(rules, form)

const submit = () => {
    v$.value.$touch()
    if (!v$.value.$error) {
        form.post(route('login'), {
            onFinish: function() {
                form.reset('password')
                v$.value.$reset()
            }
        });
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />
        <h1 class="text-3xl mb-8 text-center">Login</h1>
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>
        <div v-if="form.errors.email" class="mb-4 font-medium text-sm text-green-600">
            <InputError :message="form.errors.email" />
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    autofocus
                    autocomplete="username"
                />
                <InputError v-if="v$.email.$error" class="mt-2" :message="v$.email.$errors[0].$message" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    autocomplete="current-password"
                />

                <InputError v-if="v$.password.$error" class="mt-2" :message="v$.password.$errors[0].$message" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('register')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Create an account
                </Link>
                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

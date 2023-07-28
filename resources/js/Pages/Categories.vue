<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import DataTable from 'datatables.net-vue3';
import DataTablesLib from 'datatables.net';
import { onMounted, ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import useVuelidate from '@vuelidate/core';
import { required, helpers } from '@vuelidate/validators';
import { useToast } from "vue-toastification";
const toast = useToast();
 // Using DataTables Library
DataTable.use(DataTablesLib);

const props = defineProps({
    categories: {
        type: Array,
    }
});
 // Define component refs
const confirmingUserDeletion = ref(false);
const addEditCategoryModel = ref(false);

const form = useForm({
    category_id: '',
    status:1,
    title: ''
});
// Define form validation rules
const rules = {
    status:{
        required: helpers.withMessage('Status is required', required)
    },
    title:{ 
        required: helpers.withMessage('Category name is required', required)
    }
}
const v$ = useVuelidate(rules, form)

// Define DataTable columns
const columns = [
    {
        data: null,
        render: function (data, type, row, meta) {
            if (type === 'display') {
                return meta.row + 1;
            }
            return '';
        }
    },
    { data: 'title' },
    { 
        data: null,
        render: function (data) {
            var date = new Date(data.created_at);
            var options = {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true,
                timeZone: 'UTC',
            };
            return new Intl.DateTimeFormat('en-US', options).format(date);
        }
    },
    {
        data: null,
        render: (data) => (data.status == 1) ? '<button type="button" class="px-2 py-1 text-xs font-medium text-center text-white bg-green-600 rounded">Active</button>':'<button type="button" class="px-2 py-1 text-xs font-medium text-center text-white bg-red-600 rounded">In-Active</button>'
    },
    {
        data: null,
        render: function (data) {
            return `<button data-id="${data.id}" class="edit-category inline-flex items-center justify-center w-8 h-8 mr-2 text-gray-700 transition-colors duration-150 bg-white rounded-full focus:shadow-outline hover:bg-gray-200">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                    </button>
                    <button data-id="${data.id}" class="delete-category inline-flex items-center justify-center w-8 h-8 mr-2 text-gray-700 transition-colors duration-150 bg-white rounded-full focus:shadow-outline hover:bg-gray-200">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L17.1991 18.0129C17.129 19.065 17.0939 19.5911 16.8667 19.99C16.6666 20.3412 16.3648 20.6235 16.0011 20.7998C15.588 21 15.0607 21 14.0062 21H9.99377C8.93927 21 8.41202 21 7.99889 20.7998C7.63517 20.6235 7.33339 20.3412 7.13332 19.99C6.90607 19.5911 6.871 19.065 6.80086 18.0129L6 6M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M14 10V17M10 10V17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </button>`;
        }
    },
];
 // Function to delete category
const deleteCategory = () => {
    form.delete(route('category.destroy'), {
        preserveScroll: true,
        onSuccess: function() { 
            confirmingUserDeletion.value = false;
            form.reset()
        },
        onError: () => alert("Something went wrong")
    });
};
 // Function to process category update
const updateCategoryProcess = () => {
    v$.value.$touch()
    if (!v$.value.$error) {
        form.post(route('category.store'), {
            preserveScroll: true,
            onSuccess: function() { 
                addEditCategoryModel.value = false;
                let msg = form.category_id > 0 ? "update.":"added.";
                toast.success("Category successfully "+msg);
                form.reset()
                v$.value.$reset()
            },
            onError: () => alert("Something went wrong")
        });
    }
};
 // Function to edit category
const editCategory = (id) => {
    var categoryObject = $.grep(props.categories, function(category) {
        return category.id === id;
    });
    if (categoryObject.length > 0) {
        const cate = categoryObject[0] 
        form.title = cate.title
        form.status = cate.status
        addEditCategoryModel.value = true;
    } else {
        console.log('Category not found!');
    }
};
// Open Add Category modal
const openAddCategoryModel = () => {
     addEditCategoryModel.value = true;
    form.reset()
}
onMounted(() => {
    $("tbody").on("click", ".edit-category", function (e) {
        e.preventDefault();
        form.category_id = $(this).data("id");
        editCategory($(this).data("id"));


        // 

    });
    $("tbody").on("click", ".delete-category", function (e) {
        e.preventDefault();
        form.category_id = $(this).data("id");
        confirmingUserDeletion.value = true;
    });
});
</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="">
                        <h2 class=" p-6 font-semibold float-left text-xl text-gray-800 leading-tight">Categories</h2>
                        <button @click="openAddCategoryModel" type="button" class="float-right m-6 px-2 py-1 text-xs font-medium text-center text-white bg-green-600 rounded">Add Category</button>
                    </div>
                    <div class="p-6 text-gray-900">
                        <DataTable :columns="columns" :data="categories" class="row-borde w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <Modal :show="confirmingUserDeletion" @close="confirmingUserDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    you want to delete this category?
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="confirmingUserDeletion = false"> Cancel </SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteCategory"
                    >
                        Yes Delete it
                    </DangerButton>
                </div>
            </div>
        </Modal>
        <Modal :show="addEditCategoryModel" @close="addEditCategoryModel = false" maxWidth="lg">
            <div class="p-6">
                <form @submit.prevent="updateCategoryProcess">
                    <h1 class="text-3xl mb-8 text-center"><span v-if="form.category_id > 0">Edit</span><span v-else>Add</span> Category</h1>
                    <div>
                        <InputLabel for="title" value="Title" />
                        <TextInput
                            id="title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.title"
                            autofocus
                            autocomplete="category title"
                        />
                        <InputError v-if="v$.title.$error" class="mt-2" :message="v$.title.$errors[0].$message" />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="status" value="Status" />

                        <select
                            id="status"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            v-model="form.status"
                            required
                        >
                            <option value="1">Active</option>
                            <option value="0">In-Active</option>
                        </select>
                        <InputError v-if="v$.status.$error" class="mt-2" :message="v$.status.$errors[0].$message" />
                        <InputError class="mt-2" :message="form.errors.status" />
                    </div>
                    <div class="flex items-center justify-end mt-8">
                        <SecondaryButton @click="addEditCategoryModel = false"> Cancel </SecondaryButton>
                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            <span v-if="form.category_id > 0"> Update</span><span v-else>Add</span>
                        </PrimaryButton>
                    </div>
                </form>

            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
<style>
@import 'datatables.net-dt';
.dataTables_wrapper select {
    min-width: 60px;
}
</style>
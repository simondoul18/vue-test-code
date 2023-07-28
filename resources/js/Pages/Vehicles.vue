<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head,useForm } from '@inertiajs/vue3';
import DataTable from 'datatables.net-vue3'
import DataTablesLib from 'datatables.net';
import { onMounted, ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import useVuelidate from '@vuelidate/core';
import { required, numeric, helpers } from '@vuelidate/validators';
import { useToast } from "vue-toastification";
const toast = useToast();
DataTable.use(DataTablesLib);

// Define props for the component
const props = defineProps({
    categories: {type: Array},
    comapnies: {type: Array},
    years: {type: Array},
    models: {type: Array},
    colors: {type: Array},
});
// Define component refs
const confirmingVehicleDeletion = ref(false);
const addEditVehicleModel = ref(false);
const vehicles = ref([])

// Define the filterForm object to hold filter values
const filterForm = {
    category_id:'',
    make:'',
    model:'',
    year:'',
    color:''
}
// Form to handle user input for adding/editing vehicles
const form = useForm({
    vehicle_id:'',
    category_id:'',
    make:'',
    model:'',
    year:'',
    color:'',
    registration_no:'',
    price:'',
    status:1
});

// Define form validation rules
const rules = {
    category_id:{
        required: helpers.withMessage('Category is required', required),
    },
    make:{ required },
    model:{
        required: helpers.withMessage('Model is required', required),
    },
    year: {
        required: helpers.withMessage('Year is required', required),
        numeric: helpers.withMessage('Year must be integer', numeric)
    },
    price:{
        required: helpers.withMessage('Price is required', required),
    }
}
const v$ = useVuelidate(rules, form)

// Function to delete a vehicle
const deleteVehicle = () => {
    form.delete(route('vehicle.destroy'), {
        preserveScroll: true,
        onSuccess: function() { 
            confirmingVehicleDeletion.value = false;
            form.reset();
            getVehicles();
        },
        onError: function(data) { 
            console.log(data);
            // console.log(form.errors);
        }
    });
};
// Function to update or add a vehicle
const updateVehicleProcess = () => {
    v$.value.$touch()
    if (!v$.value.$error) {
        form.post(route('vehicle.store'), {
            preserveScroll: true,
            onSuccess: function() {
                addEditVehicleModel.value = false;
                let msg = form.vehicle_id > 0 ? "update.":"added.";
                toast.success("Vehicle successfully "+msg);
                v$.value.$reset()
                getVehicles()
                form.reset()
            },
            onError: function(data){
                console.log(data);
            }
        });
    }
}
// Function to edit a vehicle
const editVehicle = (id) => {
    var vehicleObject = $.grep(vehicles.value, function(v) {
        return v.id === id;
    });
    if (vehicleObject.length > 0) {
        const vehicle = vehicleObject[0] 
        form.category_id = vehicle.category_id
        form.make = vehicle.make
        form.model = vehicle.model
        form.year = vehicle.year
        form.color = vehicle.color
        form.registration_no = vehicle.registration_no
        form.price = vehicle.price
        form.status = vehicle.status
        addEditVehicleModel.value = true;
    } else {
        console.log('Vehicle not found!');
    }
};
// Function to fetch vehicles from the server
const getVehicles = () => {
    axios.get(route('vehicle.get',filterForm))
    .then(res => {
        vehicles.value = res.data
    });
}
getVehicles()
// Open Add Vehicle modal
const openAddVehicleModel = () => {
    addEditVehicleModel.value = true;
    form.reset()
}

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
    { 
        data: null,
        render: (data) => data.make+' '+data.model+' '+data.year
    },
    {
        data: null,
        render: (data) => data.category ? data.category.title : ''
    },
    {data: 'color'},
    {data: 'registration_no'},
    {
        data: null,
        render: (data) => data.price ? 'Rs. '+data.price : ''
    },
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
            return `<button data-id="${data.id}" class="edit-vehicle inline-flex items-center justify-center w-8 h-8 mr-2 text-gray-700 transition-colors duration-150 bg-white rounded-full focus:shadow-outline hover:bg-gray-200">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                    </button>
                    <button data-id="${data.id}" class="delete-vehicle inline-flex items-center justify-center w-8 h-8 mr-2 text-gray-700 transition-colors duration-150 bg-white rounded-full focus:shadow-outline hover:bg-gray-200">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 6L17.1991 18.0129C17.129 19.065 17.0939 19.5911 16.8667 19.99C16.6666 20.3412 16.3648 20.6235 16.0011 20.7998C15.588 21 15.0607 21 14.0062 21H9.99377C8.93927 21 8.41202 21 7.99889 20.7998C7.63517 20.6235 7.33339 20.3412 7.13332 19.99C6.90607 19.5911 6.871 19.065 6.80086 18.0129L6 6M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M14 10V17M10 10V17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </button>`;
        }
    },
];
onMounted(() => {
    $("tbody").on("click", ".edit-vehicle", function (e) {
        e.preventDefault();
        form.vehicle_id = $(this).data("id");
        editVehicle($(this).data("id"));
    });
    $("tbody").on("click", ".delete-vehicle", function (e) {
        e.preventDefault();
        form.vehicle_id = $(this).data("id");
        confirmingVehicleDeletion.value = true;
    });
});
</script>

<template>
    <Head title="Categories" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="flex flex-row justify-between">
                        <h2 class="p-6 font-semibold text-xl text-gray-800 leading-tight">Vehicles</h2>
                        <button @click="openAddVehicleModel" type="button" class="m-6 px-2 py-1 text-xs font-medium text-center text-white bg-green-600 rounded">Add New Vehicle</button>
                    </div>
                    <!-- Filters -->
                    <div class="flex flex-row p-6 space-x-4">
                        <div class="relative mt-2">Filters: </div>
                        <div class="relative">
                            <select @change="getVehicles" v-model="filterForm.category_id" class="w-48 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Choose Category</option>
                                <option v-for="cate in categories" :key="cate.id" :value="cate.id">{{cate.title}}</option>
                            </select>
                        </div>
                        <div class="relative">
                            <select @change="getVehicles" v-model="filterForm.make" class="w-48 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Choose Company</option>
                                <option v-for="(make,i) in comapnies" :key="i" :value="make">{{make}}</option>
                            </select>
                        </div>
                        <div class="relative">
                            <select @change="getVehicles" v-model="filterForm.model" class="w-48 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Choose Model</option>
                                <option v-for="(model,i) in models" :key="i" :value="model">{{model}}</option>
                            </select>
                        </div>
                        <div class="relative">
                            <select @change="getVehicles" v-model="filterForm.year" class="w-48 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Choose Year</option>
                                <option v-for="(year,i) in years" :key="i" :value="year">{{year}}</option>
                            </select>
                        </div>
                        <div class="relative">
                            <select @change="getVehicles" v-model="filterForm.color" class="w-48 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Choose color</option>
                                <option v-for="(color,i) in colors" :key="i" :value="color">{{color}}</option>
                            </select>
                        </div>
                        <!-- <button class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Filter</button> -->
                    </div>

                    <!-- Datatable -->
                    <div class="p-6 text-gray-900">
                        <DataTable :columns="columns" :data="vehicles" class="row-borde w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vehicle</th>
                                    <th>Category</th>
                                    <th>Color</th>
                                    <th>Registration No</th>
                                    <th>Price</th>
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
        <!-- Vehicle Delete Confirmation Modal -->
        <Modal :show="confirmingVehicleDeletion" @close="confirmingVehicleDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    you want to delete this vehicle?
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="confirmingVehicleDeletion = false"> Cancel </SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteVehicle"
                    >
                        Yes Delete it
                    </DangerButton>
                </div>
            </div>
        </Modal>
        <!-- Add or Edit vehicle modal -->
        <Modal :show="addEditVehicleModel" @close="addEditVehicleModel = false" maxWidth="2xl">
            <div class="p-6">
                <form @submit.prevent="updateVehicleProcess">
                    <h1 class="text-3xl mb-8 text-center"><span v-if="form.vehicle_id > 0">Edit</span><span v-else>Add</span> Vehicle</h1>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mt-4">
                            <InputLabel for="make" value="Make" />
                            <TextInput v-model="form.make" id="make" autocomplete="" type="text" class="mt-1 block w-full" />
                            <InputError v-if="v$.make.$error" class="mt-2" :message="v$.make.$errors[0].$message" />
                            <InputError class="mt-2" :message="form.errors.make" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="model" value="Model" />
                            <TextInput v-model="form.model" id="model" autocomplete="add model" type="text" class="mt-1 block w-full" />
                            <InputError v-if="v$.model.$error" class="mt-2" :message="v$.model.$errors[0].$message" />
                            <InputError class="mt-2" :message="form.errors.model" />
                        </div>
                        
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mt-4">
                            <InputLabel for="year" value="Year" />
                            <TextInput v-model="form.year" id="year" autocomplete="Year" type="text" class="mt-1 block w-full" />
                            <InputError v-if="v$.year.$error" class="mt-2" :message="v$.year.$errors[0].$message" />
                            <InputError class="mt-2" :message="form.errors.year" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="color" value="Color" />
                            <TextInput v-model="form.color" id="color" autocomplete="Color" type="text" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="form.errors.color" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mt-4">
                            <InputLabel for="registration_no" value="Registration No." />
                            <TextInput v-model="form.registration_no" id="registration_no" autocomplete="Registration No." type="text" class="mt-1 block w-full" />
                            <InputError class="mt-2" :message="form.errors.registration_no" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="price" value="Price" />
                            <TextInput v-model="form.price" id="price" autocomplete="Price" type="text" class="mt-1 block w-full" />
                            <InputError v-if="v$.price.$error" class="mt-2" :message="v$.price.$errors[0].$message" />
                            <InputError class="mt-2" :message="form.errors.price" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mt-4">
                            <InputLabel for="caterory" value="Caterory" />
                            <select id="caterory" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" v-model="form.category_id" >
                                <option value="">Choose category</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">{{category.title}}</option>
                            </select>
                            <InputError v-if="v$.category_id.$error" class="mt-2" :message="v$.category_id.$errors[0].$message" />
                            <InputError class="mt-2" :message="form.errors.category_id" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="status" value="Status" />
                            <select v-model="form.status" id="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" >
                                <option value="1">Active</option>
                                <option value="0">In-Active</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.status" />
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-8">
                        <SecondaryButton @click="addEditVehicleModel = false"> Cancel </SecondaryButton>

                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            <span v-if="form.vehicle_id > 0"> Update</span><span v-else>Add</span>
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
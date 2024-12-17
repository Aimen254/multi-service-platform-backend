<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">UserRole</h4>
        </template>
        <template #content>
            <form @submit.prevent="submit">
                <div class="row">
                    <div class="col-12 mb-4">
                        <Label for="stock" class="required" value="Select Role" />
                        <select v-model="form.user_type" class="form-select text-capitalize form-select-solid"
                            :class="{ 'is-invalid border border-danger': form.errors.user_type, }">
                            <option class="text-capitalize fw-bold" value="">Select</option>
                            <option value="business_owner" class="text-capitalize">Business Owner</option>
                        </select>
                        <error :message="form.errors.user_type"></error>
                    </div>
                    <div class="text-end">
                        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            ref="submitButton">
                            <span class="indicator-label" v-if="!form.processing"> {{ form.id ? 'Update' :
                'Save' }}</span>
                            <span class="indicator-progress" v-if="form.processing">
                                <span class="spinner-border spinner-border-sm align-middle"></span>
                            </span>
                        </Button>
                    </div>
                </div>
            </form>
        </template>
    </Modal>

</template>

<script>
import Modal from "@/Components/Modal.vue";
import Label from '@/Components/Label.vue';
import Button from "@/Components/Button.vue";
import Error from '@/Components/InputError.vue';
import { useForm } from "@inertiajs/inertia-vue3";

export default {
    props: ['url'],

    components: {
        Modal,
        Label,
        Error,
        Button
    },

    data() {
        return {
            form: {
                user_type: '',
                errors: ''
            },
            isLoading: false,
            showModal: true
        };
    },
    methods: {
        submit() {
            this.swal.fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Change Role</h1><p class='text-base'>Are you sure you want to change Role?</p>",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonText: "Yes",
                customClass: {
                    confirmButton: 'danger'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.isLoading = true;
                    if (this.form.id) {
                        this.form.processing = true;
                        this.form.post(route('dashboard.customer.change.role', this.form.id), {
                            errorBag: 'user_role',
                            preserveScroll: true,
                            onSuccess: () => {
                                $('#genericModal').modal('hide');
                            },
                            onError: (response) => {
                                this.form.errors = response;
                            },
                            onFinish: () => {
                                this.form.processing = false;
                                this.isLoading = false;
                            }
                        });
                    }
                }
            });
        }
    },

    mounted() {
        this.emitter.on('user-role-modal', (args) => {
            this.form = useForm({
                id: args.id ? args.id : null,
                user_type: '',
            });
            this.title = 'User Role'
            $('#genericModal').modal('show')
        })
    },
}

</script>

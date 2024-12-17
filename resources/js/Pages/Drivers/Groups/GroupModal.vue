
<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit" >
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="name" class="required" value="Name" />
                        <Input id="name" type="text"
                            :class="{'is-invalid border border-danger' : form.errors.code}" :value="form.name" v-model="form.name" placeholder="Enter name" />
                        <error :message="form.errors.name"></error>
                    </div>
                </div>

                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="manager_id" class="required" value="Select Manager" />
                        <select2
                            class="form-control-md text-capitalize form-control-solid"
                            :class="{'is-invalid border border-danger' : form.errors.manager_id}"
                            v-model="form.manager_id"
                            :options="managresList"
                            :settings="{ dropdownParent: '#genericModal' }"
                            placeholder="Select Manager"
                        />
                        <error :message="form.errors.manager_id"></error>
                    </div>         
                </div>

                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="drivers_ids" value="Select Driver" />
                        <multiselect :class="{
                                'is-invalid border border-danger':
                                    form.errors.driver_ids,
                            }"
                            v-model="form.driver_ids"
                            :options="driversList"
                            :multiple="true"
                            placeholder="Select Drivers"
                            track-by="id"
                            label="text">
                        </multiselect>
                        <error :message="form.errors.driver_ids"></error>
                    </div>          
                </div>

                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        >
                        <span class="indicator-label" v-if="!form.processing"> {{form.id ? 'Update' : 'Save'}}</span>
                        <span class="indicator-progress" v-if="form.processing">
                            <span class="spinner-border spinner-border-sm align-middle"></span>
                        </span>
                    </Button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3'
import Modal from '@/Components/Modal.vue'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import Error from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'
import Select2 from 'vue3-select2-component'

export default {
    components: {
        Modal,
        Label,
        Input,
        Error,
        Button,
        Select2
    },

    data() {
        return {
            title:null,
            maxWidth: '2xl',
            form: null,
            managresList: '',
            driversList: ''
        }
    },

    methods: {
        submit() {
            if (!this.form.id) {
                this.form.post(route('dashboard.driver.groups.store'), {
                    errorBag: 'groups',
                    preserveScroll: true,
                    onSuccess: () => $('#genericModal').modal('hide'),
                })
            } else {
                this.form.put(route('dashboard.driver.groups.update', this.form.id), {
                    errorBag: 'groups',
                    preserveScroll: true,
                    onSuccess: () => $('#genericModal').modal('hide'),
                })
            }
        }
    },

    mounted() {
        this.emitter.on('group-modal', (args) => {
            this.form = useForm({
                id: args.group ? args.group.id : null,
                name: args.group ? args.group.name : '',
                manager_id: args.group ? args.group.manager_id : '',
                driver_ids: args.group.drivers ? args.group.drivers : [],
            })
            this.title = args.group.id ? 'Edit Group':'Create Group';
            this.managresList = args.managers;
            this.driversList = args.drivers;
            $('#genericModal').modal('show');
        })
    }
}
</script>

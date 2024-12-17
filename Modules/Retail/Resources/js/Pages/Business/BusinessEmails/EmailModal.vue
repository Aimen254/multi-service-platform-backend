<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit" enctype="multipart/form-data">
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="name" class="required" value="Personal Name" />
                    <Input id="personal_name" type="text"
                        :class="{'is-invalid border border-danger' : form.errors.personal_name}" v-model="form.personal_name" autofocus
                        autocomplete="name" placeholder="Enter personal name" />
                    <error :message="form.errors.personal_name"></error>
                </div>
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="name" class="required" value="Title" />
                    <Input id="title" type="text"
                        :class="{'is-invalid border border-danger' : form.errors.title}" v-model="form.title" autofocus
                        autocomplete="name" placeholder="Enter title" />
                    <error :message="form.errors.title"></error>
                </div>
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="email" class="required" value="Email" />
                    <Input id="email" type="email"
                        :class="{'is-invalid border border-danger' : form.errors.email}" v-model="form.email" autofocus
                        autocomplete="email" placeholder="Enter email" />
                    <error :message="form.errors.email"></error>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        ref="submitButton">
                        <span class="indicator-label" v-if="!form.processing"> {{businessEmail ? 'Update' : 'Save'}}</span>
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
    import Modal from '@/Components/Modal.vue'
    import {
        useForm
    } from '@inertiajs/inertia-vue3'
    import Input from '@/Components/Input.vue'
    import Label from '@/Components/Label.vue'
    import Button from '@/Components/Button.vue'
    import Error from '@/Components/InputError.vue'
    import Helpers from '@/Mixins/Helpers'

    export default {
        components: {
            Modal,
            Input,
            Label,
            Button,
            Error,
        },
        data() {
            return {
                title: null,
                form: null,
                businessEmail: null,
            }
        },
        methods: {
            submit() {
                if(this.form.id) {
                    this.form.processing = true;
                    this.form.put(route('retail.dashboard.business.emails.update', [this.getSelectedModuleValue(), this.form.id, this.getSelectedBusinessValue()]), {
                        errorBag: 'emails',
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                        },
                        onError: (response) => {
                            this.form.errors = response;
                        },
                        onFinish: () => {
                            this.form.processing = false;
                        }
                    })
                } else {
                    this.form.post(route('retail.dashboard.business.emails.store', [this.getSelectedModuleValue(), this.getSelectedBusinessValue()]), {
                        errorBag: 'emails',
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                        }
                    }) 
                }

            },        
        },
        mounted() {
            this.emitter.on('email-modal', (args) => {
                this.form = useForm({
                    id: args.businessEmail ? args.businessEmail.id : null,
                    personal_name: args.businessEmail ? args.businessEmail.personal_name : '',
                    title: args.businessEmail ? args.businessEmail.title : '',
                    email: args.businessEmail ? args.businessEmail.email : '',
                    business_id: args.businessEmail ? args.businessEmail.business_id : null,
                });
                this.title = args.businessEmail ? 'Edit Additional Email' : 'Create Additional Email'
                this.businessEmail = args.businessEmail
                $('#genericModal').modal('show')
            })
        },

        mixins: [Helpers]
    }
</script>

<style scoped>

</style>
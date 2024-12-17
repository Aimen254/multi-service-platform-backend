<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{title}}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="title" class="required" value="Title" />
                    <Input id="title" type="text"
                        :class="{'is-invalid border border-danger' : form.errors.title}" v-model="form.title" placeholder="Enter title" />
                    <error :message="form.errors.title"></error>
                </div>
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="minimum_amount" class="required" value="Minimum amount" />
                    <Input id="minimum_amount" type="number"
                        :class="{'is-invalid border border-danger' : form.errors.minimum_amount}" 
                        v-model="form.minimum_amount"
                        placeholder="Enter minimum amount" />
                    <error :message="form.errors.minimum_amount"></error>
                </div>
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="price" class="required" value="Price" />
                    <Input id="price" type="number"
                        :class="{'is-invalid border border-danger' : form.errors.price}" 
                        v-model="form.price"
                        placeholder="Enter price" />
                    <error :message="form.errors.price"></error>
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
    import Modal from '@/Components/Modal.vue'
    import Input from '@/Components/Input.vue'
    import Label from '@/Components/Label.vue'
    import Error from '@/Components/InputError.vue'
    import { useForm } from '@inertiajs/inertia-vue3'
    import Button from '@/Components/Button.vue'
    import Helpers from "@/Mixins/Helpers"

    export default {
        components: {
            Modal,
            Input,
            Label,
            Error,
            Button
        },
        data() {
            return {
                title: null,
                show: false,
                form: null,
                type: null,
                error: false,
                errors: null,
                loading: false,                
                businessId : '',
            }
        },
        
        methods: {
            submit() {
                this.loading = true;
                if (!this.form.id) {
                this.form.post(route('retail.dashboard.business.mailings.store', [this.getSelectedModuleValue(), this.form.business_id]), {
                    errorBag: 'mailing',
                    preserveScroll: false,
                    onSuccess: () => $('#genericModal').modal('hide'),
                })
            } else {
                this.form.put(route('retail.dashboard.business.mailings.update', [this.getSelectedModuleValue(), this.form.business_id, this.form.id]), {
                    errorBag: 'mailing',
                    preserveScroll: false,
                    onSuccess: () => $('#genericModal').modal('hide'),
                })          
                }
            },
        },
        mounted() {
            this.emitter.on('mailing-modal', (args) => {
                this.form = useForm({
                    id: args.mailing ? args.mailing : null,
                    title: args.mailing ? args.mailing.title : '',
                    minimum_amount: args.mailing ? args.mailing.minimum_amount : null,
                    price: args.mailing ? args.mailing.price : null,
                    business_id: args.businessId
                })
                this.title = args.mailing ? 'Edit Mail':'Create Mail';
                $('#genericModal').modal('show');
            });
        },
        mixins: [Helpers],
    }
</script>
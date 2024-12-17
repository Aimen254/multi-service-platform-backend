<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">
                Add Plan
            </h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="title" class="required" value="Name" />
                    <Input id="name" type="text"
                        :class="{'is-invalid border border-danger' : form.errors.name}" 
                        v-model="form.name" placeholder="Enter title" />
                    <error :message="form.errors.name"></error>
                </div>
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="price" class="required" value="Price" />
                    <Input id="price" type="number"
                        :class="{'is-invalid border border-danger' : form.errors.price}" 
                        v-model="(form.price)" placeholder="Enter title" />
                    <error :message="form.errors.price"></error>
                </div>
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="title" class="required" value="Interval" />
                        <select class="form-select form-select-solid" name="interval" id="interval" v-model="form.interval" :class="{'is-invalid border border-danger' : form.errors.interval}">
                            <option value="" disabled>Select Interval</option>
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                            <option value="year">Year</option>
                        </select>
                    <error :message="form.errors.interval"></error>
                </div>
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="title" value="Description" />
                    <textarea class="form-control form-control-lg form-control-solid" rows="4" placeholder="Shipping and return Policy" v-model="form.description" :class="{'is-invalid border border-danger' : form.errors.description}"></textarea>
                    <error :message="form.errors.description"></error>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        ref="submitButton">
                        <span class="indicator-label" v-if="!form.processing">
                            Save
                        </span>
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
    import Label from '@/Components/Label.vue'
    import { useForm } from '@inertiajs/inertia-vue3'
    import Input from '@/Components/Input.vue'
    import Error from '@/Components/InputError.vue'
    import Button from '@/Components/Button.vue'
    export default {
        props: [''],

        components: {
            Modal,
            Label,
            Input,
            Error,
            Button
        },

        data() {
            return {
                form: null,
                processing : false,
            }
        },

        methods: {
            async submit() {
                this.processing = true;
                if (this.form.id) {
                    this.form.put(route('dashboard.subscription.plan.update', this.form.id), {
                        errorBag: null,
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                            this.processing = false
                        },
                        onError: () => {}
                    })
                } else {
                    this.form.post(route('dashboard.subscription.plan.store'), {
                        errorBag: null,
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                            this.processing = false
                        },
                        onError: () => {}
                    })
                }
            },
        },

        mounted () {
            this.emitter.on('plan_modal', (args) => {
                $('#genericModal').modal('show'); 
                this.form = useForm({
                    id: args.plan ? args.plan.product.id : null,
                    priceId: args.plan ? args.plan.price.id : null,
                    name: args.plan ? args.plan.product.name : null,
                    description: args.plan ? args.plan.product.description : null,
                    price: args.plan ? (args.plan.price.unit_amount/100) : null,
                    interval: args.plan ? args.plan.price.recurring.interval : null,
                })  
            })
        },
    }
</script>
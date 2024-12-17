<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit" >
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="message" class="required" value="Message" />
                        <textarea id="messgage" class="form-control form-control-lg form-control-solid"
                            :class="{'is-invalid border border-danger' : form.errors.message}"  v-model="form.message" placeholder="Enter Message" rows="5" maxlength ="500"/>
                        <error :message="form.errors.message"></error>
                    </div>
                </div>

                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        >
                        <span class="indicator-label" v-if="!form.processing">Send</span>
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
import Helpers from '@/Mixins/Helpers'

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
        }
    },

    methods: {
        submit() {
            this.form.put(route('retail.dashboard.business.order.type.orders.update', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), this.form.id, 'active']), {
                errorBag: 'order',
                preserveScroll: true,
                onSuccess: () => $('#genericModal').modal('hide'),
            })
        }
    },

    mounted() {
        this.emitter.on('reject-modal', (args) => {
            this.form = useForm({
                message: null,
                id: args.order_status_form ? args.order_status_form.id : null,
                order_status_id: args.order_status_form ? args.order_status_form.order_status_id : null,
                charge_stripe: args.order_status_form ? args.order_status_form.charge_stripe : null,
            })
            this.title = 'Send A Rejection Reason'
            $('#genericModal').modal('show');
        })
    },
    mixins: [Helpers]
}
</script>

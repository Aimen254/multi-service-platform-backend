<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">
                Add Payment Method
            </h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="title" class="required" value="Name" />
                    <Input id="name" type="text"
                        :class="{'is-invalid border border-danger' : form.errors.user_name}" 
                        v-model="form.user_name" placeholder="Enter title" />
                    <error :message="form.errors.user_name"></error>
                </div>
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="Card" class="" value="Card" />
                    <div id="card-element" class=""></div>
                </div>
                <label class="form-check form-check-sm form-check-custom form-check-solid me-9 mt-5">
                    <input class="form-check-input" type="checkbox" name="save_card" v-model="form.save_card">
                    <span class="form-check-label" for="save_card">Do you want to save this card for future use?</span>
                </label>
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
    import { loadStripe } from '@stripe/stripe-js';
    // import { mounted } from 'vue3-timepicker'
    export default {
        props: ['stripeKey'],

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
                show: false,
                cardElement: {},
                stripe: {},
                stripePublishKey: this.stripeKey,
                processing : false,
                errors: null,
                businessUuid : localStorage.getItem('selectedBusiness') ? localStorage.getItem('selectedBusiness') : null
            }
        },

        methods: {
            async submit() {
                this.processing = true;
                let {paymentMethod, error} =  await this.stripe.createPaymentMethod (
                    {
                        type: 'card',
                        card: this.cardElement,
                    });
                if (error) {
                    this.errors = error
                    this.processing = false
                } else {
                    this.form.payment_method_id = paymentMethod.id
                    this.form.brand = paymentMethod.card.brand
                    this.form.country = paymentMethod.card.country
                    this.form.expiry_month = paymentMethod.card.exp_month
                    this.form.expiry_year = paymentMethod.card.exp_year
                    this.form.last_four = paymentMethod.card.last4
                    this.form.live_mode = paymentMethod.livemode
                    this.form.save_card = this.form.save_card ? this.form.save_card : false
                    this.form.post(route('dashboard.subscription.payment-method.store'), {
                        errorBag: null,
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                            this.processing = false
                        },
                        onError: () => {

                        }
                    })
                }
            },
            async initializeStripe() {
                // Stripe Card element Initialization starts
                this.stripe = await loadStripe(this.stripePublishKey);
                const elements = this.stripe.elements();
                this.cardElement = elements.create('card', {
                    classes: {  
                        base: ''
                    }
                })
                this.cardElement.mount('#card-element');
            }
        },

        mounted () {
            this.emitter.on('credit_card_model', (args) => {
                $('#genericModal').modal('show'); 
                this.initializeStripe()
                this.form = useForm({
                    user_name: args.card ? args.card.user_name : null,
                    payment_method_id: args.card ? args.card.payment_method_id : null,
                    brand: args.card ? args.card.brand : null,
                    country: args.card ? args.card.country : null,
                    expiry_month: args.card ? args.card.expiry_month : null,
                    expiry_year: args.card ? args.card.expiry_year : null,
                    last_four: args.card ? args.card.last_four : null,
                    live_mode: args.card ? args.card.live_mode : null,
                    save_card: args.card ? args.card.save_card : null,
                })  
            })
        },
    }
</script>
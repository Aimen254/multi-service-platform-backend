<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ coupon ? 'Update' : 'Add' }} Coupon</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="fv-row mb-4 fv-plugins-icon-container" v-if="!coupon">
                    <Label value="Select Coupon" class="required"/>
                    <select class="form-select text-capitalize form-select-solid"
                        :class="{'is-invalid border border-danger' : form.errors.coupon_id}"
                        v-model="form.coupon_id">
                            <option class="text-capitalize" value="" default disabled>Select Coupon</option>
                            <option class="text-capitalize" :value="code.id" v-for="code in activeCodes" :key="code.id">
                                <span v-if="code.discount_type == 'fixed'">{{code.text}}$</span>
                                <span v-else>{{code.text}}%</span>
                            </option>
                    </select>
                    <error :message="form.errors.coupon_id"></error>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        ref="submitButton">
                        <span class="indicator-label" v-if="!form.processing"> {{coupon ? 'Update' : 'Save'}}</span>
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
    import Helpers from '@/Mixins/Helpers'
    import moment from 'moment';

    export default {
        props: ['product'],

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
                codesList: null,
                coupon: null
            }
        },

        methods: {
            submit() {
                if (this.coupon) {
                    this.form.put(route('dashboard.product.coupons.update', [this.product.uuid, this.coupon.id]), {
                        errorBag: 'discount',
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                        }
                    })
                } else {
                    this.form.post(route('retail.dashboard.product.coupons.store', [this.getSelectedModuleValue(), this.product.uuid]), {
                        errorBag: 'discount',
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                        }
                    })
                }
            }
        },
        computed: {
            activeCodes: function() {
                return this.codesList.filter(function(c) {
                    var currentDate = new Date();
                    currentDate = moment(currentDate).format("YYYY-MM-DD")
                    return c.status == 'active' && c.end_date >= currentDate
                })
            } 
        },

        mounted () {
            this.emitter.on('discount-modal', (args) => {
                this.form = useForm({
                    coupon_id: args.coupon ? args.coupon.id : '',
                    business_uuid: this.getSelectedBusinessValue(),
                })
                this.codesList = args.codes
                this.coupon = args.coupon
                this.show = true
                $('#genericModal').modal('show');
            })
        },
        mixins: [Helpers]
    }
</script>
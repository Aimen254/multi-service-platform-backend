<template>
    <Modal :selectedId="categoryIds ? categoryIds: null">
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-6">
                        <Label for="code" class="required" value="Code" />
                        <Input id="code" type="text" :class="{'is-invalid border border-danger' : form.errors.code}"
                            v-model="form.code" autofocus autocomplete="name" placeholder="Enter Code" />
                        <error :message="form.errors.code"></error>
                    </div>
                    <div class="col-md-6">
                        <Label for="end_date" class="required" value="End Date" />
                        <Datepicker v-model="form.end_date" :format="'yyyy-MM-dd'" date-picker :disable-time="true"
                            :class="[form.errors.end_date ? 'is-invalid border border-danger' : '', 'start_date form-control form-control-md form-control-solid']"
                            class="form-control form-control-solid" :min-date="new Date()"></Datepicker>
                        <error :message="form.errors.end_date"></error>
                    </div>
                </div>

                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-6">
                        <Label for="Discount Type" class="required" value="Discount Type" />
                        <select v-model="form.discount_type" 
                            class="form-select text-capitalize form-select-solid">
                            <option class="text-capitalize">percentage</option>
                            <option class="text-capitalize">fixed</option>
                        </select>
                        <error :message="form.errors.discount_type"></error>
                    </div>
                    <div class="col-md-6">
                        <Label for="discount_value" class="required" value="Discount Value" />
                        <Input id="discount_value" type="number"
                            :class="{'is-invalid border border-danger' : form.errors.discount_value}"
                            v-model="form.discount_value" autofocus min="1" autocomplete="name"
                            placeholder="Enter discount value" />
                        <error :message="form.errors.discount_value"></error>
                    </div>
                </div>
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="Coupon Type" class="" value="Coupon Type" />
                        <select v-model="form.coupon_type" class="form-select form-select-solid text-capitalize">
                            <option class="capitalize">business</option>
                            <option class="capitalize">product</option>
                        </select>
                        <error :message="form.errors.coupon_type"></error>
                    </div>
                </div>
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div v-if="form.coupon_type == 'category'" class="col-md-12">
                        <Label for="categories_ids" class="" value="Categories" />
                        <select class="form-select form-select-solid form-select-modal-multiple" multiple="multiple">
                            <option v-for="(category,index) in categoriesList" :value="category.id" :key="index"
                                placeholder="Categories Coupon">
                                {{category.name}}
                            </option>
                        </select>
                        <error :message="form.errors.category_ids"></error>
                    </div>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
    import Helpers from "@/Mixins/Helpers";
    import { useForm } from '@inertiajs/inertia-vue3'
    import Modal from '@/Components/Modal.vue'
    import Label from '@/Components/Label.vue'
    import Input from '@/Components/Input.vue'
    import Error from '@/Components/InputError.vue'
    import Button from '@/Components/Button.vue'
    import Select2 from 'vue3-select2-component';
    import '@vuepic/vue-datepicker/dist/main.css'
    import Datepicker from '@vuepic/vue-datepicker';

    export default {
        components: {
            Modal,
            Label,
            Input,
            Error,
            Button,
            Datepicker,
            Select2,
        },

        data() {
            return {
                title: null,
                show: false,
                maxWidth: '2xl',
                form: null,
                type: null,
                error: false,
                errors: null,
                loading: false,                
                maxWidth: '2xl',
                couponTypes : [],
                DiscountTypes : ['Percentage', 'Fixed'],
                Individual_item_status : false,
                subscription_coupon_status : false,
                delivery_type : '',
                businessId : '',
                categoriesList: '',
                categoryIds: '',
            }
        },

        methods: {        
            submit() {
                this.form.category_ids = $('.form-select-modal-multiple').val();
                this.loading = true;
                if (!this.form.id) {
                this.form.post(route('retail.dashboard.business.coupons.store', [this.getSelectedModuleValue(), this.businessUuid]), {
                    errorBag: 'coupon',
                    preserveScroll: false,
                    onSuccess: () => {
                        $('.form-select-modal-multiple').val(null)
                        $('#genericModal').modal('hide')
                    }
                })
            } else {
                this.form.category_ids = $('.form-select-modal-multiple').val();
                this.form.put(route('retail.dashboard.business.coupons.update', [this.getSelectedModuleValue(), this.businessUuid, this.form.id]), {
                    errorBag: 'coupon',
                    preserveScroll: false,
                    onSuccess: () => {
                        $('.form-select-modal-multiple').val(null)
                        this.category_ids = null;
                        $('#genericModal').modal('hide')
                    }
                })          
            }
            },

            checkDeliveryType()
            {
                if (this.delivery_type != 'No delivery') {
                    this.couponTypes =  ['Order Subtotal', 'Delivery Fee', 'Subscription Coupon'];
                } else {
                    this.couponTypes =  ['Order Subtotal', 'Subscription Coupon'];
                }
            },
            getTimeZone(){
                const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                return timezone;
            }
        },

        mounted() {
            this.emitter.on('coupon-model', (args) => {
                this.form = useForm({
                    id: args.coupons.id ? args.coupons.id : '',
                    code : args.coupons.code ? args.coupons.code : null,
                    minimum_purchase : args.coupons.minimum_purchase ? args.coupons.minimum_purchase : null,
                    limit : args.coupons.limit ? args.coupons.limit : null,
                    discount_type : args.coupons.discount_type ? args.coupons.discount_type : 'percentage',
                    discount_value : args.coupons.discount_value ? args.coupons.discount_value : null,
                    start_date : args.coupons.start_date ? new Date(args.coupons.start_date) : null,
                    end_date : args.coupons.end_date ? new Date(args.coupons.end_date) : null,
                    coupon_type : args.coupons.coupon_type ? args.coupons.coupon_type : 'business',
                    timeZone : this.getTimeZone(),
                    category_ids: args.coupons.categories ?  args.coupons.categories.map(category => category.id) : [],
                    businessUuid: args.businessUuid

                })
                this.categoryIds = args.coupons.categories ? args.coupons.categories.map(category => category.id) : null;
                this.title = args.coupons.id ? 'Edit Coupon':'Create Coupon';
                this.type = args.type;
                this.businessUuid = args.businessUuid;
                this.categoriesList = args.productCategoriesList;
                this.delivery_type = args.delivery_type;  
                this.checkDeliveryType();
                $('.form-select-modal-multiple').val(null);
                $('#genericModal').modal('show');
            });
        },
        mixins: [Helpers]
    }
</script>
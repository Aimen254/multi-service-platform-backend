<template>

    <Head title="Discount" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Discount`" :path="`Products - ${product?.name}`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="product" :type="type" :width="'w-lg-340px'" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <form class="form" v-if="form" @submit.prevent="submit">
                    <div class="card card-flush py-4">
                        <div class="card-body py-3 row">
                            <div class="col-lg-6 py-2">
                                <Label for="Discount Type" class="" value="Discount Type" />
                                <select v-model="form.discount_type" class="form-select form-select-solid text-capitalize">
                                    <option class="capitalize">percentage</option>
                                    <option class="capitalize">fixed</option>
                                </select>
                                <error :message="form.errors.discount_type"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="discount_price" class="" value="Discount Price" />
                                <Input id="discount_price" type="number"
                                    :class="{'is-invalid border border-danger' : form.errors.discount_value}"
                                    v-model="form.discount_value" autofocus min="1" autocomplete="name"
                                    placeholder="Enter Discount Price" />
                                <error :message="form.errors.discount_value"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="discount_start_date" class="" value="Discount Start Date" />
                                <Datepicker v-model="form.discount_start_date" :format="'yyyy-MM-dd'" date-picker :disable-time="true"
                                    :class="[form.errors.discount_start_date ? 'is-invalid border border-danger' : '', 'start_date form-control form-control-md form-control-solid']"
                                    class="form-control form-control-solid" :alt-position="customPosition" :min-date="new Date()"></Datepicker>
                                <error :message="form.errors.discount_start_date"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="eiscount_end_date" class="" value="Discount End Date" />
                                <Datepicker v-model="form.discount_end_date" :format="'yyyy-MM-dd'" date-picker :disable-time="true"
                                    :class="[form.errors.discount_end_date ? 'is-invalid border border-danger' : '', 'start_date form-control form-control-md form-control-solid']"
                                    class="form-control form-control-solid" :min-date="new Date()" :alt-position="customPosition"></Datepicker>
                                <error :message="form.errors.discount_end_date"></error>
                            </div>
                            <div class="col-lg-12 py-2 d-flex justify-content-end">
                                <Button type="submit" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                                    <span class="indicator-label" v-if="!form.processing">Update</span>
                                    <span class="indicator-progress" v-if="form.processing">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import ProductSidebar from '../Partials/ProductSideMenu.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Helpers from '@/Mixins/Helpers'
import Error from '@/Components/InputError.vue'
import '@vuepic/vue-datepicker/dist/main.css'
import Datepicker from '@vuepic/vue-datepicker';

export default {
    props: ['product', 'type'],

    components: {
        Head,
        AuthenticatedLayout,
        Breadcrumbs,
        ProductSidebar,
        Input,
        Label,
        Button,
        Error,
        Datepicker,
    },

    data() {
        return {
            form: null
        }
    },
    methods: {
        submit() {
            this.form.put(route('retail.dashboard.product.discount.update', [this.getSelectedModuleValue(), this.product.uuid]), {
                errorBag: "product",
                preserveScroll: true,
            });
        },
        getTimeZone() {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            return timezone;
        },

        customPosition() {
            return { top: 50, left: 0 };
        }
    },

    mounted() {
        this.form = this.$inertia.form({
            discount_type: this.product.discount_type ? this.product.discount_type : 'percentage',
            discount_value: this.product.discount_value,
            discount_start_date: this.product.discount_start_date ? new Date(this.product.discount_start_date) : null,
            discount_end_date: this.product.discount_end_date ? new Date(this.product.discount_end_date) : null,
            timeZone: this.getTimeZone(),
        });
    },

    mixins: [Helpers]
}
</script>
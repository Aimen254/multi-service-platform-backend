<template>
    <Head title="Product Tax" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Product Tax`" :path="`Products - ${product?.name}`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="product" :type="type" :width="'w-lg-225px'" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <form class="form" v-if="form" @submit.prevent="submit">
                    <div class="card card-flush py-4">
                        <div class="card-body py-3 row">
                            <div class="col-lg-12 py-2">
                                <Label for="tax_type" class="" value="Tax Percentage" />
                                <Input type="number" :class="[
                                        form.errors.tax_percentage ? 'border border-danger' : '',]"
                                    v-model="form.tax_percentage" />
                                <error :message="form.errors.tax_percentage"></error>
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
import Datepicker  from 'vue3-datepicker'

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
            this.form.put(route('retail.dashboard.product.taxes.update', [this.getSelectedModuleValue(), this.product.uuid, this.product.uuid]), {
                errorBag: "product",
                preserveScroll: true,
            });
        }
    },

    mounted() {
        this.form = this.$inertia.form({
            // tax_type: this.product.tax_type ? this.product.tax_type : 'Tax not included on price',
            tax_percentage: this.product.tax_percentage ? this.product.tax_percentage : '',
        });
    },

    mixins: [Helpers]
}
</script>
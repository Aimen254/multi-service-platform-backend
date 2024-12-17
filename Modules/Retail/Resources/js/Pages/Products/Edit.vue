<template>
    <Head title="Edit Product" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Product`" :path="`Products - ${form?.name}`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="product" :width="'w-lg-820px'" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <form class="form" v-if="form" @submit.prevent="submit">
                    <div class="card card-flush py-4">
                        <div class="card-body py-3 row">
                            <div class="col-lg-12 py-2">
                                <Label for="name" class="required" value="Name" />
                                <Input id="name" type="text" :class="{ 'is-invalid border border-danger': form.errors.name }"
                                    v-model="form.name" autofocus autocomplete="name" placeholder="Enter Name" />
                                <error :message="form.errors.name"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="price" class="required" value="Price" />
                                <Input id="price" type="number" min="0" step="0.01"
                                    :class="{ 'is-invalid border border-danger': form.errors.price }" v-model="form.price"
                                    autofocus autocomplete="price" placeholder="Enter Price" />
                                <error :message="form.errors.price"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="stock" class="required" value="Level Two Tags" />
                                <select2 class="form-control-md text-capitalize form-control-solid" :class="{
                                    'is-invalid border border-danger':
                                        form.errors.level_two_tag,
                                }" v-model="form.level_two_tag" :options="levelTwoTags"
                                    :placeholder="'Select Level Two tag'" @select="getLevelThreeTags($event, 2)" />
                                <error :message="form.errors.level_two_tag"></error>
                            </div>
                            <div class="col-lg-6 py-2" v-if="form.level_two_tag">
                                <Label for="stock" class="required" value="Level Three Tags" />
                                <select class="select form-control-md text-capitalize form-control-solid" :class="{
                                    'is-invalid border border-danger':
                                        form.errors.level_three_tag,
                                }" v-model="form.level_three_tag"></select>
                                <error :message="form.errors.level_three_tag"></error>
                            </div>
                            <div class="col-lg-6 py-2" v-if="form.level_three_tag">
                                <Label for="stock" class="required" value="Level Four Tags" />
                                <multiselect v-model="form.level_four_tags" :options="levelFourTags" :loading="isLoading"
                                    :multiple="true" :close-on-select="false" :clear-on-select="false"
                                    :preserve-search="true" placeholder="Select Level Four Tags" label="text" track-by="id"
                                    :preselect-first="false" :internal-search="false" :limit="5" :options-limit="300"
                                    :searchable="true" :show-labels="false" @search-change="asyncFind" @Open="asyncFind"></multiselect>
                                <error :message="form.errors.level_four_tags"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="sku" value="SKU" />
                                <Input id="sku" type="text" :class="{ 'is-invalid border border-danger': form.errors.sku }"
                                    v-model="form.sku" autofocus autocomplete="sku" placeholder="Enter sku code" />
                                <error :message="form.errors.sku"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="stock" value="Stock" class="required" />
                                <Input id="stock" type="number" min="-1"
                                    :class="{ 'is-invalid border border-danger': form.errors.stock }" v-model="form.stock"
                                    autofocus autocomplete="stock" placeholder="Enter Stock" />
                                <error :message="form.errors.stock"></error>
                                <div class="mt-2">-1 means infinite stock</div>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="weight" value="Weight" />
                                <Input id="weight" type="number" min="0" step="0.1"
                                    :class="{ 'is-invalid border border-danger': form.errors.weight }"
                                    @keyup="weightUnit($event)" v-model="form.weight" autofocus autocomplete="weight"
                                    placeholder="Enter Weight" />
                                <error :message="form.errors.weight"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="weight" value="Weight unit" />
                                <select v-model="form.weight_unit" class="form-select form-select-solid">
                                    <option></option>
                                    <option class="text-capitalize">kg</option>
                                    <option class="text-capitalize">pound</option>
                                </select>
                                <error :message="form.errors.weight_unit"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="available_items" value="Available items" />
                                <Input id="available_items" type="number" min="0"
                                    :class="{ 'is-invalid border border-danger': form.errors.available_items }"
                                    v-model="form.available_items" autofocus autocomplete="available_items"
                                    placeholder="Enter Available Items" />
                                <error :message="form.errors.available_items"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="package_count" value="Package count" />
                                <Input id="package_count" type="number" min="0"
                                    :class="{ 'is-invalid border border-danger': form.errors.package_count }"
                                    v-model="form.package_count" autofocus autocomplete="package_count"
                                    placeholder="Enter Package Count" />
                                <error :message="form.errors.package_count"></error>
                            </div>
                            <div class="col-lg-12 py-3">
                                <Label for="description" value="Description" />
                                <textarea v-model="form.description" class="form-control form-control-solid" rows="3"
                                    placeholder="Enter Product Description"></textarea>
                            </div>
                            <div class="col-lg-6 py-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckIsFeatured"
                                        :checked="form.is_featured" v-model="form.is_featured" />
                                    <label class="form-check-label" for="flexCheckIsFeatured">
                                        Featured ?
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-6 py-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckIsDeliverable"
                                        :checked="form.is_deliverable" v-model="form.is_deliverable" />
                                    <label class="form-check-label" for="flexCheckIsDeliverable">
                                        Deliverable ?
                                    </label>
                                </div>
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
import ProductSidebar from './Partials/ProductSideMenu.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Helpers from '@/Mixins/Helpers'
import Error from '@/Components/InputError.vue'
import Datepicker from 'vue3-datepicker'

export default {
    props: ['product', 'levelTwoTags', 'productLevelTwoTag'],

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
            form: null,
            levelThreeTags: [],
            levelFourTags: [],
        }
    },
    methods: {
        submit() {
            this.form.put(route('retail.dashboard.business.products.update', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), this.product.uuid]), {
                errorBag: "product",
                preserveScroll: true,
            });
        },
        getTimeZone() {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            return timezone;
        },
        weightUnit(event) {
            event.target.value > 0 ? this.form.weight_unit = 'kg' : this.form.weight_unit = '';
        },
        getLevelThreeTags(tag, level) {
            this.form.level_three_tag = null
            this.levelFourTags = []
            this.form.level_four_tags = []
            let parameters = [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level];
            var vm = this;
            // ajax search
            setTimeout(() => {
                var select2 = $(".select").select2({
                    placeholder: "Select Level 3",

                    // ajax request
                    ajax: {
                        url: route("retail.dashboard.business.product.tags", parameters),
                        dataType: "json",
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search query
                                page: params.page,
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.tags.data.map(function (item) {
                                    return item
                                }),
                                pagination: {
                                    more: params.page * 50 < data.tags.data.total,
                                },
                            };
                        },
                        cache: true,
                    },
                    minimumInputLength: 0, // minimum characters required to trigger search
                });

                // selected value
                window.axios.get(route('retail.dashboard.business.product.tags', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level]), {
                    params: {
                        product: this.product.id
                    }
                })
                    .then((response) => {
                        vm.form.level_three_tag = response.data.productLevelThreeTag ? response.data.productLevelThreeTag.id : null
                        var selectedLevelThree = response.data.productLevelThreeTag ? response.data.productLevelThreeTag : null
                        var option = new Option(selectedLevelThree.text, selectedLevelThree.id, true, true);
                        select2.append(option).trigger('change');
                        vm.getSelectedLevelFourTag(vm.form.level_three_tag, 3)
                    })
                    .catch(error => {
                        vm.$notify({
                            group: "toast",
                            type: 'error',
                            text: error.response.data.message
                        }, 3000)
                    });
                $(".select").on("select2:select", function (e) {
                    vm.form.level_three_tag = e.params.data.id
                    vm.levelFourTags = []
                    vm.form.level_four_tags = [],
                    vm.getSelectedLevelFourTag(vm.form.level_three_tag, 3)
                });
            }, 0);
        },
        asyncFind: _.debounce(function (query) {
            // if (query.length < 1) return;
            this.getLevelFourTags(this.form.level_three_tag, 3, query)
        }, 250),
        getLevelFourTags(tag, level, query) {
            // this.levelFourTags = []
            window.axios.get(route('retail.dashboard.business.product.tags', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level]), {
                params: {
                    levelTwoTag: this.form.level_two_tag,
                    keyword: query
                }
            }).then((response) => {
                this.levelFourTags = response.data.tags
            })
                .catch(error => {
                    this.$notify({
                        group: "toast",
                        type: 'error',
                        text: error.response.data.message
                    }, 3000)
                });
        },

        getSelectedLevelFourTag(tag, level) {
            window.axios.get(route('retail.dashboard.business.product.tags', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level]), {
                params: {
                    levelTwoTag: this.form.level_two_tag,
                    product: this.product.id,
                }
            }).then((response) => {
               
                this.form.level_four_tags = response.data.productLevelFourTag.length > 0 ?  response.data.productLevelFourTag : []
            })
                .catch(error => {
                    this.$notify({
                        group: "toast",
                        type: 'error',
                        text: error.response.data.message
                    }, 3000)
                });
        }
    },

    mounted() {
        this.form = this.$inertia.form({
            name: this.product.name,
            price: this.product.price,
            sku: this.product.sku,
            weight: this.product.weight,
            stock: this.product.stock,
            is_featured: this.product.is_featured ? true : false,
            is_deliverable: this.product.is_deliverable ? true : false,
            weight_unit: this.product.weight_unit ? this.product.weight_unit : '',
            package_count: this.product.package_count,
            available_items: this.product.available_items,
            discount_type: this.product.discount_type ? this.product.discount_type : 'percentage',
            discount_price: this.product.discount_price,
            discount_start_date: this.product.discount_start_date ? new Date(this.product.discount_start_date) : null,
            discount_end_date: this.product.discount_end_date ? new Date(this.product.discount_end_date) : null,
            timeZone: this.getTimeZone(),
            description: this.product.description,
            level_two_tag: this.productLevelTwoTag ? this.productLevelTwoTag.id : this.levelTwoTags.length == 1 ? this.levelTwoTags[0].id : null,
            level_three_tag: null,
            level_four_tags: [],
            type: 'basic_information',
        });
        this.form.level_two_tag ? this.getLevelThreeTags(this.form.level_two_tag, 2) : null
    },

    mixins: [Helpers]
}
</script>
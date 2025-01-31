<template>
    <Head title="Edit Boat" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Boat`" :path="`Boat - ${product?.name}`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="product" :width="'w-lg-820px'" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <form class="form" v-if="form" @submit.prevent="submit">
                    <div class="card card-flush py-4">
                        <div class="card-body py-3 row">
                            <div class="col-lg-6 py-2">
                                <Label for="name" class="required" value="Type" />
                                <select v-model="form.type" class="form-select form-select-solid"
                                    :class="{ 'is-invalid border border-danger': form.errors.name }">
                                    <option value="new" class="text-capitalize">New</option>
                                    <option value="used" class="text-capitalize">Used</option>
                                </select>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="price" value="Price" />
                                <Input id="price" type="number" min="0" step="0.01"
                                    :class="{ 'is-invalid border border-danger': form.errors.price }" v-model="form.price"
                                    autofocus autocomplete="price" placeholder="Enter price" />
                                <error :message="form.errors.price"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="price" value="Trim" />
                                <Input id="price" type="text"
                                    :class="{ 'is-invalid border border-danger': form.errors.trim }" v-model="form.trim"
                                    autofocus autocomplete="price" placeholder="Enter trim" />
                                <error :message="form.errors.trim"></error>
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
                                }" v-model="form.level_three_tag">
                                </select>

                                <error :message="form.errors.level_three_tag"></error>
                            </div>
                            <div class="col-lg-6 py-2" v-if="form.level_three_tag > 0">
                                <Label for="stock" class="required" value="Level Four Tags" />
                                <select class="select-level-four form-control-md text-capitalize form-control-solid select2-hidden-accessible" :class="{
                                    'is-invalid border border-danger':
                                        form.errors.level_four_tags,
                                }" v-model="form.level_four_tags"></select>
                                <error :message="form.errors.level_four_tags"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="price" class="required" value="Year" />
                                <Datepicker v-model="form.year" year-picker
                                    :class="{ 'is-invalid border border-danger': form.errors.year }"
                                    class="form-control form-control-solid"></Datepicker>

                                <error :message="form.errors.year"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="sku" value="Stock #" />
                                <Input id="sku" type="text"
                                    :class="{ 'is-invalid border border-danger': form.errors.stock_no }"
                                    v-model="form.stock_no" autofocus autocomplete="sku" placeholder="Enter stock number" />
                                <error :message="form.errors.stock_no"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="sku" value="SKU" />
                                <Input id="sku" type="text" :class="{ 'is-invalid border border-danger': form.errors.sku }"
                                    v-model="form.sku" autofocus autocomplete="sku" placeholder="Enter sku code" />
                                <error :message="form.errors.sku"></error>
                            </div>
                            <div class="col-lg-12 py-2">
                                <Label for="weight" value="Notes" />
                                <textarea class="form-control form-control-lg form-control-solid" rows="4"
                                    v-model="form.sellers_notes"></textarea>
                                <error :message="form.errors.sellers_notes"></error>
                            </div>

                            <div class="col-lg-6 fv-row  fv-plugins-icon-container">
                                <div class="form-check pt-2">
                                    <input class="form-check-input" type="checkbox" value="" :checked="form.is_featured"
                                        v-model="form.is_featured" />
                                    <label class="form-check-label fw-bolder" for="flexCheckDefault">
                                        Featured
                                    </label>
                                    <error :message="form.errors.is_featured"></error>
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
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

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
            selectedLevelThree: null
        }
    },
    methods: {
        submit() {
            this.form.put(route('boats.dashboard.dealership.boats.update', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), this.product.uuid]), {
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
            this.levelFourTags = []
            this.meta = null
            let parameters = [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level];
            var vm = this;
            // ajax search
            setTimeout(() => {
                var select2 = $(".select").select2({
                    placeholder: "Select Level 3",

                    // ajax request
                    ajax: {
                        url: route("boats.dashboard.dealership.boat.tags", parameters),
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
                window.axios.get(route('boats.dashboard.dealership.boat.tags', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level]), {
                    params: {
                        product: this.product.id
                    }
                })
                    .then((response) => {
                        vm.form.level_three_tag = response.data.productLevelThreeTag ? response.data.productLevelThreeTag.id : null
                        vm.selectedLevelThree = response.data.productLevelThreeTag ? response.data.productLevelThreeTag : null
                        var option = new Option(vm.selectedLevelThree.text, vm.selectedLevelThree.id, true, true);
                        select2.append(option).trigger('change');
                        vm.getLevelFourTags(vm.form.level_three_tag, 3)
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
                    vm.getLevelFourTags(vm.form.level_three_tag, 3)
                });
            }, 0);
        },
        getLevelFourTags(tag, level) {
            this.levelFourTags = []
            this.form.level_four_tags = null
            let parameters = [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level];
            var vm = this;
            setTimeout(() => {
                var levelFourSelect = $(".select-level-four").select2({
                    placeholder: "Select Level 4",
                    // ajax request
                    ajax: {
                        url: route("boats.dashboard.dealership.boat.tags", parameters),
                        dataType: "json",
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search query
                                page: params.page,
                                levelTwoTag: vm.form.level_two_tag
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
                window.axios.get(route('boats.dashboard.dealership.boat.tags', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level]), {
                    params: {
                        levelTwoTag: this.form.level_two_tag,
                        product: this.product.id
                    }
                }).then((response) => {
                    vm.form.level_four_tags = response.data.productLevelFourTag ? response.data.productLevelFourTag.id : null
                    var levelFourSelected =  response.data.productLevelFourTag ? response.data.productLevelFourTag : null
                    var option = new Option(levelFourSelected.text, levelFourSelected.id, true, true);
                    levelFourSelect.append(option).trigger('change');
                })
                    .catch(error => {
                        this.$notify({
                            group: "toast",
                            type: 'error',
                            text: error.response.data.message
                        }, 3000)
                    });

                //   get selected value
                $(".select-level-four").on("select2:select", function (e) {
                    vm.form.level_four_tags = e.params.data.id
                });
            }, 0);
        }
    },

    mounted() {
        this.form = this.$inertia.form({
            price: this.product.price,
            sku: this.product.sku,
            discount_type: this.product.discount_type ? this.product.discount_type : 'percentage',
            discount_price: this.product.discount_price,
            discount_start_date: this.product.discount_start_date ? new Date(this.product.discount_start_date) : null,
            discount_end_date: this.product.discount_end_date ? new Date(this.product.discount_end_date) : null,
            timeZone: this.getTimeZone(),
            description: this.product.description,
            level_two_tag: this.productLevelTwoTag ? this.productLevelTwoTag.id : this.levelTwoTags.length == 1 ? this.levelTwoTags[0].id : null,
            level_three_tag: null,
            level_four_tags: null,
            type: this.product.vehicle?.type ? this.product.vehicle.type : 'new',
            trim: this.product.vehicle?.trim ? this.product.vehicle.trim : '',
            year: this.product.vehicle?.year ? this.product.vehicle.year : '',
            stock_no: this.product.vehicle?.stock_no ? this.product.vehicle.stock_no : '',
            sellers_notes: this.product.vehicle?.sellers_notes ? this.product.vehicle.sellers_notes : '',
            is_featured: this.product.is_featured ? true : false

        });
        this.form.level_two_tag ? this.getLevelThreeTags(this.form.level_two_tag, 2) : null
    },

    mixins: [Helpers]
}
</script>

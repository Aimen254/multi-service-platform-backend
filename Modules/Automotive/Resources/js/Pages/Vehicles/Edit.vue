<template>
    <Head title="Edit Vehicle" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Vehicle`" :path="`Vehicle - ${product?.name}`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :product="product" :width="'w-lg-820px'" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <form class="form" v-if="form" @submit.prevent="submit">
                    <div class="card card-flush py-4">
                        <div class="card-body py-3 row">
                            <div class="col-lg-12 py-2">
                                <Label for="name" class="required" value="Name" />
                                <Input id="name" type="text"
                                    :class="{ 'is-invalid border border-danger': form.errors.name }" v-model="form.name"
                                    autofocus autocomplete="name" placeholder="Enter name" />
                                <error :message="form.errors.name"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="name" class="required" value="Type" />
                                <select v-model="form.type" class="form-select form-select-solid"
                                    :class="{ 'is-invalid border border-danger': form.errors.name }">
                                    <option value="new" class="text-capitalize">New</option>
                                    <option value="used" class="text-capitalize">Used</option>
                                </select>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="price" class="required" value="Price" />
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
                            <div class="row" v-for="(hierarchy, index) in hierarchies" :key="index">
                                <div class="col-md-11">
                                    <div class="row">
                                        <div class="col-md-4 py-2">
                                            <Label :class="{'required' : index == 0}" value="Level Two Tags" />
                                            <select2 :id="`level_two_tag_${index}`" class="form-control-md text-capitalize form-control-solid"
                                                v-model="hierarchy.level_two_tag"
                                                :options="levelTwoTags"
                                                :placeholder="'Select Level Two tag'"
                                                :class="{'is-invalid border border-danger': form.errors[`hierarchies.${index}.level_two_tag`]}"
                                                @select="handleLevelChange($event, 2, index)"
                                                    />
                                                <error :message="form.errors[`hierarchies.${index}.level_two_tag`]"></error>
                                        </div>

                                        <div class="col-lg-4 py-2" v-if="hierarchy.level_two_tag">
                                            <Label :for="`level_three_tag_${index}`" :class="{'required' : index == 0}" value="Level Three Tags" />
                                            <select :id="`level_three_tag_${index}`" class="select form-control-md text-capitalize form-control-solid"
                                                :class="{'is-invalid border border-danger': form.errors[`hierarchies.${index}.level_three_tag`]}"
                                                v-model="hierarchy.level_three_tag">
                                            </select>
                                            <error :message="form.errors[`hierarchies.${index}.level_three_tag`]"></error>
                                        </div>

                                        <div class="col-lg-4 py-2" v-if="hierarchy.level_three_tag">
                                            <Label :for="`level_four_tags_${index}`" :class="{'required' : index == 0}" value="Level Four Tags" />
                                            <select :id="`level_four_tags_${index}`" class="select-level-four form-control-md text-capitalize form-control-solid select2-hidden-accessible"
                                                :class="{'is-invalid border border-danger': form.errors[`hierarchies.${index}.level_four_tags`]}"
                                                v-model="hierarchy.level_four_tags">
                                            </select>
                                            <error :message="form.errors[`hierarchies.${index}.level_four_tags`]"></error>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div @click="removeHierarchy(index)" v-if="index > 0" class="mt-11">
                                        <div class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                            data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="bottom" data-bs-original-title="remove">
                                                <i class="fa fa-trash-alt text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                                <hr class="m-3" v-if="index < hierarchies.length - 1">
                            </div>

                            <div @click="addHierarchy()">
                                <div class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                    data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right" data-bs-original-title="add more hierarchies">
                                    <span class="svg-icon svg-icon-3">
                                        <inline-svg :src="'/images/icons/add_more.svg'" />
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-6 py-2">
                                <Label for="price" class="required" value="Mileage" />
                                <Input id="price" type="number"
                                    :class="{ 'is-invalid border border-danger': form.errors.mileage }"
                                    v-model="form.mileage" autofocus autocomplete="price" placeholder="Enter mileage" />
                                <error :message="form.errors.mileage"></error>

                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="price" class="required" value="VIN" />
                                <Input id="price" type="text"
                                    :class="{ 'is-invalid border border-danger': form.errors.vin }" v-model="form.vin"
                                    autofocus autocomplete="price" placeholder="Enter vin" />
                                <error :message="form.errors.vin"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="price" class="required" value="Year" />
                                <Datepicker v-model="form.year" year-picker
                                    :class="{ 'is-invalid border border-danger': form.errors.year }"
                                    class="form-control form-control-solid"></Datepicker>

                                <error :message="form.errors.year"></error>
                            </div>
                            <div class="col-lg-6 py-2">
                                <Label for="sku" value="MPG" />
                                <Input id="sku" type="text" :class="{ 'is-invalid border border-danger': form.errors.mpg }"
                                    v-model="form.mpg" autofocus autocomplete="sku" placeholder="Enter mpg" />
                                <error :message="form.errors.mpg"></error>
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
                                    v-model="form.sellers_notes">
                                </textarea>
                                <error :message="form.errors.sellers_notes"></error>
                            </div>
                            <div class="col-lg-6 fv-row  fv-plugins-icon-container">
                                <div class="form-check pt-2">
                                    <input class="form-check-input" type="checkbox" value="" :checked="form.is_featured"
                                        v-model="form.is_featured" />
                                    <label class="form-check-label fw-bolder" for="flexCheckDefault">
                                        Featured Vehicle
                                    </label>
                                    <error :message="form.errors.is_featured"></error>
                                </div>
                            </div>
                            <div class="col-lg-6 py-2 d-flex justify-content-end">
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
import InlineSvg from 'vue-inline-svg'

export default {
    props: ['product', 'levelTwoTags', 'productLevelTwoTag', 'productBodyStyle', 'productHierarchies'],

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
        InlineSvg
    },

    data() {
        return {
            form: null,
            levelThreeTags: [],
            levelFourTags: [],
            selectedLevelThree: null,
            hierarchies: [] 
        }
    },
    methods: {
        addHierarchy() {
            this.hierarchies.push({
                level_two_tag: null,
                level_three_tag: null,
                level_four_tags: null,
            })
        },

        removeHierarchy(index) {
            this.hierarchies.splice(index, 1)
        },

        submit() {
            this.form.put(route('automotive.dashboard.dealership.vehicles.update', [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), this.product.uuid]), {
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

        handleLevelChange(e, level, index) {
            this.hierarchies[index].level_three_tag = null;
            this.hierarchies[index].level_four_tags = null;
            $(`#level_three_tag_${index}`).val(null).trigger('change');
            $(`#level_three_tag_${index}`).empty().trigger('change');

            // Reset the Level 4 Select2 element
            $(`#level_four_tags_${index}`).val(null).trigger('change');
            $(`#level_four_tags_${index}`).empty().trigger('change');
            if(level == 2) {
                this.getLevelThreeTags(e?.id, level, index)
            }  else {
                this.getLevelFourTags(e?.id, level, index)
            }
        },

        getLevelThreeTags(tag, level, index, levelThree = null, levelFourTag = null) {
            this.meta = null
            let parameters = [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level];
            var vm = this;
            // ajax search
            setTimeout(() => {
                var select2 = $(`#level_three_tag_${index}`).select2({
                    placeholder: "Select Level 3",
                    // ajax request
                    ajax: {
                        url: route("automotive.dashboard.dealership.vehicle.tags", parameters),
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

                if(levelThree) {
                    var option = new Option(levelThree.text, levelThree.id, true, true);
                    select2.append(option).trigger('change');
                    this.hierarchies[index].level_three_tag = levelThree.id

                    // call level four 
                    vm.getLevelFourTags(levelThree.id, 3, index, levelFourTag)
                }
                select2.on("select2:select", function (e) {
                    vm.hierarchies[index].level_three_tag = e.params.data.id;
                    vm.getLevelFourTags(e.params.data.id, 3, index);
                });
            }, 0);
        },
        getLevelFourTags(tag, level, index = null, levelFour = null) {
            this.levelFourTags = []
            this.hierarchies[index].level_four_tags = null;
            let parameters = [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level];
            var vm = this;
            setTimeout(() => {
                var levelFourSelect = $(`#level_four_tags_${index}`).select2({
                    placeholder: "Select Level 4",
                    // ajax request
                    ajax: {
                        url: route("automotive.dashboard.dealership.vehicle.tags", parameters),
                        dataType: "json",
                        delay: 250,
                        data: function (params) {
                            return {
                                keyword: params.term, // search query
                                page: params.page,
                                levelTwoTag: vm.hierarchies[index].level_two_tag
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

                if(levelFour) {
                    vm.hierarchies[index].level_four_tags = levelFour?.id
                    var option = new Option(levelFour.text, levelFour.id, true, true);
                    levelFourSelect.append(option).trigger('change');
                }
                
                //   get selected value
                levelFourSelect.on("select2:select", function (e) {
                    vm.hierarchies[index].level_four_tags = e.params.data.id;
                });
            }, 0);
        }
    },

    mounted() {
        // hierarchies 
        this.productHierarchies.forEach((item, index) => {
            this.hierarchies.push({
                level_two_tag: item?.L2,
                level_three_tag: item?.L3?.id,
                level_four_tags: item?.L4?.id
            })
            item?.L2 ? this.getLevelThreeTags(item.L2, 2, index, item.L3, item.L4) : null
        })

        this.form = this.$inertia.form({
            name: this.product.name,
            price: this.product.price,
            sku: this.product.sku,
            discount_type: this.product.discount_type ? this.product.discount_type : 'percentage',
            discount_price: this.product.discount_price,
            discount_start_date: this.product.discount_start_date ? new Date(this.product.discount_start_date) : null,
            discount_end_date: this.product.discount_end_date ? new Date(this.product.discount_end_date) : null,
            timeZone: this.getTimeZone(),
            description: this.product.description,
            hierarchies: this.hierarchies,
            type: this.product.vehicle?.type ? this.product.vehicle.type : 'new',
            trim: this.product.vehicle?.trim ? this.product.vehicle.trim : '',
            year: this.product.vehicle?.year ? this.product.vehicle.year : '',
            mpg: this.product.vehicle?.mpg ? this.product.vehicle.mpg : '',
            stock_no: this.product.vehicle?.stock_no ? this.product.vehicle.stock_no : '',
            vin: this.product.vehicle?.vin ? this.product.vehicle.vin : '',
            sellers_notes: this.product.vehicle?.sellers_notes ? this.product.vehicle.sellers_notes : '',
            mileage: this.product.vehicle?.mileage ? this.product.vehicle.mileage : '',
            is_featured: this.product.is_featured ? true : false
        });
    },

    mixins: [Helpers]
}
</script>

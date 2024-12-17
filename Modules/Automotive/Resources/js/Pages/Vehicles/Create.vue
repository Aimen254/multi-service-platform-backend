<template>
    <Head title="Create Vehicle" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Create Vehicle`" :path="`Vehicles - Vehicles`" />
        </template>
        <form class="form d-flex flex-column flex-lg-row" v-if="form" @submit.prevent="submit"
            enctype="multipart/form-data">
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                <div class="d-flex flex-column gap-7 gap-lg-10">
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Basic Information</h3>
                            </div>
                        </div>
                        <div class="card-body py-3">
                            <div class="col-lg-6 fv-row fv-plugins-icon-container">
                                <Label for="name" class="required" value="Image" />
                                <div class="fv-row mb-4">
                                    <div class="image-input image-input-outline image-input-empty"
                                        data-kt-image-input="true">
                                        <div class="image-input-wrapper w-125px h-125px"
                                            :style="{ 'background-image': 'url(' + getImage(url, isSaved, 'product') + ')' }">
                                        </div>
                                        <EditImage :title="'Change Image'" @click="openFileDialog" />
                                        <input id="image" type="file" class="d-none" ref="image" @change="onFileChange" />
                                        <RemoveImageButton v-if="url" :title="'Remove Image'" @click="removeImage()" />
                                    </div>
                                </div>
                                <p class="fs-9 text-muted pt-2">Image must be {{ mediaSizes.width }} x {{ mediaSizes.height
                                }}
                                </p>
                                <error :message="form.errors.image"></error>
                            </div>
                            <div class="row mb-4 fv-plugins-icon-container">
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
                                        :class="{ 'is-invalid border border-danger': form.errors.price }"
                                        v-model="form.price" autofocus autocomplete="price" placeholder="Enter price" />
                                    <error :message="form.errors.price"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="price" class="required" value="Mileage" />
                                    <Input id="price" type="number"
                                        :class="{ 'is-invalid border border-danger': form.errors.mileage }"
                                        v-model="form.mileage" autofocus autocomplete="price" placeholder="Enter mileage" />
                                    <error :message="form.errors.mileage"></error>

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
                                                    @select="getLevelThreeTags($event, 2, index)"
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
                                    <Input id="sku" type="text"
                                        :class="{ 'is-invalid border border-danger': form.errors.mpg }" v-model="form.mpg"
                                        autofocus autocomplete="sku" placeholder="Enter mpg" />
                                    <error :message="form.errors.mpg"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="sku" value="Stock #" />
                                    <Input id="sku" type="text"
                                        :class="{ 'is-invalid border border-danger': form.errors.stock_no }"
                                        v-model="form.stock_no" autofocus autocomplete="sku"
                                        placeholder="Enter stock number" />
                                    <error :message="form.errors.stock_no"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="sku" value="SKU" />
                                    <Input id="sku" type="text"
                                        :class="{ 'is-invalid border border-danger': form.errors.sku }" v-model="form.sku"
                                        autofocus autocomplete="sku" placeholder="Enter sku code" />
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
                            </div>
                            <div class="d-flex justify-content-end">
                                <Button type="submit" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                                    <span class="indicator-label" v-if="!form.processing">Save</span>
                                    <span class="indicator-progress" v-if="form.processing">
                                        <span class="spinner-border spinner-border-sm align-middle"></span>
                                    </span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Error from '@/Components/InputError.vue'
import Helpers from '@/Mixins/Helpers'
import InlineSvg from 'vue-inline-svg'
import Select2 from 'vue3-select2-component'
import RemoveImageButton from '@/Components/RemoveImage.vue'
import EditImage from '@/Components/EditImage.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    props: ['level', 'parentCategoires', 'mediaSizes', 'levelTwoTags'],
    components: {
        Head,
        AuthenticatedLayout,
        Input,
        Label,
        Button,
        Error,
        InlineSvg,
        Select2,
        RemoveImageButton,
        EditImage,
        Breadcrumbs,
        Datepicker
    },

    data() {
        return {
            form: null,
            url: null,
            isSaved: false,
            hierarchies: [
                {
                    level_two_tag: null,
                    level_three_tag: null,
                    level_four_tags: null,
                },
            ],
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
            var vm = this;
            this.form.image = this.$refs.image.files[0];
            this.form.post(route('automotive.dashboard.dealership.vehicles.store', [this.getSelectedModuleValue(), this.getSelectedBusinessValue()]), {
                errorBag: "product",
                preserveScroll: true,
                onError: (e) => {
                    // vm.form.level_two_tag ? vm.getLevelThreeTags(vm.form.level_two_tag, 2) : null;
                    // vm.form.level_three_tag ? vm.getLevelFourTags(vm.form.level_three_tag, 3) : null;
                }
            });
        },

        onFileChange(e) {
            // this.deleteImage = false;
            const file = e.target.files[0];
            this.isSaved = false;
            this.url = URL.createObjectURL(file);
        },

        openFileDialog() {
            document.getElementById('image').click()
        },

        removeImage() {
            this.swal.fire({
                title: "",
                html: "<h1 class='text-lg text-gray-800 mb-1'>Remove Image</h1><p class='text-base'>Are you sure you want remove image?</p>",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonText: "Yes",
                customClass: {
                    confirmButton: 'danger'
                }
            }).then((result) => {
                this.url = null
                this.$notify({
                    group: "toast",
                    type: 'success',
                    text: "Image Removed!"
                }, 3000) // 3s
            })
        },

        weightUnit(event) {
            event.target.value > 0 ? this.form.weight_unit = 'kg' : this.form.weight_unit = '';
        },

        getLevelThreeTags(tag, level, index) {
            this.hierarchies[index].level_three_tag = null
            this.hierarchies[index].level_four_tags = null
            this.meta = null
            let parameters = [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level];
            var vm = this;
            setTimeout(() => {
                $(".select").select2({
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

                //   get selected value
                $(".select").on("select2:select", function (e) {
                    vm.hierarchies[index].level_three_tag = e.params.data.id
                    vm.getLevelFourTags(vm.hierarchies[index].level_three_tag, 3, index)
                });
            }, 0);
        },

        getLevelFourTags(tag, level, index) {
            this.hierarchies[index].level_four_tags = null
            let parameters = [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level];
            var vm = this;
            setTimeout(() => {
                $(".select-level-four").select2({
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

                //   get selected value
                $(".select-level-four").on("select2:select", function (e) {
                    vm.hierarchies[index].level_four_tags = e.params.data.id
                });
            }, 0);
        }
    },

    mounted() {
        this.form = this.$inertia.form({
            name: '',
            type: 'new',
            image: '',
            price: '',
            sku: '',
            trim: '',
            year: '',
            mpg: '',
            stock_no: '',
            vin: '',
            sellers_notes: '',
            mileage: '',
            hierarchies: this.hierarchies,
            is_featured:false
        });
        this.url = null;
        this.isSaved = this.url ? true : false;
        // this.levelTwoTags.length == 1 ? this.getLevelThreeTags(this.form.level_two_tag, 2) : null
    },

    mixins: [Helpers]
}
</script>

<template>
    <Head title="Create Product" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Create Product`" :path="`Products - Products`" />
        </template>
        <form class="form d-flex flex-column flex-lg-row"
            v-if="form" @submit.prevent="submit" enctype="multipart/form-data">
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
                                    <div class="image-input image-input-outline image-input-empty" data-kt-image-input="true">
                                        <div
                                        class="image-input-wrapper w-125px h-125px"
                                        :style="{ 'background-image': 'url(' + getImage(url, isSaved, 'product') + ')' }"
                                        ></div>
                                        <EditImage :title="'Change Image'" @click="openFileDialog" />
                                        <input id="image" type="file" class="d-none" ref="image"
                                            @change="onFileChange" />
                                        <RemoveImageButton v-if="url" :title="'Remove Image'"
                                            @click="removeImage()" />
                                    </div>
                                </div>
                                    <p class="fs-9 text-muted pt-2">Image must be {{mediaSizes.width}} x {{mediaSizes.height}}</p>
                                <error :message="form.errors.image"></error>
                            </div>
                            <div class="row mb-4 fv-plugins-icon-container">
                                <div class="col-lg-12 py-2">
                                    <Label for="name" class="required" value="Name" />
                                    <Input id="name" type="text"
                                        :class="{'is-invalid border border-danger' : form.errors.name}" v-model="form.name" autofocus autocomplete="name" placeholder="Enter name" />
                                    <error :message="form.errors.name"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="price" class="required" value="Price" />
                                    <Input id="price" type="number" min="0" step="0.01"
                                        :class="{'is-invalid border border-danger' : form.errors.price}" v-model="form.price" autofocus autocomplete="price" placeholder="Enter price" />
                                    <error :message="form.errors.price"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="sku" value="SKU" />
                                    <Input id="sku" type="text"
                                        :class="{'is-invalid border border-danger' : form.errors.sku}" v-model="form.sku" autofocus autocomplete="sku" placeholder="Enter sku code" />
                                    <error :message="form.errors.sku"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="stock" class="required" value="Level Two Tags" />
                                    <select2
                                        class="form-control-md text-capitalize form-control-solid"
                                        :class="{
                                            'is-invalid border border-danger':
                                                    form.errors.level_two_tag,
                                            }"
                                        v-model="form.level_two_tag"
                                        :options="levelTwoTags"
                                        :placeholder="'Select Level Two tag'"
                                        @select="getLevelThreeTags($event, 2)"
                                    />
                                    <error :message="form.errors.level_two_tag"></error>
                                </div>
                                <div class="col-lg-6 py-2" v-if="form.level_two_tag">
                                    <Label for="stock" class="required" value="Level Three Tags" />
                                    <select class="select form-control-md text-capitalize form-control-solid"
                                        :class="{
                                                'is-invalid border border-danger':
                                                    form.errors.level_three_tag,
                                            }"
                                        v-model="form.level_three_tag"></select>
                                    <error :message="form.errors.level_three_tag"></error>
                                </div>
                                <div class="col-lg-6 py-2" v-if="form.level_three_tag">
                                    <Label for="stock" class="required" value="Level Four Tags" />
                                    <multiselect
                                        v-model="form.level_four_tags"
                                        :options="levelFourTags"
                                        :loading="isLoading"
                                        :multiple="true"
                                        :close-on-select="false"
                                        :clear-on-select="false"
                                        :preserve-search="true"
                                        placeholder="Select Level Four Tags"
                                        label="text"
                                        track-by="id"
                                        :preselect-first="false"
                                        :internal-search="false"
                                        :limit="5"
                                        :options-limit="300"
                                        :searchable="true"
                                        :show-labels="false"
                                        @search-change="asyncFind"
                                        @Open="asyncFind"
                                    ></multiselect>
                                    <error :message="form.errors.level_four_tags"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="stock" value="Stock" class="required" />
                                    <Input id="stock" type="number" min="-1"
                                        :class="{'is-invalid border border-danger' : form.errors.stock}" v-model="form.stock" autofocus autocomplete="stock" placeholder="Enter stock" />
                                        <error :message="form.errors.stock"></error>
                                        <div class="mt-2">-1 means infinite stock</div>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="weight" value="Weight" />
                                    <Input id="weight" type="number" min="0" step="0.1"
                                        @keyup="weightUnit($event)"
                                        :class="{'is-invalid border border-danger' : form.errors.weight}" v-model="form.weight" autofocus autocomplete="weight" placeholder="Enter weight" />
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
                            </div>
                            <div class="row">
                                <div class="col-lg-6 py-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" 
                                            id="flexCheckDefault" :checked="form.is_featured"
                                            v-model="form.is_featured"
                                        />
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Featured ?
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-6 py-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" 
                                            id="flexCheckDefault" :checked="form.is_deliverable"
                                            v-model="form.is_deliverable"
                                        />
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Deliverable ?
                                        </label>
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
    },

    data() {
        return {
            form: null,
            url: null,
            isSaved: false,
            levelThreeTags: [],
            levelFourTags: []
        }
    },

    methods: {
        submit () {
            this.form.image = this.$refs.image.files[0];
            this.form.post(route('retail.dashboard.business.products.store', [this.getSelectedModuleValue(), this.getSelectedBusinessValue()]), {
                errorBag: "product",
                preserveScroll: true,
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
        
        getLevelThreeTags(tag, level) {
            this.levelFourTags = []
            this.form.level_three_tag = null
            this.meta = null
            let parameters = [this.getSelectedModuleValue(), this.getSelectedBusinessValue(), tag, level];
            var vm = this;
            setTimeout(() => {
                $(".select").select2({
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

                //   get selected value
                $(".select").on("select2:select", function (e) {
                    vm.form.level_three_tag = e.params.data.id
                    vm.levelFourTags = []
                    vm.form.level_four_tags = []
                });
            }, 0);
        },
        
        asyncFind: _.debounce(function (query) {
            // if (query.length < 1) return;
            this.getLevelFourTags(this.form.level_three_tag, 3, query)
        }, 250),
        getLevelFourTags(tag, level , query) {
            // this.levelFourTags = []
            // this.form.level_four_tags = []
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
        }
    },

    mounted() {
        this.form = this.$inertia.form({
            name: '', 
            image: '',
            price: '',
            sku: '',
            weight: '',
            stock: '',
            is_featured: false,
            is_deliverable: false,
            weight_unit: '',
            level_two_tag: this.levelTwoTags.length == 1 ? this.levelTwoTags[0].id : null,
            level_three_tag: null,
            level_four_tags: []
        });
        this.url = null; 
        this.isSaved = this.url ? true : false;
        this.levelTwoTags.length == 1 ? this.getLevelThreeTags(this.form.level_two_tag, 2) : null
    },

    mixins: [Helpers]
}
</script>
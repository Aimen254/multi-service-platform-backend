<template>
    <Head title="Create Task"/>
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :path="`Task - Task`" :title="`Create Task`"/>
        </template>
        <div>
            <form v-if="form" enctype="multipart/form-data" @submit.prevent="submit">
                <!--begin::Layout-->
                <div class="d-flex flex-column flex-xl-row">
                    <div class="flex-lg-row-fluid">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>Basic Information</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4 mt-8">
                                    <Label for="image" value="Image"/>
                                    <div class="fv-row mb-4">
                                        <div class="image-input image-input-outline image-input-empty"
                                             data-kt-image-input="true">
                                            <div
                                                :style="{ 'background-image': 'url(' + getImage(url, isSaved, 'product') + ')' }"
                                                class="image-input-wrapper w-125px h-125px">
                                            </div>
                                            <EditImage :title="'Change Image'" @click="openFileDialog()">
                                            </EditImage>
                                            <input id="news" ref="news" :accept="accept" class="d-none" type="file"
                                                   @change="onFileChange"/>
                                            <RemoveImageButton v-if="url" :title="'Remove Image'"
                                                               @click="news ? removeImage(news.id) : removeImage()"/>
                                        </div>
                                    </div>
                                    <p class="fs-9 text-muted pt-2">Image must be {{ mediaSizes.width }} x {{
                                            mediaSizes.height
                                        }}
                                    </p>
                                    <error :message="form.errors.image"></error>
                                </div>
                                <div class="row mb-4 fv-plugins-icon-container">
                                    <div class="col-lg-6 py-2">
                                        <Label class="required" for="name" value="Title"/>
                                        <Input id="name" v-model="form.name"
                                               :class="{ 'is-invalid border border-danger': form.errors.name }"
                                               autocomplete="name"
                                               autofocus
                                               placeholder="Enter task title" type="text"/>
                                        <error :message="form.errors.name"></error>
                                    </div>
                                    <div class="col-lg-6 py-2">
                                        <Label class="required" for="price" value="Price"/>
                                        <Input id="price" v-model="form.price"
                                               :class="{ 'is-invalid border border-danger': form.errors.price }"
                                               autocomplete="price"
                                               autofocus
                                               min="0" placeholder="Enter Price" step="0.01"
                                               type="number"/>
                                        <error :message="form.errors.price"></error>
                                    </div>

                                    <div class="col-lg-6 py-2">
                                        <Label class="required" for="stock" value="Level Two Tags"/>
                                        <select2 v-model="form.level_two_tag" :class="{
                                            'is-invalid border border-danger':
                                                form.errors.level_two_tag,
                                        }" :options="levelTwoTags" :placeholder="'Select Level Two tag'"
                                                 class="form-control-md text-capitalize form-control-solid"
                                                 @select="getLevelThreeTags($event, 2)"/>
                                        <error :message="form.errors.level_two_tag"></error>
                                    </div>
                                    <div v-if="form.level_two_tag" class="col-lg-6 py-2">
                                        <Label class="required" for="stock" value="Level Three Tags"/>
                                        <select v-model="form.level_three_tag"
                                                :class="{
                                            'is-invalid border border-danger':
                                                form.errors.level_three_tag,
                                        }" class="select form-control-md text-capitalize form-control-solid"></select>
                                        <error :message="form.errors.level_three_tag"></error>
                                    </div>
                                    <div v-if="form.level_three_tag > 0" class="col-lg-6 py-2">
                                        <Label class="required" for="stock" value="Level Four Tags"/>
                                        <select
                                            v-model="form.level_four_tags"
                                            :class="{
                                                'is-invalid border border-danger':
                                                    form.errors.level_four_tags,
                                            }"
                                            class="select-level-four form-control-md text-capitalize form-control-solid select2-hidden-accessible"></select>
                                        <error :message="form.errors.level_four_tags"></error>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <Label for="description" value="Description"/>
                                        <QuillEditor v-model:content="form.description" contentType="html"
                                                     placeholder='Compose task description...'
                                                     theme="snow"/>
                                        <error :message="form.errors.description"></error>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 8%;">
                                    <div class="col-lg-6 py-3">
                                        <div class="form-check">
                                            <input id="CheckDefault" v-model="form.is_featured"
                                                   :checked="form.is_featured"
                                                   class="form-check-input"
                                                   type="checkbox" value=""/>
                                            <label class="form-check-label" for="CheckDefault">
                                                Featured ?
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 py-3">
                                        <div class="form-check">
                                            <input id="flexCheckDefault" v-model="form.is_commentable"
                                                   :checked="form?.is_commentable"
                                                   class="form-check-input"
                                                   type="checkbox" value=""/>
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Commentable
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 8%;">
                                    <div class="text-end">
                                        <Button ref="submitButton" :class="{ 'opacity-25': form.processing }"
                                                :disabled="form.processing" type="submit">
                                            <span v-if="!form.processing" class="indicator-label">
                                                {{ this.news ? 'Update' : 'Save' }} </span>
                                            <span v-if="form.processing" class="indicator-progress">
                                                <span class="spinner-border spinner-border-sm align-middle"></span>
                                            </span>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Layout-->
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import {Head} from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import Error from '@/Components/InputError.vue';
import RemoveImageButton from '@/Components/RemoveImage.vue';
import EditImage from '@/Components/EditImage.vue';
import {QuillEditor} from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Select2 from 'vue3-select2-component';
import Helpers from '@/Mixins/Helpers';

export default {
    props: ['news', 'mediaSizes', 'levelTwoTags'],
    components: {
        Head,
        AuthenticatedLayout,
        Breadcrumbs,
        Input,
        Label,
        Button,
        Error,
        RemoveImageButton,
        EditImage,
        QuillEditor,
        Select2
    },

    data() {
        return {
            form: null,
            url: null,
            isSaved: false,
            imageType: 'news',
            levelThreeTags: [],
            levelFourTags: []
        }
    },

    mounted() {
        this.form = this.$inertia.form({
            id: this.news ? this.news.id : null,
            name: this.news ? this.news.name : '',
            description: this.news ? this.news.description : '',
            image: '',
            is_featured: false,
            is_commentable: true,
            level_two_tag: '',
            level_three_tag: null,
            level_four_tags: null,
            price: null
        });
        this.url = this.news ? this.news.image : null;
        this.isSaved = this.url ? true : false;
        this.levelTwoTags.length == 1 ? this.getLevelThreeTags(this.form.level_two_tag, 2) : null;
    },

    methods: {
        submit() {
            this.form.image = this.$refs.news.files[0];

            this.form.post(route('taskers.dashboard.taskers.store', this.getSelectedModuleValue()), {
                errorBag: 'recipes',
                preserveScroll: true,
                onError: (errors) => {
                    console.log(errors);
                    // this.getLevelThreeTags(this.form.level_two_tag, 2)
                }
            })
        },

        openFileDialog() {
            document.getElementById('news').click()
        },

        onFileChange(e) {
            const file = e.target.files[0];
            this.isSaved = false;
            this.url = URL.createObjectURL(file);
        },

        getLevelThreeTags(tag, level) {
            this.form.level_three_tag = null
            this.levelFourTags = []
            this.meta = null
            let parameters = [this.getSelectedModuleValue(), tag, level];
            var vm = this;
            setTimeout(() => {
                $(".select").select2({
                    placeholder: "Select Level 3",
                    // ajax request
                    ajax: {
                        url: route("taskers.dashboard.taskers.tags", parameters),
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
                    vm.getLevelFourTags(vm.form.level_three_tag, 3)
                });
            }, 0);
        },

        getLevelFourTags(tag, level) {
            this.levelFourTags = []
            this.form.level_four_tags = null
            let parameters = [this.getSelectedModuleValue(), tag, level];
            var vm = this;
            setTimeout(() => {
                $(".select-level-four").select2({
                    placeholder: "Select Level 4",
                    // ajax request
                    ajax: {
                        url: route("taskers.dashboard.taskers.tags", parameters),
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

                //   get selected value
                $(".select-level-four").on("select2:select", function (e) {
                    vm.form.level_four_tags = e.params.data.id
                });
            }, 0);
        }
    },
    mixins: [Helpers]
};
</script>

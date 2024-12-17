<template>
    <Head title="Create Event" />
    <AuthenticatedLayout>

        <form v-if="form" @submit.prevent="submit" enctype="multipart/form-data">
            <!--begin::Layout-->
            <div class="d-flex flex-column flex-xl-row">
                <div class="flex-lg-row-fluid">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-body">
                            <div class="row mb-4 mt-8">
                                <Label for="image" value="Image" />
                                <div class="fv-row mb-4">
                                    <div class="image-input image-input-outline image-input-empty"
                                        data-kt-image-input="true">
                                        <div class="image-input-wrapper w-125px h-125px"
                                            :style="{ 'background-image': 'url(' + getImage(url, isSaved, 'blogs') + ')' }">
                                        </div>
                                        <EditImage :title="'Change Image'" @click="openFileDialog()">
                                        </EditImage>
                                        <input id="blog" type="file" class="d-none" :accept="accept" ref="blog"
                                            @change="onFileChange" />
                                        <RemoveImageButton v-if="url" :title="'Remove Image'"
                                            @click="blog ? removeImage(blog.id) : removeImage()" />
                                    </div>
                                </div>
                                <p class="fs-9 text-muted pt-2">Image must be {{ mediaSizes.width }} x {{
                                    mediaSizes.height }}
                                </p>
                                <error :message="form.errors.image"></error>
                            </div>

                            <div class="row mb-4 fv-plugins-icon-container">
                                <div class="col-lg-6 py-2">
                                    <Label for="name" value="Title" class="required" />
                                    <Input id="name" type="text" v-model="form.name"
                                        :class="{ 'is-invalid border border-danger': form.errors.name }" autofocus
                                        autocomplete="name" placeholder="Enter Event title" />
                                    <error :message="form.errors.name"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="performer" value="Performer" class="required" />
                                    <Input id="performer" type="text" v-model="form.performer"
                                        :class="{ 'is-invalid border border-danger': form.errors.performer }" autofocus
                                        autocomplete="name" placeholder="Enter performer" />
                                    <error :message="form.errors.performer"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <label for="event_date" class="required fw-bolder">Date</label>
                                    <Datepicker v-model="form.event_date" date-picker
                                        :class="{ 'is-invalid border border-danger': form.errors.event_date }"
                                        class="form-control form-control-solid" :min-date="new Date()"></Datepicker>

                                    <error :message="form.errors.event_date"></error>
                                </div>

                                <div class="col-lg-6 py-2">
                                    <Label for="performer" value="Minimum Price" class="required" />
                                    <Input id="performer" type="text" v-model="form.price"
                                        :class="{ 'is-invalid border border-danger': form.errors.price }" autofocus
                                        autocomplete="name" placeholder="Enter minimum price" />
                                    <error :message="form.errors.price"></error>
                                </div>

                                <div class="col-lg-6 py-2">
                                    <Label for="performer" value="Maximum Price" class="required" />
                                    <Input id="performer" type="text" v-model="form.max_price"
                                        :class="{ 'is-invalid border border-danger': form.errors.max_price }" autofocus
                                        autocomplete="name" placeholder="Enter  maximum price" />
                                    <error :message="form.errors.max_price"></error>
                                </div>


                                <div class="col-lg-6 py-2">
                                    <Label for="performer" value="Event location" class="required" />
                                    <Input id="performer" type="text" v-model="form.event_location"
                                        :class="{ 'is-invalid border border-danger': form.errors.event_location }" autofocus
                                        autocomplete="name" placeholder="Enter event location" />
                                    <error :message="form.errors.event_location"></error>
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
                                <div class="col-lg-6 py-2" v-if="form.level_three_tag > 0">
                                    <Label for="stock" class="required" value="Level Four Tags" />
                                    <select class="select-level-four form-control-md text-capitalize form-control-solid select2-hidden-accessible"
                                        :class="{
                                            'is-invalid border border-danger':
                                                form.errors.level_four_tags,
                                        }" v-model="form.level_four_tags"></select>
                                    <error :message="form.errors.level_four_tags"></error>
                                </div>

                                <div class="col-lg-6 py-2">
                                    <Label for="ticket_url" value="Event Ticket URL" class="required" />
                                    <Input id="ticket_url" type="text" v-model="form.ticket_url"
                                        :class="{ 'is-invalid border border-danger': form.errors.ticket_url }" autofocus
                                        autocomplete="name" placeholder="Enter the URL to buy ticket" />
                                    <error :message="form.errors.ticket_url"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="away_team" value="Away Team" />
                                    <Input id="away_team" type="text" v-model="form.away_team"
                                           :class="{ 'is-invalid border border-danger': form.errors.away_team }" autofocus
                                           autocomplete="name" placeholder="Enter away team" />
                                    <error :message="form.errors.away_team"></error>
                                </div>


                            </div>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <Label for="description" value="Description" />
                                    <QuillEditor theme="snow" v-model:content="form.description" contentType="html"
                                        placeholder='Compose blog description...' />
                                    <error :message="form.errors.description"></error>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 8%;">
                                <div class="col-lg-6 py-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="CheckDefault"
                                            :checked="form?.is_featured" v-model="form.is_featured" />
                                        <label class="form-check-label" for="CheckDefault">
                                            Featured ?
                                        </label>
                                    </div>
                                </div>

                                <div class="col-lg-6 py-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"
                                            :checked="form?.is_commentable" v-model="form.is_commentable" />
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Commentable
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-end">
                                    <Button type="submit" :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing" ref="submitButton">
                                        <span class="indicator-label" v-if="!form.processing">
                                            {{ this.blog ? 'Update' : 'Save' }} </span>
                                        <span class="indicator-progress" v-if="form.processing">
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
    </AuthenticatedLayout>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Breadcrumbs from "@/Components/Breadcrumbs.vue";
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import Error from '@/Components/InputError.vue';
import RemoveImageButton from '@/Components/RemoveImage.vue';
import EditImage from '@/Components/EditImage.vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Select2 from 'vue3-select2-component';
import '@vuepic/vue-datepicker/dist/main.css'
import Datepicker from '@vuepic/vue-datepicker';

import Helpers from '@/Mixins/Helpers';

export default {
    props: ['blog', 'mediaSizes', 'levelTwoTags'],
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
        Select2,
        Datepicker
    },

    data() {

        return {
            form: null,
            url: null,
            isSaved: false,
            imageType: 'blog',
            levelThreeTags: [],
            levelFourTags: []
        }
    },

    mounted() {
        this.form = this.$inertia.form({
            name: '',
            description: '',
            image: '',
            is_featured: false,
            is_commentable: false,
            level_two_tag: this.levelTwoTags.length == 1 ? this.levelTwoTags[0].id : null,
            level_three_tag: null,
            level_four_tags: null,
            performer: '',
            max_price: '',
            price: '',
            away_team: '',
            ticket_url: '',
            event_location: '',
            event_date: new Date(),
        });
        this.url = this.events ? this.events.image : null;
        this.isSaved = this.url ? true : false;
        this.levelTwoTags.length == 1 ? this.getLevelThreeTags(this.form.level_two_tag, 2) : null;
    },

    methods: {


        submit() {
            var vm = this;
            this.form.image = this.$refs.blog.files[0];

            this.form.post(route('events.dashboard.events.store', this.getSelectedModuleValue()), {
                errorBag: 'blog',
                preserveScroll: true,
                onError: () => {
                    vm.form.level_two_tag ? vm.getLevelThreeTags(vm.form.level_two_tag, 2) : null;
                    vm.form.level_three_tag ? vm.getLevelFourTags(vm.form.level_three_tag, 3) : null;
                }
            });
        },



        openFileDialog() {
            document.getElementById('blog').click()
        },

        onFileChange(e) {
            const file = e.target.files[0];
            this.isSaved = false;
            this.url = URL.createObjectURL(file);
        },

        getLevelThreeTags(tag, level) {
            this.levelFourTags = []
            this.meta = null
            let parameters = [this.getSelectedModuleValue(), tag, level];
            var vm = this;
            setTimeout(() => {
                $(".select").select2({
                    placeholder: "Select Level 3",
                    // ajax request
                    ajax: {
                        url: route("events.dashboard.events.tags", parameters),
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
                        url: route("events.dashboard.events.tags", parameters),
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

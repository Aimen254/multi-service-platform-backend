<template>

    <Head title="Edit Obituary" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Obituary`" :path="`Obituaries - ${limit(obituaries?.name, 36)}`" />
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <ProductSidebar :obituaries="obituaries" :width="'w-lg-820px'" />
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <form class="form" v-if="form" @submit.prevent="submit">
                    <div class="card card-flush py-4">
                        <div class="card-body py-3">
                            <span class="d-inline-block position-relative ms-2">
                                <span class="d-inline-block mb-2 fs-2 fw-bold">
                                    Basic Details:
                                </span>
                                <span
                                    class="d-inline-block position-absolute h-3px bottom-0 end-0 start-0 bg-secondary translate rounded"></span>
                            </span>
                            <div class="row mt-8">
                                <div class="col-lg-12 py-2">
                                    <Label for="name" class="required" value="Title" />
                                    <Input id="name" type="text"
                                        :class="{ 'is-invalid border border-danger': form.errors.name }"
                                        v-model="form.name" autofocus autocomplete="name"
                                        placeholder="Enter obituaries title" />
                                    <error :message="form.errors.name"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label for="dob" value="Date of Birth" class="required" />
                                    <Input id="dob" type="date" v-model="form.date_of_birth"
                                        :class="{ 'is-invalid border border-danger': form.errors.date_of_birth }"
                                        autofocus autocomplete="name" placeholder="Enter Date of Birth" />
                                    <error :message="form.errors.date_of_birth"></error>
                                </div>

                                <div class="col-lg-6 py-2">
                                    <Label for="dd" value="Date of Death" class="required" />
                                    <Input id="dd" type="date" v-model="form.date_of_death"
                                        :class="{ 'is-invalid border border-danger': form.errors.date_of_death }"
                                        autofocus autocomplete="name" placeholder="Enter Date of Death" />
                                    <error :message="form.errors.date_of_death"></error>
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
                                    <select
                                        class="select-level-four form-control-md text-capitalize form-control-solid select2-hidden-accessible"
                                        :class="{
                'is-invalid border border-danger':
                    form.errors.level_four_tags,
            }" v-model="form.level_four_tags"></select>
                                    <error :message="form.errors.level_four_tags"></error>
                                </div>
                                <div class="col-lg-12 py-2">
                                    <Label for="description" value="Description" />
                                    <QuillEditor theme="snow" v-model:content="form.description" contentType="html"
                                        placeholder='Compose obituaries description...' />
                                    <error :message="form.errors.description"></error>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 12%;">
                                <div class="col-lg-6 py-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value=""
                                            id="flexCheckCommentable" :checked="form?.is_commentable"
                                            v-model="form.is_commentable" />
                                        <label class="form-check-label" for="flexCheckCommentable">
                                            Commentable
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-6 py-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckFeatured"
                                            :checked="form.is_featured" v-model="form.is_featured" />
                                        <label class="form-check-label" for="flexCheckFeatured">
                                            Featured ?
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 py-5 mt-4 d-flex justify-content-end">
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
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import ProductSidebar from './Partials/ProductSideMenu.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import Error from '@/Components/InputError.vue'
import { QuillEditor } from '@vueup/vue-quill';
import Helpers from '@/Mixins/Helpers';
import Select2 from 'vue3-select2-component';

export default {
    props: ['obituaries', 'levelTwoTags', 'productLevelTwoTag', 'mediaSizes'],

    components: {
        Head,
        AuthenticatedLayout,
        Breadcrumbs,
        ProductSidebar,
        Input,
        Label,
        Button,
        Error,
        QuillEditor,
        Select2,
    },

    data() {
        return {
            form: null,
            imageForm: null,
            url: null,
            isSaved: false,
            imageType: 'obituaries',
            levelThreeTags: [],
            levelFourTags: []
        }
    },
    methods: {
        submit() {
            this.form.put(route('obituaries.dashboard.obituaries.update', [this.getSelectedModuleValue(), this.obituaries.uuid]), {
                errorBag: "obituaries",
                preserveScroll: true,
                onError: (errors) => {
                    this.getLevelThreeTags(this.form.level_two_tag, 2)
                }
            });
        },
        getLevelThreeTags(tag, level) {
            this.levelFourTags = []
            this.meta = null
            let parameters = [this.getSelectedModuleValue(), tag, level];
            var vm = this;
            // ajax search
            setTimeout(() => {
                var select2 = $(".select").select2({
                    placeholder: "Select Level 3",

                    // ajax request
                    ajax: {
                        url: route("obituaries.dashboard.obituaries.tags", parameters),
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
                window.axios.get(route('obituaries.dashboard.obituaries.tags', [this.getSelectedModuleValue(), tag, level]), {
                    params: {
                        obituaries: this.obituaries.id
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
            let parameters = [this.getSelectedModuleValue(), tag, level];
            var vm = this;
            setTimeout(() => {
                var levelFourSelect = $(".select-level-four").select2({
                    placeholder: "Select Level 4",
                    // ajax request
                    ajax: {
                        url: route("obituaries.dashboard.obituaries.tags", parameters),
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
                window.axios.get(route('obituaries.dashboard.obituaries.tags', [this.getSelectedModuleValue(), tag, level]), {
                    params: {
                        levelTwoTag: this.form.level_two_tag,
                        obituaries: this.obituaries.id
                    }
                }).then((response) => {
                    vm.form.level_four_tags = response.data.productLevelFourTag ? response.data.productLevelFourTag.id : null
                    var levelFourSelected = response.data.productLevelFourTag ? response.data.productLevelFourTag : null
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
        console.log(this.obituaries.is_featured, " this.obituaries.is_featured")
        this.form = this.$inertia.form({
            id: this.obituaries ? this.obituaries.id : null,
            name: this.obituaries ? this.obituaries.name : '',
            date_of_birth: this.obituaries ? this.obituaries.date_of_birth : '',
            date_of_death: this.obituaries ? this.obituaries.date_of_death : '',
            description: this.obituaries ? this.obituaries.description : '',
            level_two_tag: this.productLevelTwoTag ? this.productLevelTwoTag.id : (this.levelTwoTags.length == 1 ? this.levelTwoTags[0].id : null),
            level_three_tag: null,
            is_featured: this.obituaries.is_featured ? true : false,
            is_commentable: this?.obituaries?.is_commentable ? true : false,
            level_four_tags: null,
        });
        this.form.level_two_tag ? this.getLevelThreeTags(this.form.level_two_tag, 2) : null
    },

    mixins: [Helpers]
}
</script>

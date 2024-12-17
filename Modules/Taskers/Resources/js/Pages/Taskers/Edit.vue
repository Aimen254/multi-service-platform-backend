<template>
    <Head title="Edit Task"/>
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :path="`Task - ${limit(form?.name, 36)}`" :title="`Edit Task`"/>
        </template>
        <div class="d-flex flex-column flex-lg-row">
            <TaskerSidebar :tasker="tasker" :width="'w-lg-820px'"/>
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white">
                <form v-if="form" class="form" @submit.prevent="submit">
                    <div class="card card-flush py-4">
                        <div class="card-body py-3">
                            <div class="row mt-8">
                                <div class="col-lg-12 py-2">
                                    <Label class="required" for="name" value="Title"/>
                                    <Input id="name" v-model="form.name"
                                           :class="{ 'is-invalid border border-danger': form.errors.name }"
                                           autocomplete="name"
                                           autofocus placeholder="Enter task title" type="text"/>
                                    <error :message="form.errors.name"></error>
                                </div>
                                <div class="col-lg-6 py-2">
                                    <Label class="required" for="price" value="Price"/>
                                    <Input id="price" v-model="form.price" :class="{ 'is-invalid border border-danger': form.errors.price }" autocomplete="price"
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
                                    <select v-model="form.level_three_tag" :class="{
                                        'is-invalid border border-danger':
                                            form.errors.level_three_tag,
                                    }" class="select form-control-md text-capitalize form-control-solid"></select>
                                    <error :message="form.errors.level_three_tag"></error>
                                </div>
                                <div v-if="form.level_three_tag > 0" class="col-lg-6 py-2">
                                    <Label class="required" for="stock" value="Level Four Tags"/>
                                    <select v-model="form.level_four_tags"
                                            :class="{
                                            'is-invalid border border-danger':
                                                form.errors.level_four_tags,
                                        }" class="select-level-four form-control-md text-capitalize form-control-solid select2-hidden-accessible"></select>
                                    <error :message="form.errors.level_four_tags"></error>
                                </div>
                                <div class="col-lg-12 py-2">
                                    <Label for="description" value="Description"/>
                                    <QuillEditor v-model:content="form.description" contentType="html" placeholder='Compose task description...'
                                                 theme="snow"/>
                                    <error :message="form.errors.description"></error>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 12%;">
                                <div class="col-lg-6 py-3">
                                    <div class="form-check">
                                        <input id="flexCheckIsFeatured" v-model="form.is_featured" :checked="form?.is_featured"
                                               class="form-check-input"
                                               type="checkbox" value=""/>
                                        <label class="form-check-label" for="flexCheckIsFeatured">
                                            Featured ?
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-6 py-3">
                                    <div class="form-check">
                                        <input id="flexIsCommentable" v-model="form.is_commentable" :checked="form?.is_commentable" class="form-check-input"
                                               type="checkbox" value=""/>
                                        <label class="form-check-label" for="flexIsCommentable">
                                            Commentable
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 py-5 mt-4 d-flex justify-content-end">
                                    <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                            type="submit">
                                        <span v-if="!form.processing" class="indicator-label">Update</span>
                                        <span v-if="form.processing" class="indicator-progress">
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
import {Head} from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '@/Layouts/Authenticated.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import TaskerSidebar from './Partials/TaskerSideMenu.vue';
import Input from '@/Components/Input.vue';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import Error from '@/Components/InputError.vue'
import {QuillEditor} from '@vueup/vue-quill';
import Helpers from '@/Mixins/Helpers';
import Select2 from 'vue3-select2-component';

export default {
    props: ['tasker', 'levelTwoTags', 'productLevelTwoTag', 'mediaSizes'],

    components: {
        Head,
        AuthenticatedLayout,
        Breadcrumbs,
        TaskerSidebar,
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
            imageType: 'tasker',
            levelThreeTags: [],
            levelFourTags: []
        }
    },
    methods: {
        submit() {
            this.form.put(route('taskers.dashboard.taskers.update', [this.getSelectedModuleValue(), this.tasker.uuid]), {
                errorBag: "tasker",
                preserveScroll: true,
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

                // selected value
                window.axios.get(route('taskers.dashboard.taskers.tags', [this.getSelectedModuleValue(), tag, level]), {
                    params: {
                        tasker: this.tasker.id
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
                            text: error?.response?.data?.message ?? error
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
                            params.page = params.page || 1
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
                window.axios.get(route('taskers.dashboard.taskers.tags', [this.getSelectedModuleValue(), tag, level]), {
                    params: {
                        levelTwoTag: this.form.level_two_tag,
                        tasker: this.tasker.id
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
        this.form = this.$inertia.form({
            id: this?.tasker?.id ?? null,
            name: this?.tasker?.name ?? '',
            price: this?.tasker?.price ?? '',
            description: this?.tasker?.description ?? '',
            level_two_tag: this.productLevelTwoTag ? this.productLevelTwoTag.id : (this.levelTwoTags.length == 1 ? this.levelTwoTags[0].id : null),
            level_three_tag: null,
            is_featured: this?.tasker?.is_featured ? true : false,
            is_commentable: this?.tasker?.is_commentable ? true : false,
            level_four_tags: null,
        });
        this.form.level_two_tag ? this.getLevelThreeTags(this.form.level_two_tag, 2) : null
    },

    mixins: [Helpers]
}
</script>

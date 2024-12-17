<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit" enctype="multipart/form-data">
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="name" class="required" value="Category Name" />
                    <Input id="category_name" type="text"
                        :class="{'is-invalid border border-danger' : form.errors.category_name}" v-model="form.category_name" autofocus
                        autocomplete="name" placeholder="Enter category title" />
                    <error :message="form.errors.category_name"></error>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        ref="submitButton">
                        <span class="indicator-label" v-if="!form.processing"> {{form.id ? 'Update' : 'Save'}}</span>
                        <span class="indicator-progress" v-if="form.processing">
                            <span class="spinner-border spinner-border-sm align-middle"></span>
                        </span>
                    </Button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
    import Modal from '@/Components/Modal.vue'
    import {
        useForm
    } from '@inertiajs/inertia-vue3'
    import Input from '@/Components/Input.vue'
    import Label from '@/Components/Label.vue'
    import Button from '@/Components/Button.vue'
    import Error from '@/Components/InputError.vue'
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'

    export default {
        components: {
            Modal,
            Input,
            Label,
            Button,
            Error,
            InlineSvg,
        },
        data() {
            return {
                title: null,
                form: null,
            }
        },
        methods: {
            submit() {
                if(this.form.id) {
                    this.form.processing = true;
                    this.form.put(route('dashboard.categories.update', this.form.id), {
                        errorBag: 'news_categories',
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                        },
                        onError: (response) => {
                            this.form.errors = response;
                        },
                        onFinish: () => {
                            this.form.processing = false;
                        }
                    })
                } else {
                    this.form.post(route('dashboard.categories.store'), {
                        errorBag: 'news_categories',
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                        }
                    }) 
                }

            },        
        },
        mounted() {
            this.emitter.on('news-category-modal', (args) => {
                this.form = useForm({
                    id: args.newsCategory ? args.newsCategory.id : null,
                    category_name: args.newsCategory ? args.newsCategory.category_name : '',
                });
                this.title = args.newsCategory ? 'Edit News Category' : 'Create News Category'
                $('#genericModal').modal('show')
            })
        },
        mixins: [Helpers]
    }
</script>

<style scoped>

</style>
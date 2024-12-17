<template>
    <form v-if="form" @submit.prevent="submit" enctype="multipart/form-data">
        <!--begin::Layout-->
        <div class="d-flex flex-column flex-xl-row">
            <div class="flex-lg-row-fluid">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-body">
                        <div class="row mb-4">
                            <Label for="image" value="Image" />
                            <div class="fv-row mb-4">
                                <div class="image-input image-input-outline image-input-empty" data-kt-image-input="true">
                                    <div
                                    class="image-input-wrapper w-125px h-125px"
                                    :style="{ 'background-image': 'url(' + getImage(url, isSaved, 'news') + ')' }"
                                    ></div>
                                    <EditImage :title="'Change Image'" @click="openFileDialog">
                                    </EditImage>
                                    <input id="news" type="file" class="d-none" :accept="accept" ref="news"
                                        @change="onFileChange" />
                                    <RemoveImageButton v-if="url" :title="'Remove Image'" @click="news ? removeImage(news.id) : removeImage()" />
                                </div>
                            </div>
                            <p class="fs-9 text-muted pt-2">Image must be {{newsmediaSize.width}} x {{newsmediaSize.height}}</p>
                            <error :message="form.errors.image"></error>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <Label for="title" value="Title" class="required"/>
                                <Input id="title" type="text" v-model="form.title" 
                                    :class="{'is-invalid border border-danger' : form.errors.title}" autofocus
                                    autocomplete="title" placeholder="Enter news title" />
                                <error :message="form.errors.title"></error>
                            </div>
                            <div class="col-md-6">
                                <Label for="slug" value="Slug" class="required"/>
                                <Input id="slug" type="text" v-model="form.slug"
                                    :class="{'is-invalid border border-danger' : form.errors.slug}" autofocus
                                    autocomplete="slug" placeholder="Enter news slug" />
                                <error :message="form.errors.slug"></error>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <Label for="category" value="Category" class="required"/>
                                <select2 v-model="form.news_category_id" :options="newsCategories"
                                    :class="{'is-invalid border border-danger' : form.errors.category_id}"
                                    placeholder="Select News Category">
                                </select2>
                                <error :message="form.errors.news_category_id"></error>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <Label for="description" value="Description" />
                                <QuillEditor theme="snow" v-model:content="form.description" contentType="html" placeholder='Compose news description...' />
                                <error :message="form.errors.description"></error>
                            </div>
                        </div>
                        <div class="row news-btn">
                            <div class="text-end">
                                <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                    ref="submitButton">
                                    <span class="indicator-label" v-if="!form.processing">
                                        {{ this.news ? 'Update' : 'Save' }} </span>
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
</template>


<script>
    import Input from '@/Components/Input.vue'
    import Label from '@/Components/Label.vue'
    import Button from '@/Components/Button.vue'
    import Error from '@/Components/InputError.vue'
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'
    import RemoveImageButton from '@/Components/RemoveImage.vue'
    import EditImage from '@/Components/EditImage.vue'
    import { QuillEditor } from '@vueup/vue-quill'
    import '@vueup/vue-quill/dist/vue-quill.snow.css'
    import Select2 from 'vue3-select2-component'

    export default {
        props: ['news', 'newsmediaSize', 'newsCategories'],
        components: {
            Input,
            Label,
            Button,
            Error,
            InlineSvg,
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

            }
        },
        mounted() {
            this.form = this.$inertia.form({
                id: this.news ? this.news.id : null,
                title: this.news ? this.news.title : '',
                slug: this.news ? this.news.slug : '',
                news_category_id: this.news ? this.news.news_category_id : null,
                image: '',
                description: this.news ? this.news.description : ''
            });
            this.url = this.news ? this.news.image : null;
            this.isSaved = this.url ? true : false;
        },
        methods: {
            submit() {
                this.form.image = this.$refs.news.files[0];
                if (this.news && this.news.id !== '') {
                    this.form._method = 'PUT'
                    this.$inertia.post(route('dashboard.news.update', this.news.id), this.form, {
                        errorBag: 'news',
                        preserveScroll: true,
                        onError: (response) => {
                            this.form.errors = response;
                        }
                    })
                } else {
                    this.form.post(route('dashboard.news.store'), {
                        errorBag: 'news',
                        preserveScroll: true
                    })
                }
            },
            onFileChange(e) {
                const file = e.target.files[0];
                this.isSaved = false;
                this.url = URL.createObjectURL(file);
            },
            
            openFileDialog() {
                document.getElementById('news').click()
            },

            removeImage(id = null) {
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
                if (result.value) {
                    if(id) {
                        window.axios.get(route('dashboard.media.remove', [id, this.imageType]))
                        .then((response) => {
                            this.url = null
                            if(id == this.$page.props.auth.user.id) {
                                this.emitter.emit("delete-media")
                            }
                            this.$notify({
                                group: "toast",
                                type: 'success',
                                text: response.data.message
                            }, 3000) // 3s
                        }).catch((error) => {
                            this.$notify({
                                group: "toast",
                                type: 'error',
                                text: error.response.data.message
                            }, 3000) // 3s
                        })
                    } else {
                        this.url = null
                        this.$notify({
                            group: "toast",
                            type: 'success',
                            text: "Avatar Removed!"
                        }, 3000) // 3s
                    }
                }
            })
            },
        },
        mixins: [Helpers]
    }
</script>
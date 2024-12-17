<template>
    <Modal :id=mutipleImageModalId>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">Upload Image</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="text-center fv-row fv-plugins-icon-container">
                    <div class="fv-row mb-4">
                        <div class="image-input image-input-outline image-input-empty" data-kt-image-input="true">
                            <div class="image-input-wrapper w-125px h-125px"
                                :style="{ 'background-image': 'url(' + getImage(url, isSaved, 'variant') + ')' }"
                            ></div>
                            <EditImage :title="'Change image'" @click="openFileDialog">
                            </EditImage>
                            <input id="image" type='file' class="d-none" :accept="accept" ref="image"
                                @change="onFileChange" />
                        </div>
                        <p class="fs-9 text-muted pt-2">Image must be {{productImageSizes.width}} x {{productImageSizes.height}}</p>
                        <error :message="form.errors.image"></error>
                    </div>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        ref="submitButton">
                        <span class="indicator-label" v-if="!form.processing"> {{this.form.id ? 'Update' : 'Save'}}</span>
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
    import Label from '@/Components/Label.vue'
    import { useForm } from '@inertiajs/inertia-vue3'
    import Input from '@/Components/Input.vue'
    import Error from '@/Components/InputError.vue'
    import Button from '@/Components/Button.vue'
    import RemoveImageButton from '@/Components/RemoveImage.vue'
    import EditImage from '@/Components/EditImage.vue'
    import Helpers from '@/Mixins/Helpers'

    export default {
        props: ['product', 'productImageSizes'],

        components: {
            Modal,
            Label,
            Input,
            Error,
            Button,
            RemoveImageButton,
            EditImage
        },

        data() {
            return {
                form: null,
                variant: null,
                url: null,
                variants : null,
                mutipleImageModalId : 'mutipleImageModalId'
            }
        },

        methods: {
            submit () {
                this.form.image = this.$refs.image.files[0];
                this.form.post(route('retail.dashboard.product.variant.imageUpload', [this.getSelectedModuleValue(), this.product.uuid]), {
                    errorBag: 'variant',
                    preserveScroll: true,
                    onSuccess: () => {
                        this.$refs.image.value = null
                        this.emitToParent()
                        $('#mutipleImageModalId').modal('hide')
                    }
                })
            },

            emitToParent () {
                this.$emit('childToParent')
            },

            openFileDialog() {
                document.getElementById('image').click()
            },

            onFileChange(e) {
                const file = e.target.files[0];
                this.isSaved = false;
                this.url = URL.createObjectURL(file);
            },
        },

        mounted () { 
            this.emitter.on('variant-image-modal', (args) => {
                this.form = useForm({
                    image: '',
                    variants: args.variant ? args.variant : ''
                }) 
                this.url = null
                $('#mutipleImageModalId').modal('show')
            })
        },

        mixins: [Helpers]
    }
</script>
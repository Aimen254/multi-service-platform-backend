<template>
    <Modal :id=variantModalId>
        <template #header>
            <h4 v-if="this.form" id="bsModalLabel" class="modal-title">{{ this.form.id ? 'Update' : 'Add' }} Variant</h4>
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
                        <p class="fs-9 text-muted pt-2 required">Image must be {{productImageSizes.width}} x {{productImageSizes.height}}</p>
                        <error :message="form.errors.image"></error>
                    </div>
                </div>

                <div class="row" v-if="!form.id">
                    <div class="col-md-12 fv-row mb-4 fv-plugins-icon-container">
                        <Label for="title" class="required" value="Title" />
                        <Input id="title" type="text"
                            :class="{'is-invalid border border-danger' : form.errors.title}" v-model="form.title" autofocus placeholder="Enter title" />
                        <error :message="form.errors.title"></error>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 fv-row mb-4 fv-plugins-icon-container">
                        <Label for="price" class="required" value="Price" />
                        <Input id="price" type="number" min="0" step="0.01"
                            :class="{'is-invalid border border-danger' : form.errors.price}" v-model="form.price" autofocus
                            placeholder="Enter price" />
                        <error :message="form.errors.price"></error>
                    </div>
                    <div class="col-md-6 fv-row mb-4 fv-plugins-icon-container">
                        <Label for="sku" value="sku" />
                        <Input id="sku" type="text"
                            :class="{'is-invalid border border-danger' : form.errors.sku}" v-model="form.sku" autofocus
                            placeholder="Enter sku" />
                        <error :message="form.errors.sku"></error>
                    </div>
                </div>
                <div class="row" v-if="!form.id">
                    <div class="col-md-6 fv-row mb-4 fv-plugins-icon-container">
                        <Label for="sizes" value="Sizes" />
                        <select v-model="form.size_id" 
                        class="form-select form-select-solid"
                        :class="{'is-invalid border border-danger' : form.errors.size_id}" @change="checkSizeId($event)">
                            <option :value="null" selected disabled>Select a size</option>
                            <option value="">Custom</option>
                            <option v-for="(size, index) in sizes" :value="size.id" :key="index" placeholder="Select Size">
                                {{ size.text }}
                            </option>
                        </select>
                        <error :message="form.errors.size_id"></error>
                    </div>
                    <div class="col-md-6 fv-row mb-4 fv-plugins-icon-container">
                        <Label for="color" value="Colors"/>
                        <select v-model="form.color_id" 
                            class="form-select form-select-solid"
                            :class="{'is-invalid border border-danger' : form.errors.color_id}" @change="checkColorId($event)">
                            <option :value="null" disabled selected>Select a color</option>
                            <option value="">Custom</option>
                            <option v-for="(color,index) in colors" :value="color.id"  :key="index" placeholder="Select Color">
                                {{color.text}}
                            </option>
                        </select>
                        <error :message="form.errors.color_id"></error>
                    </div>
                </div>
                <div class="row" v-if="showCustomSize">
                    <div class="col-md-12 fv-row mb-4 fv-plugins-icon-container">
                        <Label for="custom_size"  value="Custom Size" />
                        <Input id="custom_size" type="text"
                            :class="{'is-invalid border border-danger' : form.errors.custom_size}" v-model="form.custom_size" autofocus
                            min="1" placeholder="Enter Custom Size" />
                        <error :message="form.errors.custom_size"></error>
                    </div>
                </div>
                <div class="row" v-if="showCustomColor">
                    <div class="col-md-12 fv-row mb-4 fv-plugins-icon-container">
                        <Label for="custom_color" value="Custom Color" />
                        <Input id="custom_color" type="text"
                            :class="{'is-invalid border border-danger' : form.errors.custom_color}" v-model="form.custom_color" autofocus
                            placeholder="Enter Custom Color" />
                        <error :message="form.errors.custom_color"></error>
                    </div>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        ref="submitButton">
                        <span class="indicator-label" v-if="!form.processing"> {{this.form.id ? 'Update' : 'Save'}}
                        </span>
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
        props: ['product', 'productImageSizes','sizes','colors'],

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
                codesList: null,
                variant: null,
                url: null,
                variantModalId : 'variantModalId',
                showCustomSize: false,
                showCustomColor: false,


            }
        },

        methods: {
            submit () {

                this.form.image = this.$refs.image.files[0];
                if (this.form.id) {
                    this.form.processing = true
                    this.form._method = 'PUT'
                    this.$inertia.post(route('retail.dashboard.product.variants.update', [this.getSelectedModuleValue(), this.product.uuid, this.form.id]), this.form, {
                        errorBag: 'variant',
                        preserveScroll: true,
                        onSuccess: () => {
                            this.$refs.image.value = null
                            $('#variantModalId').modal('hide')
                        },
                        onError: (response) => {
                            this.form.errors = response;
                        },
                        onFinish: () => this.form.processing = false
                    })
                } else {
                    this.form.post(route('retail.dashboard.product.variants.store', [this.getSelectedModuleValue(), this.product.uuid]), {
                        errorBag: 'variant',
                        preserveScroll: true,
                        onSuccess: () => {
                            this.$refs.image.value = null
                            this.showCustomColor = false
                            this.showCustomSize = false
                            this.form.custom_size = ''
                            this.form.custom_color = ''
                            $('#variantModalId').modal('hide')
                        }
                    })
                }
            },

            openFileDialog() {
                document.getElementById('image').click()
            },

            onFileChange(e) {
                const file = e.target.files[0];
                this.isSaved = false;
                this.url = URL.createObjectURL(file);
            },

            checkColorId($event){
                let colorValue = $event.target
                let text = colorValue.selectedOptions[0].text
                this.showCustomColor = text == 'Custom' ? true : false
            },

            checkSizeId($event){
                let sizeValue = $event.target
                let text = sizeValue.selectedOptions[0].text
                this.showCustomSize = text == 'Custom' ? true : false 
            }
        },

        mounted () {
            this.emitter.on('variant-modal', (args) => {
                this.form = useForm({
                    id: args.variant ? args.variant.id : '',
                    title: args.variant ? args.variant.title : '',
                    price: args.variant ? args.variant.price : '',
                    sku: args.variant ? args.variant.sku : '',
                    color_id: args.variant ? args.variant.color_id : null,
                    size_id: args.variant ? args.variant.size_id : null,
                    custom_size: args.variant ? args.variant.custom_size : '',
                    custom_color: args.variant ? args.variant.custom_color : '',
                    image: args.variant && args.variant.image ? args.variant.image.path : '',
                })
                this.url = args.variant && args.variant.image ? args.variant.image.path : null
                this.isSaved = this.url ? true : false 
                $('#variantModalId').modal('show');
            })
        },

        mixins: [Helpers]
    }
</script>
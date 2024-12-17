<template>
<Head title="Product Images" />
<AuthenticatedLayout>
    <template #breadcrumbs>
        <Breadcrumbs :title="`Product Images`" :path="`Products - ${product?.name}`" />
    </template>
    <div class="d-flex flex-column flex-lg-row">
        <ProductSidebar :product="product" :width="'w-lg-225px'"/>
        <div v-if="checkUserPermissions('add_product_images')" class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10 bg-white" id="dropzone_div">
            <div class="row">
                <div class="col-lg-3 p-4 ps-8 fv-row fv-plugins-icon-container">
                    <Label for="name" value="Main Image" />
                    <div class="fv-row mb-4">
                        <div class="image-input image-input-outline image-input-empty" data-kt-image-input="true">
                            <div class="image-input-wrapper w-125px h-125px"
                                :style="{ 'background-image': 'url(' + getImage(url, isSaved, 'product', product.main_image ? product.main_image.is_external : 0) + ')' }"></div>
                            <EditImage :title="'Change Image'" @click="openFileDialog" />
                            <input id="image" type="file" class="d-none" ref="image" @change="onFileChange" />
                        </div>
                    </div>
                    <p class="fs-9 text-muted pt-2">Image must be {{mediaSizes.width}} x {{mediaSizes.height}}</p>
                    <error v-if="error" :message="error"></error>
                </div>
                <div class="p-4 pe-8 col-lg-9">
                    <Label for="name" value="Other Images" />
                    <div id="product" class="dropzone">
                        <i class="bi bi-file-earmark-arrow-up text-primary fs-3x" id="upload_icon"></i>
                        <h3 class="fs-5 fw-bolder text-gray-900 mt-3" id="text_message">Drop files here or click to
                            upload.</h3>
                    </div>
                    <p class="fs-9 text-muted pt-4">Image must be {{mediaSizes.width}} x {{mediaSizes.height}}</p>
                </div>
            </div>
        </div>
    </div>
</AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import ProductSidebar from '../Partials/ProductSideMenu.vue'
import Select2 from 'vue3-select2-component'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Helpers from '@/Mixins/Helpers'
import Error from '@/Components/InputError.vue'
import Dropzone from "dropzone"
import EditImage from '@/Components/EditImage.vue'
import {useForm} from "@inertiajs/inertia-vue3"

export default {
props: ['product', 'mediaSizes'],

components: {
    Head,
    AuthenticatedLayout,
    Breadcrumbs,
    ProductSidebar,
    Select2,
    Label,
    Button,
    Error,
    EditImage
},

data() {
    return {
        url: null,
        isSaved: false,
        form: null,
        error: null,
    }
},
methods : {
    openFileDialog() {
        document.getElementById("image").click();
    },
    onFileChange(e) {
        const file = e.target.files[0];
        this.isSaved = false;
        this.url = URL.createObjectURL(file);
        this.submit();
    },
    submit() {
        this.form.image = this.$refs.image.files[0]
        this.form._method = 'PUT'
        this.$inertia.post(route("automotive.dashboard.vehicle.media.update", [this.getSelectedModuleValue(), this.product.uuid, this.product.main_image ? this.product.main_image.id : 0]), this.form, {
            errorBag: "admin",
            preserveScroll: true,
            onError: (data) => {
                this.error = data.image
            },
        });
    },
},
mounted(){
    var productMedia = this.product
    this.form = useForm({
        image: "",
    });
    let moduleId = this.getSelectedModuleValue()
    this.url = productMedia.main_image ? productMedia.main_image.path : null
    this.isSaved = this.url ? true : false;
    var submissionUrl = route('automotive.dashboard.vehicle.media.store', [moduleId, this.product.uuid])
    $("#product").dropzone({ 
        url: submissionUrl,
        parallelUploads:6,
        autoProcessQueue: false,
        acceptedFiles: ".png,.jpg,.jpeg",
        dictMaxFilesExceeded : "You can not upload any more files.",
        uploadMultiple:true,
        dictDefaultMessage: 'Upload upto 6 images',
        addRemoveLinks: true,
        success: function (data) {
            let productDropzone = this
            var response = JSON.parse(data.xhr.response)
            let media = response.media
            $('#product').find('div.dz-processing').remove();
            var div = document.getElementById('error')
            if(div) {
                div.remove();
            }
            $('#product').empty();
            let count = 0
            media.forEach((value, index) => {
                let baseUrl = window.location.protocol + "//" + window.location.host
                //Media url will be just url if path contains http.
                if (value && value.path && value.path.length > 0 && value.path.includes('http')) {
                    var mediaUrl = value.path
                } else {
                    var mediaUrl = baseUrl+'/storage/'+value.path
                }
                let fileName = value.path.split("/");
                var file = {
                    id: value.id,
                    name: fileName[1],
                    size:value.size,
                    url: mediaUrl
                }
                productDropzone.emit("addedfile", file);
                productDropzone.emit("thumbnail", file, mediaUrl);
                productDropzone.emit("complete", file);
                count ++
            })
            if(count == 6) {
                productDropzone.removeEventListeners();
                $('#product').attr('style', 'cursor: auto !important');
            }
            window.toast.fire({
                icon: 'success',
                text: 'File added successfully'
            })
        },
        error: function(data) {
            var err = data;
            var validationError = ''
            if(err.xhr) {
                var response = JSON.parse(err.xhr.response)
                validationError = response.message
            }
            var div = document.getElementById('error')
            if(div) {
                div.remove();
            }
            $('#product').after('<div class="text-danger mt-2" id="error">' + validationError + '</div>'); 
            $('#product').find('div.dz-processing').remove();
            document.getElementById('upload_icon').style.display = 'block'
            document.getElementById('text_message').style.display = 'block'
            $('#business').find('.dz-message').attr('style', 'display: block !important');
        },
        removedfile: function (file) {
            if (file.id) {
                window.swal.fire({
                    title: "",
                    html: "<h1 class='text-lg text-gray-800 mb-1'>Delete Record</h1><p class='text-base'>Are you sure want to delete this record?</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "Delete Record",
                    customClass: {
                        confirmButton: 'danger'
                    }
                }).then((result) => {
                    if (result.value) {
                        showWaitDialog()
                        window.axios.delete(route('automotive.dashboard.vehicle.media.destroy', [moduleId, productMedia.uuid, file.id]))
                            .then((response) => {
                                hideWaitDialog()
                                file.previewElement.remove();
                                $('#product').attr('style', 'cursor: pointer !important');
                                var div = document.getElementById('error')
                                if (div) {
                                    div.remove();
                                }
                                if (response.data.count == 1) {
                                    $("#product").find('.dz-message').remove();
                                    $("#product").append('<i class="bi bi-file-earmark-arrow-up text-primary fs-3x" id="upload_icon"></i>')
                                    $("#product").append('<h3 class="fs-5 fw-bolder text-gray-900 mt-3" id="text_message">Drop files here or click to upload.</h3>')
                                    $("#product").append('<div class="dz-default dz-message"><button class="dz-button" type="button">Upload upto 6 images</button></div>');
                                }
                                window.toast.fire({
                                    icon: 'success',
                                    text: 'File deleted successfully'
                                })
                        })
                    }
                })
            }
        },
        init: function() {
            let productDropzone = this
            productDropzone.on("addedfiles", function(files) {
                let count = files.length
              if(count > 6) {
                for(let index = 0; index < count; index++ ) {
                  this.removeAllFiles('added');
                  files[index].previewElement.remove();
                }
                var div = document.getElementById('error')
                if(!div) {
                    $('#product').after('<div class="text-danger mt-2" id="error"> You can upload 6 images !</div>'); 
                }
              } else {
                productDropzone.processQueue();
                document.getElementById('upload_icon').style.display = 'none'
                document.getElementById('text_message').style.display = 'none'
                $('#product').find('.dz-message').attr('style', 'display: none !important');

              }
            });
            if(Object.keys(productMedia.secondary_images).length == 6 ) {
                productDropzone.removeEventListeners();
                $('#product').attr('style', 'cursor: auto !important');
            }
            if(Object.keys(productMedia.secondary_images).length > 0) {
                document.getElementById('upload_icon').style.display = 'none'
                document.getElementById('text_message').style.display = 'none'
                $('#product').find('.dz-message').attr('style', 'display: none !important');
                productMedia.secondary_images.forEach((value, index) => {
                    let baseUrl = window.location.protocol + "//" + window.location.host
                    //Media url will be just url if path contains http.
                    if (value && value.path && value.path.length > 0 && value.path.includes('http')) {
                        var mediaUrl = value.path
                    } else {
                        var mediaUrl = baseUrl+'/storage/'+value.path
                    }
                    let fileName = value.path.split("/");
                    var file = { 
                        id: value.id,
                        name: fileName[1], 
                        size: value.size, 
                        url: mediaUrl
                    }
                    productDropzone.emit("addedfile", file);                                
                    productDropzone.emit("thumbnail", file, mediaUrl);
                    productDropzone.emit("complete", file);
                });

            }
        }
    });
},

mixins: [Helpers]

}
</script>
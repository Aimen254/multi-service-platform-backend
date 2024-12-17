<template>
  <div class="card mb-5 mb-xl-8">
    <div class="card-body">
      <div class="row">
        <div class="col-md-2">
          <div class="">
            <h4>Logo</h4>
            <div class="fv-row mb-4">
              <div
                class="image-input image-input-outline image-input-empty"
                data-kt-image-input="true"
              >
                <div
                class="image-input-wrapper w-125px h-125px"
                :style="{ 'background-image': 'url(' + getImage(business.logo ? business.logo.path : null , true, 'logo') + ')' }"
                ></div>
                <EditImage :title="'Change Logo'" @click="openFileDialog('logo')">
                </EditImage>
                <input id="logo" type="file" class="d-none" :accept="accept" @change="onFileChange($event, 'logo')"/>
                <RemoveImageButton v-if="url" :title="'Remove Logo'"
                    @click="removeImage(business.logo.id, 'logo')"/>
              </div>
              <p class="fs-9 text-muted pt-2">Image must be {{mediaLogoSizes.width}} x {{mediaLogoSizes.height}}</p>
            </div>
            <error v-if="form && form.type == 'logo'" :message="form.errors.image"></error>
          </div>
          
          <div class="pt-12">
            <h4>Thumbnail</h4>
            <div class="fv-row mb-4">
              <div
                class="image-input image-input-outline image-input-empty"
                data-kt-image-input="true"
              >
                <div
                class="image-input-wrapper w-125px h-125px"
                :style="{ 'background-image': 'url(' + getImage(business.thumbnail ? business.thumbnail.path : null , true, 'thumbnail') + ')' }"
                ></div>
                <EditImage :title="'Change Thumbnail'" @click="openFileDialog('thumbnail')">
                </EditImage>
                <input id="thumbnail" type="file" class="d-none" :accept="accept" @change="onFileChange($event, 'thumbnail')" />
                <RemoveImageButton v-if="url" :title="'Remove thumbnail'"
                    @click="removeImage(business.thumbnail.id, 'thumbnail')" />
              </div>
              <p class="fs-9 text-muted pt-2">Image must be {{mediaThumbnailSizes.width}} x {{mediaThumbnailSizes.height}}</p>
            </div>
            <error v-if="form && form.type == 'thumbnail'" :message="form.errors.image"></error>
          </div>
        </div>

        <div class="col-md-10">
          <h4>Banner</h4>
          <div class="fv-row mb-4">
            <div
              class="image-input image-input-outline image-input-empty w-100"
              data-kt-image-input="true"
            >
              <div
                class="image-input-wrapper w-100 h-350px"
                :style="{ 'background-image': 'url(' + getImage(business.banner ? business.banner.path : null , true, 'banner') + ')' }"
                ></div>
              <EditImage :title="'Change Banner'" @click="openFileDialog('banner')">
              </EditImage>
              <input id="banner" type="file" class="d-none"
                  :accept=" accept" @change="onFileChange($event, 'banner')"
              />
              <RemoveImageButton v-if="url" :title="'Remove Banner'"
                @click="removeImage(business.banner.id, 'banner')" />
            </div>
            <p class="fs-9 text-muted pt-2">Image must be {{mediaBannerSizes.width}} x {{mediaBannerSizes.height}}</p>
          </div>
          <error v-if="form && form.type == 'banner'" :message="form.errors.image"></error>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-lg-12">
          <h4>Other Images</h4>
          <div id="business" class="dropzone">
              <i class="bi bi-file-earmark-arrow-up mr-2 text-primary fs-3x" id="upload_icon"></i>
              <h3 class="fs-5 fw-bolder text-gray-900 mt-3" id="text_message">Drop files here or click to
              upload.</h3>
          </div>
          <p class="fs-9 text-muted pt-4">Image must be {{mediaBannerSizes.width}} x {{mediaBannerSizes.height}}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Helpers from "@/Mixins/Helpers";
import { useForm } from "@inertiajs/inertia-vue3";
import RemoveImageButton from '@/Components/RemoveImage.vue'
import EditImage from '@/Components/EditImage.vue'
import Error from "@/Components/InputError.vue";
import Dropzone from "dropzone"
import { forEach } from 'lodash';

export default {
  props: ["business", "mediaLogoSizes", "mediaThumbnailSizes", "mediaBannerSizes", "token"],
  components: {
      RemoveImageButton,
      EditImage,
      Error,
  },
  data() {
    return {
      form: null,
      url: null,
      isSaved: false,
      image: {},
      thumbnail: {},
      logo: {},
    };
  },
  methods: {
    openFileDialog(image_type) {
      if (image_type == "thumbnail") {
        document.getElementById("thumbnail").click();
      } else if (image_type == "logo") {
        document.getElementById("logo").click();
      } else {
        document.getElementById("banner").click();
      }
    },
    onFileChange(e, type) {
      this.swal
        .fire({
          title: "",
          html:
            "<p class='text-base'>Are you sure want to add new " +
            type +
            " image?</p>",
          icon: "warning",
          showCancelButton: true,
          cancelButtonText: "No",
          confirmButtonText: "Yes",
          customClass: {
            confirmButton: "danger",
          },
        })
        .then((result) => {
          if (result.value) {
            showWaitDialog();
            this.form = useForm({
              image: e.target.files[0],
              type: type,
              businessId: this.business.id,
            });
            this.form.post(route("dashboard.media.change", [this.business.id, 'image']), {
              errorBag: "image",
              preserveScroll: true,
              onSuccess: () => {
                hideWaitDialog();
                const file = e.target.files[0];
                this.isSaved = false;
                this.url = URL.createObjectURL(file);
              },
              onError: () => {
                hideWaitDialog();
              },
            });
          }
        });
    },
    setImage(business, type) {
      if (type == "banner") {
        this.url = business.banner ? business.banner.path : "";
        this.isSaved = this.url ? true : false;
        return this.getImage(this.url, this.isSaved, "banner");
      } else if (type == "thumbnail") {
        this.url = business.thumbnail ? business.thumbnail.path : "";
        this.isSaved = this.url ? true : false;
        return this.getImage(this.url, this.isSaved, "thumbnail");
      } else {
        this.url = business.logo ? business.logo.path : "";
        this.isSaved = this.url ? true : false;
        return this.getImage(this.url, this.isSaved, "logo");
      }
    },

    removeImage(id = null, type) {
        this.swal.fire({
            title: "",
            html: "<h1 class='text-lg text-gray-800 mb-1'>Remove "+type+"</h1><p class='text-base'>Are you sure you want remove "+type+"?</p>",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'No',
            confirmButtonText: "Yes",
            customClass: {
                confirmButton: 'danger'
            }
        }).then((result) => {
            if (result.value) {
                showWaitDialog();
                this.$inertia.visit(route('dashboard.media.remove', [id, type, this.business.id, 'inertia']), {
                  errorBag: "image",
                  preserveScroll: true,
                  onSuccess: () => {
                    hideWaitDialog();
                    this.isSaved = false;
                    this.url = '';
                  },
                })
            }
        })
    },
  },

  mounted() {
    var submissionUrl = route('dashboard.business.optional.images', [this.business.id])
    let banner = this.business
    $("#business").dropzone({ 
        headers: {
            'X-CSRF-TOKEN': this.token
        },
        url: submissionUrl,
        parallelUploads:3,
        autoProcessQueue: false,
        clickable:true,
        acceptedFiles:".png,.jpg,.jpeg",
        uploadMultiple:true,
        dictDefaultMessage: 'Upload upto 6 images',
        addRemoveLinks: true,
        success: function (data) {
            let businessDropzone = this
            var response = JSON.parse(data.xhr.response)
            let media = response.media
            $('#business').find('div.dz-processing').remove();
            var div = document.getElementById('error')
            if(div) {
                div.remove();
            }
            $('#business').empty();
            let count = 0
            media.forEach((value, index) => {
                let baseUrl = window.location.protocol + "//" + window.location.host
                let mediaUrl = baseUrl + '/storage/' + value.path
                let fileName = value.path.split("/");
                var file = {
                    id: value.id,
                    name: fileName[1],
                    size:value.size,
                    url: mediaUrl
                }
                businessDropzone.emit("addedfile", file);
                businessDropzone.emit("thumbnail", file, mediaUrl);
                businessDropzone.emit("complete", file);
                count ++
            })
            if(count == 3 ) {
                businessDropzone.removeEventListeners();
                $('#business').attr('style', 'cursor: auto !important');
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
            $('#business').after('<div class="text-danger mt-2" id="error">' + validationError + '</div>'); 
            $('#business').find('div.dz-processing').remove();
            document.getElementById('upload_icon').style.display = 'block'
            document.getElementById('text_message').style.display = 'block'
            $('#business').find('.dz-message').attr('style', 'display: block !important');
        },
        removedfile: function (file) {
            let businessDropzone = this
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
                        window.axios.post(route('dashboard.business.optional.images.remove', [banner.uuid, file.id]))
                            .then((response) => {
                                hideWaitDialog()
                                file.previewElement.remove();
                                var div = document.getElementById('error')
                                if (div) {
                                    div.remove();
                                }
                                $('#business').attr('style', 'cursor: pointer !important');
                                businessDropzone.setupEventListeners();
                                if (response.data.count == 1) {
                                    $('#business').find('.dz-message').remove();
                                    $("#business").append('<i class="bi bi-file-earmark-arrow-up text-primary fs-3x" id="upload_icon"></i>')
                                    $("#business").append('<h3 class="fs-5 fw-bolder text-gray-900 mt-3" id="text_message">Drop files here or click to upload.</h3>')
                                    $('#business').append('<div class="dz-default dz-message"><button class="dz-button" type="button">Upload upto 3 images</button></div>');
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
            let businessDropzone = this
            businessDropzone.on("addedfiles", function(files) {
              let count = files.length
              if(count > 3) {
                for(let index = 0; index < count; index++ ) {
                  this.removeAllFiles('added');
                  files[index].previewElement.remove();
                }
                var div = document.getElementById('error')
                if(!div) {
                    $('#business').after('<div class="text-danger mt-2" id="error"> You can upload 3 images !</div>'); 
                }
              } else {
                businessDropzone.processQueue();
                document.getElementById('upload_icon').style.display = 'none'
                document.getElementById('text_message').style.display = 'none'
                $('#business').find('.dz-message').attr('style', 'display: none !important');

              }
            });
            if(Object.keys(banner.secondary_images).length == 3 ) {
                businessDropzone.removeEventListeners();
                $('#business').attr('style', 'cursor: auto !important');
            }
            if(Object.keys(banner.secondary_images).length > 0) {
                document.getElementById('upload_icon').style.display = 'none'
                document.getElementById('text_message').style.display = 'none'
                $('#business').find('.dz-message').attr('style', 'display: none !important');
                banner.secondary_images.forEach((value, index) => {
                    let baseUrl = window.location.protocol + "//" + window.location.host
                    let mediaUrl = baseUrl+'/storage/'+value.path
                    let fileName = value.path.split("/");
                    var file = { 
                        id: value.id,
                        name: fileName[1], 
                        size: value.size, 
                        url: mediaUrl
                    }
                    businessDropzone.emit("addedfile", file);                                
                    businessDropzone.emit("thumbnail", file, mediaUrl);
                    businessDropzone.emit("complete", file);
                });

            }
        }
    });
  },

  mixins: [Helpers],
};
</script>
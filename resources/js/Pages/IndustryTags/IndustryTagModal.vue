<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="text-center fv-row fv-plugins-icon-container">
                    <div class="fv-row mb-4">
                        <div
                            class="image-input image-input-outline image-input-empty"
                            data-kt-image-input="true"
                        >
                            <div
                                class="image-input-wrapper w-125px h-125px"
                                :style="{
                                    'background-image':
                                        'url(' +
                                        getImage(url, isSaved, 'logo') +
                                        ')',
                                }"
                            ></div>
                            <EditImage
                                :title="'Change Icon'"
                                @click="openFileDialog"
                            >
                                <input
                                    type="hidden"
                                    name="avatar_remove"
                                    value="1"
                                />
                            </EditImage>
                            <input
                                id="icon"
                                type="file"
                                class="d-none"
                                :accept="accept"
                                ref="icon"
                                @change="onFileChange"
                            />
                        </div>
                        <p class="fs-9 text-muted pt-2">
                            Icon must be {{ iconSizes.width }} x
                            {{ iconSizes.height }}
                        </p>
                        <error :message="form.errors.icon"></error>
                    </div>
                </div>
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="name" class="required" value="Name" />
                        <Input
                            id="name"
                            type="text"
                            :class="{
                                'is-invalid border border-danger':
                                form.errors.name,
                            }"
                            :value="form.name"
                            v-model="form.name"
                            placeholder="Enter name"
                        />
                        <error :message="form.errors.name"></error>
                    </div>
                </div>
                <div class="text-end">
                    <Button
                        type="submit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        <span class="indicator-label" v-if="!form.processing">
                            {{ form.id ? "Update" : "Save" }}</span
                        >
                        <span class="indicator-progress" v-if="form.processing">
                            <span
                                class="spinner-border spinner-border-sm align-middle"
                            ></span>
                        </span>
                    </Button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script>
import { useForm } from "@inertiajs/inertia-vue3";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import Error from "@/Components/InputError.vue";
import Button from "@/Components/Button.vue";
import Select2 from "vue3-select2-component";
import VueTagsInput from "@sipec/vue3-tags-input";
import Helpers from "@/Mixins/Helpers";
import RemoveImageButton from "@/Components/RemoveImage.vue";
import EditImage from "@/Components/EditImage.vue";

export default {
    components: {
        Modal,
        Label,
        Input,
        Error,
        Button,
        Select2,
        VueTagsInput,
        RemoveImageButton,
        EditImage,
    },

    data() {
        return {
            title: null,
            maxWidth: "2xl",
            iconSizes: null,
            form: null,
            url: null,
            isSaved: false,
            tags: '',
            tagsArray: [],
            showDropDown: false
        };
    },

    methods: {
        submit() {
            this.form.tags = this.tagsArray;
            if (!this.form.id) {
                this.form.post(
                    route("dashboard.tag.store"),
                    {
                        errorBag: "groups",
                        preserveScroll: true,
                        onSuccess: () => {
                            this.$refs.icon.value = null;
                            this.emitter.emit("industry-tag-modal", {
                                iconSizes: this.iconSizes,
                            });
                            this.$notify({
                                group: "toast",
                                type: "success",
                                text: 'Tag Added Successfully.',
                            },3000); // 3s
                        },
                        onError: errors => {
                            this.form.errors = errors
                            this.form.processing = false;
                        },
                    }
                );
            } else {
                this.form._method = "PUT";
                this.form.processing = true;
                this.$inertia.post(
                    route("dashboard.tag.update", [
                        this.form.id,
                    ]),
                    this.form,
                    {
                        errorBag: "groups",
                        preserveScroll: true,
                        onSuccess: () => {
                            this.$refs.icon.value = null;
                            $("#genericModal").modal("hide");
                        },
                        onError: errors => {
                            this.form.errors = errors
                        },
                        onFinish: () => {
                            this.form.processing = false;
                        },
                        onError: errors => {
                            this.form.errors = errors
                            this.form.processing = false;
                        },
                    }
                );
            }
        },

        onFileChange(e) {
            const file = e.target.files[0];
            this.isSaved = false;
            this.url = URL.createObjectURL(file);
            this.form.icon = this.$refs.icon.files[0];
            this.$refs.icon.value = null
        },
        openFileDialog() {
            document.getElementById("icon").click();
        },
        updateTags(event) {
            this.tagsArray = event
        },
        focus(){
            this.showDropDown = this.showDropDown ? false : true
        }
    },

    mounted() {
        this.emitter.on("industry-tag-modal", (args) => {
            this.form = useForm({
                id: args.tag ? args.tag.id : null,
                name: args.tag ? args.tag.name : "",
                icon: "",
                tags: args.tag ? args.tag.tags : '',
                type: "product"
            });
            this.iconSizes = args.iconSizes;
            this.url = args.tag ? args.tag.icon : null;
            this.isSaved = this.url ? true : false;
            this.title = args.tag?.id
                ? "Edit Tag"
                : "Create Tag";
            $("#genericModal").modal("show");
        });
    },
    mixins: [Helpers],
};
</script>
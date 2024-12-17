<template>
    <Modal :modelType="modelType">
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">
                {{ modelType == 'mapper_model' ? 'Map Tag' : 'Clone Tag'}}
                <span class="text-muted fs-6 fw-normal" v-if="tag">({{ tag.name }})</span>
            </h4>
        </template>
        <template #content v-if="modelType == 'mapper_model'">
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div v-for="(tag, index) in form.tags" :key="index" class="mb-5">
                    <div class="fv-row mb-4 fv-plugins-icon-container">
                        <div class="row">
                            <div class="col-6"><Label class="required" for="internal categories" value="Tag Type" />
                            </div>
                            <div class="col-6 text-end">
                                <div class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 mb-3"
                                    data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                    data-bs-placement="bottom" data-bs-original-title="add more" @click="addTag(index)"><span
                                        class="svg-icon svg-icon-3"><svg fill="none" viewBox="0 0 24 24" height="24"
                                            width="24" xmlns="http://www.w3.org/2000/svg">
                                            <path xmlns="http://www.w3.org/2000/svg" opacity="0.3"
                                                d="M11 13H7C6.4 13 6 12.6 6 12C6 11.4 6.4 11 7 11H11V13ZM17 11H13V13H17C17.6 13 18 12.6 18 12C18 11.4 17.6 11 17 11Z"
                                                fill="black"></path>
                                            <path xmlns="http://www.w3.org/2000/svg"
                                                d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM17 11H13V7C13 6.4 12.6 6 12 6C11.4 6 11 6.4 11 7V11H7C6.4 11 6 11.4 6 12C6 12.6 6.4 13 7 13H11V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V13H17C17.6 13 18 12.6 18 12C18 11.4 17.6 11 17 11Z"
                                                fill="black">
                                            </path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 mb-3"
                                    data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click"
                                    data-bs-placement="bottom" data-bs-original-title="remove"
                                    v-if="form.tags.length > 1 && index > 0" @click="removeTag(index)"><i
                                        class="fa fa-trash-alt text-danger"></i>
                                </div>
                            </div>
                        </div>
                        <select v-model="tag.tag_type" class="form-select text-capitalize form-select-solid"
                            @change="defaultAttributeType(); filterStandardTags(index)">
                            <option class="text-capitalize" value="product">
                                Product
                            </option>
                            <option class="text-capitalize" value="brand">
                                Brand
                            </option>
                            <option class="text-capitalize" value="attribute">
                                Attribute
                            </option>
                        </select>
                    </div>
                    <div v-if="
                        tag.tag_type == 'attribute' &&
                        attributes &&
                        attributes.length > 0
                    " class="fv-row mb-4 fv-plugins-icon-container">
                        <Label class="required" for="internal categories" value="Attribute Type" />
                        <select @change="filterStandardTags(index)" v-model="tag.attribute_id"
                            class="form-select text-capitalize form-select-solid">
                            <option class="text-capitalize" v-for="attribute in attributes" :key="attribute.id"
                                :value="attribute.id">
                                {{ attribute.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="standardTags[index] && standardTags[index].length > 0"
                        class="fv-row mb-4 text-capitalize fv-plugins-icon-container">
                        <Label for="internal categories" :value="tag.tag_type + ' tag'" />
                        <select2 class="form-control-md text-capitalize form-control-solid" v-model="tag.standard_tag"
                            :options="standardTags[index]" :settings="{ dropdownParent: '#genericModal' }"
                            :placeholder="'Select ' + tag.tag_type + ' tag'" @select="filterStandardTagsList()" />
                    </div>
                    <div class="separator separator-dashed mt-7"></div>
                </div>
                <div class="row">
                    <div class="col-6">
                    </div>
                    <div class="col-6 text-end">
                        <Button type="submit" :class="{ 'opacity-25': form.processing || message }"
                            :disabled="form.processing || message" ref="submitButton">
                            <span class="indicator-label" v-if="!form.processing">
                                {{ form.id ? "Update" : "Save" }}</span>
                            <span class="indicator-progress" v-if="form.processing">
                                <span class="spinner-border spinner-border-sm align-middle"></span>
                            </span>
                        </Button>
                    </div>
                </div>
            </form>
        </template>
        <template #content v-else>
            <form class="mb-4" v-if="form" @submit.prevent="cloneTag">
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label class="required" for="internal categories" value="Tag Type" />
                    <select v-model="form.standardTagType" class="form-select text-capitalize form-select-solid">
                        <option class="text-capitalize" value="product">
                            Product
                        </option>
                        <option class="text-capitalize" value="brand">
                            Brand
                        </option>
                        <option class="text-capitalize" value="attribute">
                            Attribute
                        </option>
                    </select>
                </div>
                <div v-if="form?.standardTagType == 'attribute' && attributes && attributes.length > 0
                " class="fv-row mb-4 fv-plugins-icon-container">
                    <Label class="required" for="internal categories" value="Attribute Type" />
                    <select v-model="form.attributeId" class="form-select text-capitalize form-select-solid">
                        <option class="text-capitalize" v-for="attribute in attributes" :key="attribute.id"
                            :value="attribute.id">
                            {{ attribute.name }}
                        </option>
                    </select>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing}" :disabled="form.processing">
                        <span class="indicator-label" v-if="!form.processing">
                            {{ form.id ? "Update" : "Save" }}</span>
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
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Label from "@/Components/Label.vue";
import Button from "@/Components/Button.vue";
import { Link } from "@inertiajs/inertia-vue3";
import Error from "@/Components/InputError.vue";
import Helpers from "@/Mixins/Helpers";
import { isNull, values } from 'lodash';

export default {
    components: {
        Modal,
        Label,
        Button,
        Error,
        Link,
    },

    props: [],

    data() {
        return {
            form: null,
            tag: null,
            message: null,
            attributes: null,
            brands: [],
            standardTagsList: null,
            standardTags: [],
            processing: false,
            modelType: null
        };
    },

    methods: {
        submit() {
            this.form.tags.forEach((tag, index) => {
                tag.attribute_id = tag.tag_type == 'attribute' ? tag.attribute_id : null
            })
            this.form.put(
                route("dashboard.tag-mappers.update", [
                    this.form.tags[0].id,
                ]),
                {
                    errorBag: "tags-mapper",
                    preserveScroll: true,
                    onSuccess: () => {
                        $("#genericModal").modal("hide");
                    },
                }
            );
        },

        cloneTag() {
            this.processing = true
            this.form.post(route('dashboard.tag-mappers.clone-tag', this.tag.id), {
                preserveScroll: true,
                errorBag: "tags-cloner",
                onSuccess: () => {
                    this.processing = false
                    $("#genericModal").modal("hide")
                },
                onError: errors => { console.log(errors); }
            })
        },

        closeModal() {
            $("#genericModal").modal("hide");
        },

        filterStandardTags(ind = null) {
            this.standardTags[ind] = this.standardTagsList.filter((value) => {
                if (value.type === this.form.tags[ind].tag_type) {
                    if ( value.attribute.length > 0 && this.form.tags[ind].tag_type == 'attribute') {
                        return value.attribute.some(attr => attr.id == this.form.tags[ind].attribute_id);
                    } else if (this.form.tags[ind].tag_type != 'attribute') {
                        return true
                    }
                }
                return false; // Return false if no matching conditions are met
            });

            
        },

        defaultAttributeType() {
            this.form.tags.forEach((tag, index) => {
                tag.attribute_id = tag.tag_type == 'attribute' ? 1 : null
            })
        },

        addTag(index) {
            this.form.tags.push({
                id: this.form.tags[0].id,
                standard_tag: null,
                tag_type: this.form.tags[0].tag_type ? this.form.tags[0].tag_type : "product",
                attribute_id: this.form.tags[0].tag_type == "attribute" && this.form.tags[0].attribute_id ? this.form.tags[0].attribute_id : null,
            })
            this.filterStandardTags(index + 1)
        },

        removeTag(index) {
            this.form.tags.splice(index, 1)
            this.filterStandardTagsList()
        },

        filterStandardTagsList() {
            window.axios.post(route('dashboard.filterStandardTagsList'), {
                'selectedTags': this.form.tags
            })
                .then((response) => {
                    this.standardTagsList = response.data.data
                })
                .catch((error) => {
                    console.log(error)
                })
        }
    },

    mounted() {
        this.emitter.on("tag-mapper-modal", (args) => {
            this.tag = args.tag;
            this.attributes = args.attributes;
            this.modelType = args.modelType;
            if (this.modelType == 'mapper_model') {
                this.form = useForm({
                    tags: [
                        {
                            id: args.tag.id,
                            standard_tag: null,
                            tag_type: args.tag.type ? args.tag.type : "product",
                            attribute_id: args.tag.type == "attribute" && args.tag.attribute_id ? args.tag.attribute_id : null,
                        }
                    ]
                });
                this.standardTagsList = args.standardTags ? args.standardTags : null
                this.filterStandardTags(0)
            } else {
                this.form = useForm({
                    attributeId: null,
                    standardTagType: 'product',
                });
            }
            $("#genericModal").modal("show")
        });
    },
    mixins: [Helpers],
};
</script>
<template>
    <Modal :id="modalId">
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">
                Clone Tag
                <span class="text-muted fs-6 fw-normal" v-if="tag">({{ tag.name }})</span>
            </h4>
        </template>
        <template #content>
            <div class="fv-row mb-4 fv-plugins-icon-container">
                <Label class="required" for="internal categories" value="Tag Type" />
                <select v-model="standardTagType" class="form-select text-capitalize form-select-solid">
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
            <div v-if="standardTagType == 'attribute' && attributes && attributes.length > 0
            " class="fv-row mb-4 fv-plugins-icon-container">
                <Label class="required" for="internal categories" value="Attribute Type" />
                <select v-model="attributeId" class="form-select text-capitalize form-select-solid">
                    <option class="text-capitalize" v-for="attribute in attributes" :key="attribute.id"
                        :value="attribute.id">
                        {{ attribute.name }}
                    </option>
                </select>
            </div>
            <div class="text-end">
                <Button @click="submit()" :class="{'opacity-25': processing}"
                    :disabled="processing" ref="submitButton" >
                    <span class="indicator-label" v-if="!processing">Create</span>
                    <span class="indicator-progress" v-if="processing">
                        <span class="spinner-border spinner-border-sm align-middle"></span>
                    </span>
                </Button>
            </div>
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
import { isNull, truncate, values } from 'lodash';

export default {
    components: {
        Modal,
        Label,
        Button,
        Error,
        Link,
    },

    data() {
        return {
            form: null,
            tag: null,
            standardTagType: null,
            attributeId: null,
            modalId: 'cloneModel',
            processing: false,
            attributes: null
        };
    },

    methods: {
        submit() {
            this.processing = true
            this.$inertia.visit(route('dashboard.tag-mappers.clone-tag', this.tag.id), {
                data: {
                    'type': this.standardTagType,
                    'attribute': this.attributeId,
                },
                preserveScroll: true,
                onSuccess: () => {
                    this.processing = false
                    $("#cloneModel").modal("hide")
                }, 
                onError: errors => {}             
            })
        },
    },

    mounted() {
        this.emitter.on("tag-cloning-modal", (args) => {
            this.tag = args.tag
            this.attributes = args.attributes
            $("#cloneModel").modal("show")
        });
    },
    mixins: [Helpers],
};
</script>
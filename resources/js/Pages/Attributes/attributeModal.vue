
<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit" >
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="name" class="required" value="Name" />
                        <Input id="name" type="text"
                            :class="{'is-invalid border border-danger' : form.errors.name}" :value="form.name" v-model="form.name" placeholder="Enter name" />
                        <error :message="form.errors.name"></error>
                    </div>
                </div>
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="name" class="required" value="Module Tag" />
                        <multiselect :class="{
                                'is-invalid border border-danger':
                                    form.errors.module,
                            }"
                            v-model="form.module"
                            :options="modules"
                            :multiple="true"
                            placeholder="Select Module Tags"
                            track-by="id"
                            label="text">
                        </multiselect>
                        <error :message="form.errors.module"></error>
                    </div>
                </div>
                <div class="row fv-row mb-4 fv-plugins-icon-container pt-3">
                    <div class="col-md-12">
                        <label class="form-check form-check-sm form-check-custom form-check-solid me-9">
                            <input class="form-check-input" type="checkbox" v-model="form.manual_position" :checked="form.manual_position" :value="form.manual_position">
                            <span class="form-check-label fw-bold">
                                Manual Positioning
                            </span>
                        </label>
                    </div>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        >
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
import { useForm } from '@inertiajs/inertia-vue3'
import Modal from '@/Components/Modal.vue'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import Error from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'
// import Select2 from 'vue3-select2-component'
import VueTagsInput  from '@sipec/vue3-tags-input';
import Helpers from "@/Mixins/Helpers";


export default {
    components: {
        Modal,
        Label,
        Input,
        Error,
        Button,
        VueTagsInput
    },

    data() {
        return {
            title:null,
            maxWidth: '2xl',
            form: null,
            key: '',
            modules: null
        }
    },

    methods: {
        submit() {
            if (!this.form.id){
                this.form.post(route('dashboard.attributes.store'), {
                errorBag: 'groups',
                preserveScroll: true,
                onSuccess: () => {
                    $('#genericModal').modal('hide')
                }
            })
            }
            else{
                this.form.put(route('dashboard.attributes.update', [this.form.id]), {
                    errorBag: 'groups',
                    preserveScroll: true,
                    onSuccess: () => {
                        $('#genericModal').modal('hide')
                    }
                })
            }
                
        }
    },
    mounted() {
        this.emitter.on('attribute-modal', (args) => {  
            this.form = useForm({
                id: args.attribute ? args.attribute.id : null,
                name: args.attribute ? args.attribute.name : '',
                module: args.attribute ? args.attribute.module_tags : [],
                manual_position: args.attribute ? args.attribute.manual_position ? true : false : null,
            })
            this.title = args.attribute.id ? 'Edit Attributes' : 'Create Attributes';
            this.modules = args.moduleTags
            $('#genericModal').modal('show')
        })
    },

    mixins: [Helpers],
}
</script>
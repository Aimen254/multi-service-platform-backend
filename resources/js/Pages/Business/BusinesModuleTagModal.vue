
<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit" >
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
            this.form.post(route('dashboard.assign-module-tags'), {
                errorBag: 'groups',
                preserveScroll: true,
                onSuccess: () => {
                    $('#genericModal').modal('hide')
                }
            })
                
        }
    },
    mounted() {
        this.emitter.on('module-tag-modal', (args) => {
            this.form = useForm({
                id:args.business.id,
                module: args.business.standard_tags.length > 0 ? args.business.standard_tags : []
            })
            this.title = 'Assign Module Tags';
            this.modules = args.modules
            $('#genericModal').modal('show')
        })
    },

    mixins: [Helpers],
}
</script>
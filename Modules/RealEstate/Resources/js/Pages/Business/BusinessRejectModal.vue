<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="reason" class="required" value="Reason to reject" />
                        <textarea v-model="form.reason" class="form-control form-control-lg form-control-solid"
                            placeholder="Enter reason for rejecting the broker"></textarea>
                        <error :message="form.errors.reason"></error>
                    </div>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        <span class="indicator-label" v-if="!form.processing"> {{ 'Submit' }}</span>
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
import { QuillEditor } from '@vueup/vue-quill';
// import Select2 from 'vue3-select2-component'
import Helpers from "@/Mixins/Helpers";


export default {
    components: {
        Modal,
        Label,
        Input,
        Error,
        Button,
        QuillEditor
    },

    data() {
        return {
            title: null,
            maxWidth: '2xl',
            form: null,
            key: '',
            modules: null,
            reason: null
        }
    },

    methods: {
        submit() {
            this.form.post(route('real-estate.dashboard.broker.reject', [this.getSelectedModuleValue()]), {
                errorBag: 'groups',
                preserveScroll: true,
                onSuccess: () => {
                    $('#genericModal').modal('hide')
                }
            })

        }
    },
    mounted() {
        this.emitter.on('module-reject-modal', (args) => {
            this.form = useForm({
                id: args.business.id,
                reason: ''
            })
            this.title = 'Reason for rejection';
            $('#genericModal').modal('show')
        })
    },

    mixins: [Helpers],
}
</script>
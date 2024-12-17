<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit" enctype="multipart/form-data">
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="email" class="required" value="Select Language" />
                     <select2
                            class="form-control-md text-capitalize form-control-solid"
                            :class="{'is-invalid border border-danger' : form.errors.name}"
                            v-model="form.name"
                            :options="languages"
                            :settings="{ dropdownParent: '#genericModal' }"
                            placeholder="Select Language"
                        />
                    <error :message="form.errors.name"></error>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        ref="submitButton">
                        <span class="indicator-label" v-if="!form.processing"> {{'Save'}}</span>
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
    import {
        useForm
    } from '@inertiajs/inertia-vue3'
    import Input from '@/Components/Input.vue'
    import Label from '@/Components/Label.vue'
    import Button from '@/Components/Button.vue'
    import Error from '@/Components/InputError.vue'
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'
    import Select2 from 'vue3-select2-component';
    import RemoveImageButton from '@/Components/RemoveImage.vue'
    import EditImage from '@/Components/EditImage.vue'

    export default {
        components: {
            Modal,
            Input,
            Label,
            Button,
            Error,
            InlineSvg,
            Select2,
            RemoveImageButton,
            EditImage
        },
        data() {
            return {
                title: null,
                form: null,
                url: null,
                isOpened: false,
                isSaved: false,
                languages: ''
            }
        },
        methods: {
            submit() {
                this.form.post(route('dashboard.settings.languages.store'), {
                    errorBag: 'languages',
                    preserveScroll: true,
                    onSuccess: () => $('#genericModal').modal('hide')
                })

            },
        },
        mounted() {
            this.emitter.on('language-modal', (args) => {
                this.form = useForm({
                    name: ''
                })
                this.show = true;
                this.languages = args.languages;
                this.title = 'Add Language'
                $('#genericModal').modal('show');
            })
        },
        mixins: [Helpers]
    }
</script>

<style scoped>

</style>
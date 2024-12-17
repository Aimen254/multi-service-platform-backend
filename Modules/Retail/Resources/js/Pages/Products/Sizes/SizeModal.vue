<template>
    <Modal>
        <template #header>
            <h4 v-if="form" id="bsModalLabel" class="modal-title">
                {{ form.id ? 'Update' : 'Add' }} Size
            </h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="title" class="required" value="Title" />
                    <Input id="title" type="text"
                        :class="{'is-invalid border border-danger' : form.errors.title}" 
                        v-model="form.title" placeholder="Enter title" />
                    <error :message="form.errors.title"></error>
                </div>
                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        ref="submitButton">
                        <span class="indicator-label" v-if="!form.processing">
                            {{ form.id ? 'Update' : 'Save' }}
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
    import Helpers from '@/Mixins/Helpers'

    export default {
        props: ['business'],

        components: {
            Modal,
            Label,
            Input,
            Error,
            Button
        },

        data() {
            return {
                form: null
            }
        },

        methods: {
            submit() {
                if (this.form.id) {
                    this.form.put(route('retail.dashboard.business.sizes.update', [this.getSelectedModuleValue(), this.business.uuid, this.form.id]), {
                        errorBag: 'size',
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                        }
                    })
                } else {
                    this.form.post(route('retail.dashboard.business.sizes.store', [this.getSelectedModuleValue(), this.business.uuid]), {
                        errorBag: 'size',
                        preserveScroll: true,
                        onSuccess: () => {
                            $('#genericModal').modal('hide')
                        }
                    })
                }
            }
        },

        mounted () {
            this.emitter.on('size-modal', (args) => {
                this.form = useForm({
                    id: args.size ? args.size.id : null,
                    title: args.size ? args.size.title : ''
                })

                this.show = true
                $('#genericModal').modal('show');
            })
        },

        mixins: [Helpers]
    }
</script>
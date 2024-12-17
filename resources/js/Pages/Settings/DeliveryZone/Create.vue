<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="fv-row mb-4 fv-plugins-icon-container">
                    <Label for="name" class="required" value="Type Name" />
                    <Input id="name" type="text"
                        :class="{'is-invalid border border-danger' : form.errors.platform_delivery_type}" v-model="form.platform_delivery_type" autofocus
                        autocomplete="name" placeholder="Enter departname name" />
                    <error :message="form.errors.platform_delivery_type"></error>
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

    export default {
        components: {
            Modal,
            Input,
            Label,
            Button,
            Error,
            InlineSvg,
        },
        data() {
            return {
                isOpened: false,
                isSaved: false,
                title: null
            }
        },
        methods: {
            submit() {
                this.form.post(route('dashboard.settings.deliveyzone.store'), {
                    errorBag: 'delivery',
                    preserveScroll: true,
                    onSuccess: () => {
                        $('#genericModal').modal('hide')
                    }
                }) 
            },        
        },
        mounted() {
            this.emitter.on('delivery-type-create', (args) => {
                this.form = useForm({
                    id: args.deliveryZone ? args.deliveryZone.id : null,
                    platform_delivery_type: args.deliveryZone ? args.deliveryZone.platform_delivery_type : '',
                });
                this.title = args.deliveryZone ? 'Edit Delivery Type' : 'Create Delivery Type'
                $('#genericModal').modal('show')
            })
        },
        mixins: [Helpers]
    }
</script>

<style scoped>

</style>
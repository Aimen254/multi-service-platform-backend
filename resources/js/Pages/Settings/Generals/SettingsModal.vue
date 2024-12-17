<template>
    <modal :show="show" @close="closeModal()">
        <template #title>
            {{ title }}
        </template>
        <template #content>
            <form @submit.prevent="submit" ref="form">
                <number-format-form v-if="group == 'number_format_settings'" :form="form"></number-format-form>
                <time-format-form v-if="group == 'time_format_settings'" :form="form"></time-format-form>
                <stripe-connect-form v-if="group == 'stripe_connect_settings'" :form="form"></stripe-connect-form>
                <driver-assignment-form v-if="group == 'driver_assignment_settings'" :form="form"></driver-assignment-form>
                <checkout-fields-form v-if="group == 'checkout_fields_settings'" :form="form"></checkout-fields-form>
                <div class="px-6 py-4 bg-gray-100 text-right bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button btnWidth="w-28 ml-auto" class="group relative flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-400 hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 ml-2" @click="closeModal">
                        <span class="ml-2">Cancel</span>
                    </button>
                    <Button v-if="group != 'checkout_fields_settings'" type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"  btnWidth="w-28 ml-auto">
                        <span class="ml-2">Update</span>
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                        </span>
                    </Button>
                </div>
            </form>
        </template>
    </modal>
</template>

<script>
import Modal from '@/Components/Modal.vue'
import Button from '@/Components/Button.vue'
import NumberFormatForm from '../Forms/NumberFormatForm.vue'
import TimeFormatForm from '../Forms/TimeFormatForm.vue'
import StripeConnectForm from '../Forms/StripeConnectForm.vue'
import DriverAssignmentForm from '../Forms/DriverAssignmentForm.vue'
import { useForm } from '@inertiajs/inertia-vue3'

export default {
    components: {
        Modal,
        Error,
        NumberFormatForm,
        TimeFormatForm,
        Button,
        StripeConnectForm,
        DriverAssignmentForm,
    },
    data() {
        return {
            show: false,
            maxWidth: '2xl',
            form: useForm({
                settings: [],
                group: null
            }),
            title: '',
            group: '',
        }
    },
    methods: {
        closeModal() {
            this.show = false;
            this.form.errors = {};
        },
        submit() {
            this.form.post(route('dashboard.settings.general.update'), {
                preserveScroll: true,
                errorBag: 'settings',
                onSuccess: () => this.show = false,
            })
        }
    },
    mounted() {
        this.emitter.on('settings-modal', (args) => {
            this.show = true,
            this.form.settings = args.settings,
            this.form.group = args.group,
            this.title = args.title,
            this.group = args.group
        })
    }
}
</script>
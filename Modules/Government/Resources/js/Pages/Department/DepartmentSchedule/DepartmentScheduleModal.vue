<template>
    <Modal :id="business_schedule_modal_id">
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-6">
                        <Label for="open_at" class="required" value="Open At" />
                        <input type="time" id="open_at" class="form-control" :value="formattedOpenAt"
                            @input="updateTime($event.target.value, 'open_at')" />
                        <error v-if="error && errors.open_at" :message="errors.open_at[0]">
                        </error>
                    </div>
                    <div class="col-md-6">
                        <Label for="close_at" class="required" value="Close At" />
                        <input type="time" id="close_at" class="form-control" :value="formattedCloseAt"
                            @input="updateTime($event.target.value, 'close_at')" />
                        <error v-if="error && errors.close_at" :message="errors.close_at[0]"></error>
                    </div>
                </div>

                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        <span class="indicator-label" v-if="!form.processing">Save</span>
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
import Error from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'
import VueTimepicker from 'vue3-timepicker'
import Helpers from "@/Mixins/Helpers"

// CSS
import 'vue3-timepicker/dist/VueTimepicker.css'

export default {
    props: ['business'],
    components: {
        Modal,
        Label,
        Error,
        Button,
        VueTimepicker,
    },
    data() {
        return {
            title: null,
            show: false,
            maxWidth: '2xl',
            form: null,
            type: null,
            error: false,
            errors: null,
            loading: false,
            businessSchedule: null,
            businessdata: '',
            business_schedule_modal_id: 'genericModal_business',
        }
    },
    methods: {
        closeModal() {
            $('#' + this.business_schedule_modal_id).modal('hide');
            this.error = false;
            this.loading = false;
        },
        submit() {
            this.loading = true;
            if (this.type == 'post') {
                window.axios.post(route('government.dashboard.department.departmentschedules.store', [this.getSelectedModuleValue(), this.business.uuid]), this.form)
                    .then((response) => {
                        this.reset();
                        this.businessSchedule = response.data.schedule;
                        this.$emit('childToParent', this.businessSchedule);
                        this.form = null;
                        this.closeModal();
                        this.$notify({
                            group: "toast",
                            type: 'success',
                            text: response.data.message
                        }, 3000) // 3s
                    })
                    .catch(error => {
                        this.error = true;
                        this.errors = error.response.data.errors;
                        this.$notify({
                            group: "toast",
                            type: 'error',
                            text: error.response.data.message
                        }, 3000) // 3s
                    }).finally(() => {
                        this.loading = false
                    });

            } else {
                window.axios.put(route('government.dashboard.department.departmentschedules.update', [this.getSelectedModuleValue(), this.form.id, this.business.uuid]), this.form)
                    .then((response) => {
                        this.reset();
                        this.businessSchedule = response.data.schedule;
                        this.$emit('childToParent', this.businessSchedule);
                        this.closeModal();
                        this.$notify({
                            group: "toast",
                            type: 'success',
                            text: response.data.message
                        }, 3000) // 3s
                    })
                    .catch(error => {
                        this.error = true;
                        this.errors = error.response.data.errors;
                        this.$notify({
                            group: "toast",
                            type: 'error',
                            text: error.response.data.message
                        }, 3000) // 3s
                    }).finally(() => {
                        this.loading = false
                    });
            }
        },
        reset() {
            this.error = false;
        },

        updateTime(time, type = 'open_at') {
            const [hours, minutes] = time.split(':');
            if (!hours) {
                return;
            }
            const hh = (hours > 12 ? (hours - 12).toString().padStart(2, '0') : hours.padStart(2, '0'));
            const a = hours >= 12 ? 'PM' : 'AM';
            type == 'open_at' ? this.form.open_at = `${hh}:${minutes} ${a}` : this.form.close_at = `${hh}:${minutes} ${a}`
        },
    },

    computed: {
        formattedOpenAt() {
            if (!this.form.open_at) return '';
            const timeParts = this.form.open_at.split(/[:\s]/); // Split by colon and space
            const hh = timeParts[0];
            const mm = timeParts[1];
            const a = timeParts[2];
            const hours = a === 'PM' && hh !== '12' ? (parseInt(hh) + 12).toString().padStart(2, '0') : (a === 'AM' && hh === '12' ? '00' : hh);
            return `${hours}:${mm}`;
        },

        formattedCloseAt() {
            if (!this.form.close_at) return '';
            const timeParts = this.form.close_at.split(/[:\s]/); // Split by colon and space
            const hh = timeParts[0];
            const mm = timeParts[1];
            const a = timeParts[2];
            const hours = a === 'PM' && hh !== '12' ? (parseInt(hh) + 12).toString().padStart(2, '0') : (a === 'AM' && hh === '12' ? '00' : hh);
            return `${hours}:${mm}`;
        }

    },

    mounted() {
        this.emitter.on('business-schedule-model', (args) => {
            this.errors = null
            this.form = useForm({
                id: args.schedule.id ? args.schedule.id : null,
                business_schedule_id: args.schedule.business_schedule_id ? args.schedule.business_schedule_id : args.schedule.id,
                schedule_id: args.schedule.business_schedule_id ? args.schedule.id : '',
                name: args.name ? args.name : null,
                open_at: args.schedule.open_at ? args.schedule.open_at : '',
                close_at: args.schedule.close_at ? args.schedule.close_at : '',
            });
            this.title = args.schedule.business_schedule_id ? 'Edit Schedule' : 'Create Schedule';
            this.type = args.type;
            $('#' + this.business_schedule_modal_id).modal('show');
        })
    },

    mixins: [Helpers],
}
</script>

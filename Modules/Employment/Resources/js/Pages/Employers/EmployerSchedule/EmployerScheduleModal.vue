
<template>
    <Modal :id="business_schedule_modal_id">
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit" >
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-6">
                        <Label for="open_at" class="required" value="Open At" />
                        <vue-timepicker 
                            v-model="form.open_at" format="hh:mm A" :minute-interval="15"></vue-timepicker>
                        <error v-if="error && errors.open_at" :message="errors.open_at[0]">
                        </error>
                    </div>
                    <div class="col-md-6">
                        <Label for="close_at" class="required" value="Close At" />
                        <vue-timepicker
                            v-model="form.close_at" format="hh:mm A" :minute-interval="15"></vue-timepicker>
                        <error v-if="error && errors.close_at" :message="errors.close_at[0]"></error>
                    </div>
                </div>

                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        >
                        <span class="indicator-label" v-if="!form.processing"> {{form.business_schedule_id ? 'Update' : 'Save'}}</span>
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
                title:null,
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
                    window.axios.post(route('employment.dashboard.employers.employerschedule.store', [this.getSelectedModuleValue(), this.business.uuid]), this.form)
                        .then((response) => {
                            this.reset();
                            this.businessSchedule = response.data.schedule;
                            this.$emit('childToParent', this.businessSchedule);
                            this.form=null;
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
                    window.axios.put(route('employment.dashboard.employers.employerschedule.update', [this.getSelectedModuleValue(), this.form.id, this.business.uuid]), this.form)
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
            }
        },
        mounted() {     
            this.emitter.on('business-schedule-model', (args) => {
                this.form = useForm({
                    id: args.schedule.id ? args.schedule.id : null,
                    business_schedule_id: args.schedule.business_schedule_id ? args.schedule.business_schedule_id : args.schedule.id,
                    schedule_id: args.schedule.business_schedule_id ? args.schedule.id : '',
                    name: args.name ? args.name : null,
                    open_at: args.schedule.open_at ? args.schedule.open_at : '',
                    close_at: args.schedule.close_at ? args.schedule.close_at : '',
                });
                this.title = args.schedule.business_schedule_id ? 'Edit Schedule':'Create Schedule';
                this.type = args.type;
                $('#' + this.business_schedule_modal_id).modal('show');
            })
        },
        mixins: [Helpers],
    }
</script>

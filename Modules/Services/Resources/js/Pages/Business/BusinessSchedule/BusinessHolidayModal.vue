
<template>
    <Modal :id=business_schedule_modal_id>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ titleNew }}</h4>
        </template>
        <template #content>
            <form class="mb-4" v-if="form" @submit.prevent="submit">
                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="title" class="required" value="Title" />
                        <Input id="title" type="text" :class="{ 'is-invalid border border-danger': error && errors.title }"
                            v-model="form.title" placeholder="Enter Title" />
                        <error v-if="error && errors.title" :message="errors.title[0]"></error>
                    </div>

                </div>

                <div class="row fv-row mb-4 fv-plugins-icon-container">
                    <div class="col-md-12">
                        <Label for="date" class="required" value="Date" />
                        <Calendar :attributes="attributes" @dayclick="onDayClick" is-expanded />
                        <error v-if="error && errors.date" :message="errors.date[0]"></error>
                    </div>
                </div>

                <div class="text-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        <span class="indicator-label" v-if="!form.processing"> {{ form.id ? 'Update' : 'Save' }}</span>
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
import 'v-calendar/dist/style.css';
import { Calendar, DatePicker } from 'v-calendar';
import Helpers from "@/Mixins/Helpers"


export default {
    props: ['business'],
    components: {
        Modal,
        Label,
        Input,
        Error,
        Button,
        Calendar,
        DatePicker,
    },
    data() {
        return {
            titleNew: null,
            show: false,
            maxWidth: '2xl',
            form: null,
            type: null,
            error: false,
            errors: null,
            loading: false,
            businessHolidays: null,
            days: [],
            myResult: [],
            business_schedule_modal_id: 'genericModal_hiloday',
        }
    },
    computed: {
        dates() {
            return this.days.map(day => day.date);
        },
        attributes() {
            return this.dates.map(date => ({
                highlight: true,
                dates: date,
            }));
        },
    },
    methods: {
        onDayClick(day) {
            const index = this.days.findIndex(d => d.id === day.id);
            if (index >= 0) {
                this.days.splice(index, 1);
            } else {
                this.days.push({
                    id: day.id,
                    date: day.id,
                });
            }
        },
        dateIntoArray(dateValues) {
            this.myResult = dateValues.split(",");
            var arrayDate = [];
            this.myResult.forEach(function (number) {
                arrayDate.push({
                    id: number,
                    date: number
                })
            });
            return arrayDate;
        },
        closeModal() {
            $('#' + this.business_schedule_modal_id).modal('hide');
            this.error = false;
        },
        submit() {
            this.form.date = this.days;
            this.loading = true;
            if (this.type == 'post') {
                window.axios.post(route('services.dashboard.service-provider.businessholidays.store', [this.getSelectedModuleValue(), this.business.uuid]), this.form)
                    .then((response) => {
                        this.reset();
                        this.businessHolidays = response.data.businessHolidays;
                        this.$emit('childToParentHolidayModel', this.businessHolidays);
                        this.closeModal();
                        // show toast message
                        this.$notify({
                            group: "toast",
                            type: 'success',
                            text: response.data.message
                        }, 3000) // 3s
                    }).catch(error => {
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
                this.form.date = this.days;
                window.axios.put(route('services.dashboard.service-provider.businessholidays.update', [this.getSelectedModuleValue(), this.form.id, this.business.uuid]), this.form)
                    .then((response) => {
                        this.reset();
                        this.businessHolidays = response.data.businessHolidays;
                        this.$emit('childToParentHolidayModel', this.businessHolidays);
                        this.closeModal();

                        // show toast message
                        this.$notify({
                            group: "toast",
                            type: 'success',
                            text: response.data.message
                        }, 3000) // 3s
                    }).catch(error => {
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
        this.emitter.on('business-holiday-modal', (args) => {
            this.form = useForm({
                id: args.holiday.id ? args.holiday.id : null,
                business_id: this.business ? this.business.id : null,
                title: args.holiday.title ? args.holiday.title : null,
                date: null,
            })
            this.type = args.type;
            this.days = [];
            if (this.type == 'put' && args.holiday.date) {
                this.dateArray = this.dateIntoArray(args.holiday.date);
                this.days = this.dateArray ? this.dateArray : null;
            }
            this.titleNew = args.holiday.id ? 'Edit Schedule' : 'Create Schedule';
            $('#' + this.business_schedule_modal_id).modal('show');
        })
    },
    mixins: [Helpers],
}
</script>

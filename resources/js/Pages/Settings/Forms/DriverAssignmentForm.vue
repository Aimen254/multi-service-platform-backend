<template>
    <div class="px-6 py-4">
        <div v-for="(setting, index) in this.settings" :key="index">
            <div v-if="setting.key == 'autoassign_type'">
                <div class="px-6 py-4">
                    <div class="row">
                        <Label :class="`col-lg-4 col-form-label required fw-bold fs-6`" :value="setting.name" />
                        <div class="col-lg-8 fv-row">
                            <select v-model="setting.value" class="form-select text-capitalize form-select-solid">
                                <option class="text-capitalize">basic</option>
                                <option class="text-capitalize">Enterprise</option>
                            </select>
                            <error :message="form.errors[`settings.${index}.value`]"></error>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="setting.key == 'max_order_assignments'" class="mt-3">
                <div class="px-6 py-4">
                    <div class="row">
                        <Label :class="`col-lg-4 col-form-label required fw-bold fs-6`" :value="setting.name" />
                        <div class="col-lg-8 fv-row">
                            <Input type="text"
                                :class="[form.errors[`settings.${index}.value`] ? 'border border-danger' : '', 'mt-1']"
                                v-model="setting.value" />
                            <error :message="form.errors[`settings.${index}.value`]"></error>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="setting.key == 'enable_auto_assignment'">
                <div class="px-6 py-4">
                    <div class="row">
                        <Label :class="`col-lg-4 col-form-label required fw-bold fs-6`" :value="setting.name" />
                        <div class="col-lg-8 fv-row">
                            <select v-model="setting.value" class="form-select text-capitalize form-select-solid">
                                <option class="text-capitalize">yes</option>
                                <option class="text-capitalize">no</option>
                            </select>
                            <error :message="form.errors[`settings.${index}.value`]"></error>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="setting.key == 'auto_assignment_max_distance'" class="mt-3">
                <div class="px-6 py-4">
                    <div class="row">
                        <Label :class="`col-lg-4 col-form-label required fw-bold fs-6`" :value="setting.name" />
                        <div class="col-lg-8 fv-row">
                            <Input type="text"
                                :class="[form.errors[`settings.${index}.value`] ? 'border border-danger' : '', 'mt-1']"
                                v-model="setting.value" />
                            <error :message="form.errors[`settings.${index}.value`]"></error>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="setting.key == 'start_process_in_status'">
                <div class="px-6 py-4">
                    <div class="row">
                        <Label :class="`col-lg-4 col-form-label required fw-bold fs-6`" :value="setting.name" />
                        <div class="col-lg-8 fv-row">
                            <select v-model="setting.value" class="form-select text-capitalize form-select-solid">
                                <option class="text-capitalize">ready for pickup</option>
                                <option class="text-capitalize">accepted by bussiness</option>
                            </select>
                            <error :message="form.errors[`settings.${index}.value`]"></error>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import Error from '@/Components/InputError.vue'
export default {
    props: ['form'],
    components: {
        Input,
        Label,
        Error
    },
    data() {
        return {
            title: '',
            settings: []
        }
    },
    watch: {
        form: {
            handler(val) {
                this.settings = val ? val.settings : null
            },
            deep: true
        }
    },
    mounted() {
        this.settings = this.form ? this.form.settings : null
    }
}
</script>
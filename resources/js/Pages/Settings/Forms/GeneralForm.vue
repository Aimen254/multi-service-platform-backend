<template>
    <div class="card mb-5 mb-xl-10">
        <!--begin::Content-->
        <div id="kt_account_profile_details" class="collapse show">
            <div class="px-9 py-4" v-for="(setting, index) in this.settings" :key="index">
                <div v-if="setting.key == 'enalble_push_notifications'">
                    <div class="row">
                        <Label :class="`col-lg-4 col-form-label fw-bold fs-6`" :value="setting.name" />
                        <div class="col-lg-8 fv-row">
                            <label class="form-check form-check-custom form-check-solid align-items-start mt-3">
                                <input :id="index" :value="setting.value" v-model="setting.value"
                                    :checked="setting.value" class="form-check-input me-3" type="checkbox" />
                            </label>
                        </div>
                    </div>
                </div>
                <div v-else-if="setting.key == 'firebase_config_file'">
                    <div class="row">
                        <Label :class="`col-lg-4 col-form-label fw-bold fs-6`" :value="setting.name" />
                        <div class="col-lg-8 fv-row">
                            <Input type="file"
                                :class="[form.errors[`settings.${index}.value`] ? 'border border-danger' : '', 'mt-1']"
                                @input="setting.value = $event.target.files[0]" />
                            <error :message="form.errors[`settings.${index}.value`]"></error>
                        </div>
                    </div>
                </div>
                <div v-else-if="setting.key != 'enalble_push_notifications'">
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
        </div>
        <!--end::Content-->
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
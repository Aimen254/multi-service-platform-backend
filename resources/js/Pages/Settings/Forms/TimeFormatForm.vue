<template>
    <div class="px-6 py-4">
        <div v-for="(setting, index) in settings" :key="index">
            <div v-if="setting.key == 'time_format'">
                <div class="px-6 py-4">
                    <div class="row">
                        <Label :class="`col-lg-4 col-form-label required fw-bold fs-6`" :value="setting.name" />
                        <div class="col-lg-8 fv-row">
                            <select v-model="setting.value" class="form-select text-capitalize form-select-solid">
                                <option class="text-capitalize">12 hours</option>
                                <option class="text-capitalize">24 hours</option>
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
import Label from '@/Components/Label.vue'
export default {
    props: ['form'],
    components: {
        Label,
    },
    data() {
        return {
            title: '',
            settings: [],
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
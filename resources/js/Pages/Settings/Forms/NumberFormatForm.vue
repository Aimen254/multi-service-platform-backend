<template>
    <div class="px-6 py-4">
        <div v-for="(setting, index) in this.settings" :key="index">
            <div v-if="setting.key == 'decimal_length'">
                <div class="px-6 py-4">
                    <div class="row">
                        <Label :class="`col-lg-4 col-form-label required fw-bold fs-6`" :value="setting.name" />
                        <div class="col-lg-8 fv-row">
                            <select v-model="setting.value" class="form-select form-select-solid">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                            <error :message="form.errors[`settings.${index}.value`]"></error>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="setting.key == 'decimal_separator'">
                <div class="px-6 py-4">
                    <div class="row">
                        <Label :class="`col-lg-4 col-form-label required fw-bold fs-6`" :value="setting.name" />
                        <div class="col-lg-8 fv-row">
                            <select v-model="setting.value" class="form-select form-select-solid">
                                <option>.</option>
                                <option>,</option>
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
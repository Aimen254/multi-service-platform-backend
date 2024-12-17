<template>
    <div class="px-6 py-4">
        <div v-for="(setting, index) in this.settings" :key="index">
            <div v-if="setting.key == 'sandbox'">
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
            <div v-if="checkSandbox">
                <div v-if="setting.key == 'client_id_sandbox'" class="mt-3">
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
            </div>
            <div v-if="checkSandbox">
                <div v-if="setting.key == 'client_secret_sandbox'" class="mt-3">
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
            </div>
            <div v-if="checkSandbox">
                <div v-if="setting.key == 'stripe_webhook_secret_sandbox'" class="mt-3">
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
            </div>

            <div v-if="!checkSandbox">
                <div v-if="setting.key == 'client_id_production'" class="mt-3">
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
            </div>
            <div v-if="!checkSandbox">
                <div v-if="setting.key == 'client_secret_production'" class="mt-3">
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
            </div>
            <div v-if="!checkSandbox">
                <div v-if="setting.key == 'stripe_webhook_secret_production'" class="mt-3">
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
            settings: [],
            checkSandbox: false
        }
    },
    watch: {
        form: {
            handler(val) {
                this.settings = val ? val.settings : null
                this.checkSandBox()
            },
            deep: true
        },
        // checkSandbox() {
        //     this.checkSandBox()
        // }
    },
    mounted() {
        this.settings = this.form ? this.form.settings : null
    },
    methods: {
        checkSandBox() {
            Object.values(this.settings).forEach((val,key) => {
                if (val.key == "sandbox" && val.value == "yes") {
                    this.checkSandbox = true
                } 
                if (val.key == "sandbox" && val.value == "no") {
                    this.checkSandbox = false
                }
            })
        },
    }

}
</script>
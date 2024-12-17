<template>
    <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid w-35px"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="change status">
        <!--begin::Input-->
        <input :class="[value == 1 ? '' : 'checkbox-input', 'form-check-input']" type="checkbox" :value="value" v-model="value" :checked="value">
        <span class="form-check-label fw-bold text-muted"></span>
    </label>
</template>
<script>
    export default {
        props: ['status', 'outerToggleClass', 'innerToggleClass', 'translate'],
        data() {
            return {
                value: this.status,
                translate_class: 'translate-x-3'
            }
        },
        watch: {
            status(val) {
                this.value = val
            }
        },
        methods: {
            toggle() {
                this.value = !this.value;
            }
        },
        mounted() {
            this.translate_class = this.translate ? this.translate : 'translate-x-3'
        }
    }
</script>

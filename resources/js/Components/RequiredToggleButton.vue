<template>
    <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid w-35px">
        <!--begin::Input-->
        <input :class="[value == 1 ? '' : 'checkbox-input', 'form-check-input']" type="checkbox" :value="value" v-model="value" :checked="value">
        <span class="form-check-label fw-bold text-muted"></span>
    </label>
</template>
<script>
    export default {
        props: ['required'],
        data () {
            return {
                value: this.required
            }
        },
        watch: {
            required(val) {
                this.value = val
            }
        },
        methods: {
            toggle() {
                this.value = !this.value;
            }
        }
    }
</script>

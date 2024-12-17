<template>
    <input :value="type.value" v-model="type.value" :checked="type.value" class="form-check-input me-3"
        type="checkbox" />
</template>

<script>
export default {
    props: ['type'],
}
</script>

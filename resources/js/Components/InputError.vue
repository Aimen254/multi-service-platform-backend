<template>
    <div v-show="message">
        <p class="invalid-feedback d-block">
            {{ message }}
        </p>
    </div>
</template>

<script>
export default {
    props: ['message'],
}
</script>

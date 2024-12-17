<template>
    <div class="btn btn-active-color-danger btn-sm me-1" :class="{ 'btn-icon btn-bg-light': !text }"
        @click="onDelete"
        v-if="checkUserPermissions(permission)"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="delete">
        <span class="svg-icon svg-icon-3">
            <inline-svg :src="'/images/icons/delete.svg'"/><span v-if="text">{{ text }}</span>
        </span>
    </div>
</template>

<script>
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'

    export default {
        props: ['url', 'permission', 'text', 'currentCount', 'currentPage'],

        components: {
            InlineSvg
        },

        methods: {
            onDelete () {
                this.swal.fire({
                    title: "",
                    html: "<h1 class='text-lg text-gray-800 mb-1'>Delete Record</h1><p class='text-base'>Are you sure want to delete this record?</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "Delete Record",
                    customClass: {
                    confirmButton: 'danger'
                    }
                }).then((result) => {
                    if (result.value) {
                        const urlWithParams = `${this.url}?page=${this.currentPage}&currentCount=${this.currentCount}`;
                        showWaitDialog()
                        this.$inertia.delete(urlWithParams, {
                            preserveScroll: false,
                            onSuccess: () => {
                                hideWaitDialog();
                                // Emit the event on successful deletion
                                localStorage.setItem('selectedBusiness', '');
                                this.emitter.emit('business-deleted');
                            },
                            onError: () => {
                                hideWaitDialog();
                            }
                        })
                    }
                })
            }
        },
        mixins: [Helpers]
    }
</script>

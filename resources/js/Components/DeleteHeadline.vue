<template>
    <div class="btn btn-active-color-danger btn-sm ms-1" :class="{ 'btn-icon btn-bg-light': !text }" @click="onDelete"
        v-if="checkUserPermissions(permission)" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="Remove Headline">
        <span class="svg-icon svg-icon-3 cursor-pointer">
            <i class="fas fa-eraser"></i><span v-if="text">{{ text }}</span>
        </span>
    </div>
</template>

<script>
import Helpers from '@/Mixins/Helpers'
import InlineSvg from 'vue-inline-svg'
export default {
    props: ['url', 'permission', 'message', 'text'],
    components: {
        InlineSvg
    },
    methods: {
        onDelete() {
            this.swal.fire({
                title: "",
                html: `<h1 class='text-lg text-gray-800 mb-1'>Delete Record</h1><p class='text-base'>Are you sure want to delete this ${this.message} headline?</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: `Delete ${this.message} Headline`,
                customClass: {
                    confirmButton: 'danger'
                }
            }).then((result) => {
                if (result.value) {
                    showWaitDialog()
                    this.$inertia.delete(this.url, {
                        preserveScroll: false,
                        onSuccess: () => hideWaitDialog()
                    })
                }
            })
        }
    },
    mixins: [Helpers]
}
</script>

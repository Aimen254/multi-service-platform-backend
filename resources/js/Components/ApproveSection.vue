<template>
    <div class="btn btn-active-color-primary btn-sm me-1" :class="{ 'btn-icon btn-bg-light': !text }"
        @click="onApprove"
        v-if="checkUserPermissions(permission)"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="approve">
        <span class="svg-icon svg-icon-3">
            <inline-svg :src="'/images/icons/thumb_up.svg'"/><span v-if="text">{{ text }}</span>
        </span>
    </div>
</template>

<script>
    import Helpers from '@/Mixins/Helpers'
    import InlineSvg from 'vue-inline-svg'

    export default {
        props: ['url', 'permission', 'text'],

        components: {
            InlineSvg
        },

        methods: {
            onApprove () {
                this.swal.fire({
                    title: "",
                    html: "<h1 class='text-lg text-gray-800 mb-1'>Approve Broker</h1><p class='text-base'>Are you sure want to Approve this Broker?</p>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "Approve Broker",
                    customClass: {
                    confirmButton: 'danger'
                    }
                }).then((result) => {
                    if (result.value) {
                        console.log(this.text);
                        showWaitDialog()
                        this.$inertia.visit(this.url, {
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

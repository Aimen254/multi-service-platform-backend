
<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <div>
                <div class="row gx-5 gx-xl-10" v-if="comment">
                    <div class="col-12 mb-5 mb-xl-10">
                        <!--begin::Chart widget 8-->
                        <div class="card card-flush h-xl-100">
                            <!--begin::Body-->
                            <div class="card-body pt-6">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="fw-normal fw-bold text-muted d-block fs-7">{{ comment.comment }}</p>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div class="image-input-wrapper w-40px h-40px"
                                                    style="border-radius: 50% !important;"
                                                    :style="{ 'background-image': 'url(' + getImage(comment.user.avatar) + ')' }">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span class="
                                                    text-dark
                                                    text-hover-primary
                                                    mb-1
                                                    fs-8
                                                ">
                                                    {{ comment.user.first_name }} {{ comment.user.last_name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <span class="
                                                    text-dark
                                                    text-hover-primary
                                                    mb-1
                                                    p-3
                                                    fs-8
                                                ">
                                                {{ formatDateTime(comment.created_at) }}
                                            </span>
                                        </div>
                                        <p></p>
                                    </div>
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Chart widget 8-->
                    </div>

                </div>
            </div>
        </template>
    </Modal>
</template>

<script>
import Modal from "@/Components/Modal.vue";
import Helpers from "@/Mixins/Helpers";


export default {
    components: {
        Modal
    },

    data() {
        return {
            title: 'Comment Detail',
            comment: null
        };
    },

    methods: {

    },

    mounted() {
        this.emitter.on("comment-modal", (args) => {
            this.comment = args.comment;
            console.log(this.comment);
            $("#genericModal").modal("show");
        });
    },
    mixins: [Helpers],
};
</script>

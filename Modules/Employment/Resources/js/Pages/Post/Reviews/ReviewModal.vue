<template>
    <Modal>
        <template #header>
            <h4 id="bsModalLabel" class="modal-title">{{ title }}</h4>
        </template>
        <template #content>
            <div>
                <div class="row gx-5 gx-xl-10" v-if="review">
                    <div class="col-xl-12 mb-5 mb-xl-10">
                        <!--begin::Chart widget 8-->
                        <div class="card card-flush h-xl-100">
                            <!--begin::Body-->
                            <div class="card-body pt-6">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="fw-normal fw-bold text-muted d-block fs-7">
                                            <shapla-star-rating v-model="review.rating" :is-static="true" :active-color="['#ffad0f']" :color="['#ffad0f']"/>
                                        </p>
                                    </div>
                                    <div class="col-12">
                                        <p class="fw-normal fw-bold text-muted d-block fs-7">{{ review.comment }}</p>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="image-input image-input-empty">
                                                <div class="image-input-wrapper w-40px h-40px" style="border-radius: 50% !important;"
                                                    :style="{ 'background-image': 'url(' + getImage(review.user.avatar) + ')' }">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-start flex-column mx-2">
                                                <span class="
                                                    text-dark
                                                    text-hover-primary
                                                    mb-1
                                                    fs-8
                                                ">
                                                {{review.user.first_name }} {{ review.user.last_name }}
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
                                                {{ formatDate(review.created_at) }}
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
import ShaplaStarRating from '@shapla/vue-star-rating';
import '@shapla/vue-star-rating/dist/style.css';
export default {
    components: {
        Modal,
        ShaplaStarRating,
    },
    data() {
        return {
            title: 'Review Detail',
            review: null
        };
    },
    methods: {

    },
    mounted() {
        this.emitter.on("employment-post-review-modal", (args) => {
            this.review = args.review;
            $("#genericModal").modal("show");
        });
    },
    mixins: [Helpers],
};
</script>

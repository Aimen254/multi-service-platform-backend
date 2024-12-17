<template>
    <div>
        <!--begin::Root-->
        <div class="d-flex flex-column flex-root">
            <!--begin::Page-->
            <div class="page d-flex flex-row flex-column-fluid">
                <!-- sidebar -->
                <!--begin::Aside-->
                <div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true"
                    data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}"
                    data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}"
                    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
                    <Sidebar />
                </div>
                <!--end::Aside-->
                <!--begin::Wrapper-->
                <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                    <Header />
                    <stripe-alert v-if="module == 'retail'"></stripe-alert>
                    <Toast></Toast>
                    <!--begin::Content-->
                    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                        <!--begin::Toolbar-->
                        <div class="toolbar" id="kt_toolbar">
                            <!--begin::Container-->
                            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                                <slot name="breadcrumbs" />
                            </div>
                            <!--end::Container-->
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Post-->
                        <div class="post d-flex flex-column-fluid" id="kt_post">
                            <!--begin::Container-->
                            <div id="kt_content_container" class="container-xxl">
                                <div v-show="this.$page.props.flash">
                                    <banner></banner>
                                </div>
                                <slot />
                            </div>
                            <!--end::Container-->
                        </div>
                        <!--end::Post-->
                    </div>
                    <!--end::Content-->
                    <div class="footer py-6 d-flex flex-lg-column" id="kt_footer">
                        <!--begin::Container-->
                        <div
                            class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <!--begin::Copyright-->
                            <div class="text-dark order-2 order-md-1">
                                <span class="text-muted fw-bold me-1">2022©</span>
                                <span href="#" class="text-muted">Powered By Interapptive</span>
                            </div>
                            <!--end::Copyright-->
                        </div>
                        <!--end::Container-->
                    </div>
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Page-->
        </div>
        <!--end::Root-->
        <!--begin::Drawers-->

        <!--end::Drawers-->
        <!--end::Main-->
        <!--begin::Engage drawers-->

        <!--end::Engage drawers-->
        <!--begin::Scrolltop-->
        <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
            <span class="svg-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)"
                        fill="black" />
                    <path
                        d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                        fill="black" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Scrolltop-->
    </div>
</template>

<script>
import Header from '@/Components/Header.vue'
import Sidebar from '@/Components/Sidebar.vue'
import StripeAlert from '@/Components/StripeAlert.vue'
import Banner from '@/Components/Banner.vue'
import Toast from '@/Components/ToastMessage.vue'
import {
    Link
} from '@inertiajs/inertia-vue3';
import Helpers from '@/Mixins/Helpers'
export default {
    components: {
        Link,
        Header,
        Sidebar,
        Banner,
        Toast,
        StripeAlert,
    },
    data() {
        return {
            module: this.getSelectedModuleName()
        }
    },
    mounted() {
        if (window.KTMenu) {
            window.KTScrolltop.init()
        }
        this.showTooltip()
        this.emitter.on('stripe-connect', () => {
            this.module = this.getSelectedModuleName()
        });
    },
    mixins: [Helpers]
}
</script>

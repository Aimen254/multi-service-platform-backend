<template>
    <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
        data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
        class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
        <!--begin::Title-->
        <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1 text-capitalize">{{title}}</h1>
        <!--end::Title-->
        <!--begin::Separator-->
        <span class="h-20px border-gray-300 border-start mx-4"></span>
        <!--end::Separator-->
        <!--begin::Breadcrumb-->
        <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
            <!--begin::Item-->
            <li class="breadcrumb-item text-muted">
                <a :href="route('dashboard.panel', getSelectedModuleValue())" class="text-muted text-hover-primary">Dashboard</a>
            </li>
            <!--end::Item-->
            <!--begin::Item-->
            <li class="breadcrumb-item">
                <span class="bullet bg-gray-300 w-5px h-2px"></span>
            </li>
             <!--begin::Item-->
             
            <!--end::Item-->
            <!--begin::Item-->
            <li v-if="path" class="breadcrumb-item text-muted text-capitalize">{{path}}</li>
            <!--end::Item-->
            <!--begin::Item-->
            <li v-if="path" class="breadcrumb-item">
                <span class="bullet bg-gray-300 w-5px h-2px"></span>
            </li>
            <!--end::Item-->
            <li v-if="sub_path" class="breadcrumb-item text-muted text-capitalize">{{sub_path}}</li>
            <!--end::Item-->
            <!--begin::Item-->
            <li v-if="sub_path" class="breadcrumb-item">
                <span class="bullet bg-gray-300 w-5px h-2px"></span>
            </li>
            <!--begin::Item-->
            <li v-if="title" class="breadcrumb-item text-dark text-capitalize">{{title}}</li>
            <!--end::Item-->
        </ul>
        <!--end::Breadcrumb-->
    </div>
</template>

<script>
import Helpers from '@/Mixins/Helpers'

export default {
    props: ['title', 'path', 'sub_path'],
    components: {
        Helpers
    },
    mixins: [Helpers]
}
</script>
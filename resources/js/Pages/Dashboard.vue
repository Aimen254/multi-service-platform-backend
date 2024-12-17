<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #breadcrumbs>
            <h3>Dashboard</h3>
        </template>
        <DeliveryZoneAlert :deliveryZoneErrors="deliveryZoneErrors"/>
        <div class="row">
            <div class="col-xl-3" v-for="user in totalUsers" :key="user.id">
                <div class="card h-100">
                    <div class="card-header flex-nowrap border-0 pt-9">
                        <div class="card-title m-0">
                            <div class="symbol me-5">
                                <i class="fa fa-users display-6"></i>
                            </div>
                            <a href="#" class="fs-4 fw-bold text-hover-primary text-capitalize text-gray-600 m-0">Total {{ getRoleName(user.name)+'s' }}</a>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column px-9 pt-6 pb-8">
                        <div class="fs-2tx fw-bolder mb-3 text-black-50">{{user.count}}</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
    import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
    import {
        Head
    } from '@inertiajs/inertia-vue3';
    import Header from '@/Components/Header.vue';
    import Helpers from '@/Mixins/Helpers'
    import DeliveryZoneAlert from '@/Components/DeliveryZoneAlert.vue';
    export default {
        props: ['totalUsers', 'deliveryZoneErrors'],
        components: {
            AuthenticatedLayout,
            Head,
            Header,
            DeliveryZoneAlert
        },
        mounted(){
            
        },
        mixins: [Helpers]
    }
</script>

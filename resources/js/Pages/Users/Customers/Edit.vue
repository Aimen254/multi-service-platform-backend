<template>
    <Head title="Edit Customer" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Edit Customer`" :path="`Users - Customers`" />
        </template>
        <div>
            <j-form :user="customer" :type="type"  :mediaAvatarSizes="mediaSizes"></j-form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import JForm from '../Form.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

export default {
    components: {
        Head,
        AuthenticatedLayout,
        JForm,
        Breadcrumbs
    },
    props: ['customer', 'mediaSizes'],
    data() {
        return {
            type: 'customer'
        }
    }
}
</script>
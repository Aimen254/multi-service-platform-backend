<template>

    <Head title="Plans" />
    <AuthenticatedLayout>
        <template #breadcrumbs>
            <Breadcrumbs :title="`Plans`" :path="`Subscriptions`" />
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link :href="route('retail.dashboard.subscription.plan.create', [getSelectedModuleValue()])" v-if="checkUserPermissions('add_subscription_plan')" class="btn btn-sm btn-primary">
                    <span class="svg-icon svg-icon-2">
                        <inline-svg :src="'/images/icons/add.svg'" />
                    </span>
                    Add Plan
                </Link>
            </div>
        </template>
        <div class="card">
            <div class="card-body py-3 pt-10">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder border-0 text-muted bg-light">
                                <th class="ps-4 min-w-50px rounded-start">Name</th>
                                <th class="min-w-120px">Price</th>
                                <th class="min-w-120px">Interval</th>
                                <th class="min-w-120px">Status</th>
                                <th class="min-w-120px rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody v-if="Subscriptions?.length > 0">
                            <template v-for="(subscription, index) in Subscriptions" :key="index">
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex justify-content-start flex-column"><span
                                                class="text-dark fw-bolder text-hover-primary mb-1 fs-6">{{ ellipsis(subscription.product.name) }}</span><span
                                                class="text-muted d-block fs-6"><small class="capitalize">Type : {{subscription.product.type}}</small></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary text-capitalize"> ${{ subscription.price.unit_amount/100 }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            {{ subscription.price.recurring.interval }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-bold text-muted d-block fs-7 text-capitalize">
                                            {{subscription.product.active ? 'Active' : 'Inactive'}}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <edit-section
                                                permission="edit_subscription_plan"
                                                :url="route('retail.dashboard.subscription.plan.edit', [getSelectedModuleValue(),subscription.product.id])"
                                                :iconType="'link'"/>
                                            <delete-section 
                                                permission="delete_subscription_plan"
                                                :url="route('retail.dashboard.subscription.plan.destroy', [getSelectedModuleValue(), subscription.product.id])" 
                                                :currentPage="Subscriptions.current_page" :currentCount="Subscriptions.length"/>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <div v-else class="p-4 text-muted">
                            Record Not Found
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { Head , Link} from '@inertiajs/inertia-vue3'
import Helpers from '@/Mixins/Helpers'
import InlineSvg from 'vue-inline-svg'
import Toggle from '@/Components/ToggleButton.vue'
import SearchInput from '@/Components/SearchInput.vue'
import Pagination from '@/Components/Pagination.vue'
import EditSection from "@/Components/EditSection.vue"
import DeleteSection from "@/Components/DeleteSection.vue"
import moment from 'moment'

export default {
    props: ['subscriptions'],

    components: {
        AuthenticatedLayout,
        Breadcrumbs,
        Head,
        InlineSvg,
        Toggle,
        SearchInput,
        Pagination,
        EditSection,
        DeleteSection,
        Link
    },

    data() {
        return {
            Subscriptions: this.subscriptions,
            type: 'plan',
            showModal: false,
        }
    },

    watch: {
        subscriptions: {
            handler(subscriptions) {
                this.Subscriptions = subscriptions
            },
            deep: true
        }
    },
    mixins: [Helpers]
}
</script>
<template>
    <div class="d-flex flex-column gap-7 gap-lg-10 bg-white me-lg-10"
        :class="width" :style="!width ? 'width: 25%!important' : ''">
        <div class="d-flex overflow-auto justify-content-center px-5">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-column">
                <li class="nav-item mt-2">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('automotive.dashboard.dealership.admin-settings', [getSelectedModuleValue(), getSelectedBusinessValue()])"
                        :class="{'active' : route().current('automotive.dashboard.dealership.admin-settings')}">
                        <i class="fab fa-atlassian px-2 fs-2"></i> Assign tags 
                    </Link>
                </li>
                <li class="nav-item mt-2">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('automotive.dashboard.dealership.chat-settings', [getSelectedModuleValue(), getSelectedBusinessValue()])"
                        :class="{'active' : route().current('automotive.dashboard.dealership.chat-settings')}">
                        <i class="fas fa-cog px-2 fs-2"></i>Chat Settings
                    </Link>
                </li>
                <!-- <li class="nav-item mt-2">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('retail.dashboard.business.admin.settings', [getSelectedModuleValue(), getSelectedBusinessValue()])"
                        :class="{'active' : route().current('retail.dashboard.business.admin.settings')}">
                        <i class="fas fa-cog px-2 fs-2"></i> Settings
                    </Link>
                </li>
                <li class="nav-item mt-2" v-if="deliveryType == 'Platform delivery'">
                    <Link class="nav-link text-active-primary ms-0 py-5"
                        :href="route('retail.dashboard.business.admin.assignFeeType', [getSelectedModuleValue(), getSelectedBusinessValue()])"
                        :class="{'active' : route().current('retail.dashboard.business.admin.assignFeeType')}">
                        <i class="fas fa-money-bill-alt px-2 fs-2"></i> Platform Delivery
                    </Link>
                </li> -->
            </ul>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Helpers from '@/Mixins/Helpers';

export default {
    props: ['width', 'type', 'deliveryType'],
    components: {
        Link
    },
    mixins: [Helpers]
}
</script>